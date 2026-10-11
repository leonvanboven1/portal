<?php

use App\Models\EducationCourse;
use App\Models\EducationEnrollment;
use App\Models\CompanyType;
use App\Models\Permission;
use App\Models\PortalFeature;
use App\Models\Role;
use App\Models\User;
use App\Services\Plugin\BankingService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Fakes\FakeBankingService;

const EDUCATION_PLAYER_UUID = '66666666-ffff-4fff-8fff-666666666666';
const EDUCATION_BASE = 'http://portaal.test';

beforeEach(function () {
    Http::fake();

    $this->bank = new FakeBankingService();
    $this->bank->addAccount('education-player', EDUCATION_PLAYER_UUID, 250);
    $this->bank->addAccount('education-town', '77777777-aaaa-4aaa-8aaa-777777777777', 0, 'GOVERNMENT');
    $this->app->instance(BankingService::class, $this->bank);

    PortalFeature::whereIn('key', ['education', 'companies'])->update(['is_enabled' => true]);
    \App\Support\Features::forget();

    $this->player = educationFlowUser();
});

function educationFlowUser(string $uuid = EDUCATION_PLAYER_UUID): User
{
    return User::create([
        'name' => 'Leerling',
        'email' => Str::random(8).'@example.com',
        'minecraft_username' => 'Leerling'.Str::random(4),
        'password' => Hash::make('geheim123'),
        'token' => Str::random(32),
        'minecraft_verified' => true,
        'minecraft_uuid' => str_replace('-', '', $uuid),
        'minecraft_plain_uuid' => $uuid,
    ]);
}

function educationCourse(array $attributes = []): EducationCourse
{
    return EducationCourse::create($attributes + [
        'title' => 'Startopleiding',
        'slug' => Str::random(8),
        'description' => 'De eerste stap.',
        'price' => 0,
        'duration_hours' => 2,
        'is_active' => true,
    ]);
}

it('shows a roadmap and enforces its prerequisite, payment, duration, and company qualification', function () {
    $first = educationCourse([
        'title' => 'Ondernemersbasis',
        'price' => 50,
        'payout_bank_account_uuid' => 'education-town',
        'qualifies_for_company' => true,
    ]);
    $next = educationCourse(['title' => 'Ondernemen in de praktijk']);
    $next->prerequisites()->attach($first);
    $companyType = CompanyType::create([
        'name' => 'Winkel',
        'description' => 'Een winkel.',
        'price' => 0,
        'is_active' => true,
        'authorized_roles' => [],
        'form_fields' => [],
    ]);

    $this->actingAs($this->player)
        ->get(EDUCATION_BASE.'/opleidingen')
        ->assertOk()
        ->assertSee('Ondernemersbasis')
        ->assertSee('Ondernemen in de praktijk')
        ->assertSee('Eerst afronden');

    $this->post(EDUCATION_BASE."/opleidingen/{$next->id}/start", ['payer_bank_account_uuid' => 'education-player'])
        ->assertSessionHas('error');
    expect($this->bank->withdrawals())->toBe([]);

    $this->post(EDUCATION_BASE."/opleidingen/{$first->id}/start", ['payer_bank_account_uuid' => 'education-player'])
        ->assertSessionHas('success');

    $enrollment = EducationEnrollment::sole();
    expect($enrollment->status)->toBe('in_progress')
        ->and($this->bank->balance('education-player'))->toBe(200.0)
        ->and($this->bank->balance('education-town'))->toBe(50.0);

    $this->get(EDUCATION_BASE.'/portal/companies/register')->assertRedirect(EDUCATION_BASE.'/opleidingen');
    $this->post(EDUCATION_BASE."/portal/companies/{$companyType->id}/request", [])
        ->assertRedirect(EDUCATION_BASE.'/opleidingen');

    $this->travelTo(now()->addHour());
    $this->get(EDUCATION_BASE.'/opleidingen')->assertSee('Bezig');
    expect($enrollment->fresh()->status)->toBe('in_progress');

    $this->travelTo(now()->addHour());
    $this->get(EDUCATION_BASE.'/opleidingen')->assertSee('Afgerond');
    expect($enrollment->fresh()->status)->toBe('completed');

    $this->get(EDUCATION_BASE.'/portal/companies/register')->assertOk();
    $this->post(EDUCATION_BASE."/portal/companies/{$companyType->id}/request", [])
        ->assertSessionHasErrors(['form_data.Bedrijfsnaam', 'bank_account_uuid']);
    $this->post(EDUCATION_BASE."/opleidingen/{$next->id}/start", ['payer_bank_account_uuid' => 'education-player'])
        ->assertSessionHas('success');
    expect(EducationEnrollment::count())->toBe(2);
});

it('allows only the education manager permission into course administration', function () {
    $role = Role::create(['name' => 'Opleidingsbeheer', 'slug' => 'education-manager']);
    $role->permissions()->attach(Permission::where('slug', 'manage-education')->value('id'));
    $manager = educationFlowUser((string) Str::uuid());
    $manager->roles()->syncWithoutDetaching([$role->id]);

    $this->actingAs($manager)->get(EDUCATION_BASE.'/portal/admin/opleidingen')->assertOk();
    $this->get(EDUCATION_BASE.'/portal/admin/opleidingen/aanmaken')
        ->assertOk()
        ->assertSee('education-town')
        ->assertDontSee('education-player');
    $this->actingAs($this->player)->get(EDUCATION_BASE.'/portal/admin/opleidingen')->assertForbidden();
});

it('does not require an education when no active course grants company access', function () {
    $this->actingAs($this->player)
        ->get(EDUCATION_BASE.'/portal/companies/register')
        ->assertOk();
});
