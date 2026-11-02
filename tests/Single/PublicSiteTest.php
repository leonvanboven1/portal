<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteContent;
use App\Models\SiteRule;
use App\Models\SiteRuleCategory;
use App\Models\BrandingSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function siteUser(array $permissions = []): User
{
    $minecraftUsername = 'Speler'.Str::random(8);
    $minecraftUuid = (string) Str::uuid();

    $user = User::create([
        'name' => 'Speler',
        'email' => Str::random(8).'@example.com',
        'minecraft_username' => $minecraftUsername,
        'minecraft_uuid' => str_replace('-', '', $minecraftUuid),
        'minecraft_plain_uuid' => $minecraftUuid,
        'password' => Hash::make('geheim123'),
        'token' => Str::random(32),
        'minecraft_verified' => true,
    ]);

    if ($permissions) {
        $role = Role::create(['name' => 'Sitebeheer', 'slug' => 'sitebeheer']);
        $role->permissions()->sync(Permission::whereIn('slug', $permissions)->pluck('id'));
        $user->roles()->attach($role);
    }

    return $user->fresh();
}

it('serves the public website to guests and logged-in users', function () {
    Cache::forget('site.minecraft-status.v1');
    Http::fake([
        '*mcsrvstat.us/*' => Http::response([
            'online' => true,
            'players' => ['online' => 12, 'max' => 100],
        ]),
    ]);

    $this->get('http://portaal.test/')
        ->assertOk()
        ->assertSee('Vunex')
        ->assertSee('Jouw verhaal begint hier.')
        ->assertSee('Wat maakt Vunex uniek?')
        ->assertSee('Hoe begin je jouw avontuur?')
        ->assertSee('Start Minecraft')
        ->assertSee('Bouw je welvaart')
        ->assertSee('play.vunex.nl')
        ->assertSee('12 spelers online')
        ->assertSee(route('site.rules'))
        ->assertSee('Account maken')
        ->assertSee(route('login'));

    $user = siteUser();

    $this->actingAs($user)->get('http://portaal.test/')
        ->assertOk()
        ->assertSee($user->minecraft_username)
        ->assertSee('Naar het panel')
        ->assertSee('Uitloggen')
        ->assertSee('mc-heads.net/avatar/'.$user->minecraft_username.'/40');
});

it('uses the uploaded appearance logo across the public website', function () {
    Storage::fake('public');
    Cache::put('site.minecraft-status.v1', [
        'available' => false,
        'online' => false,
        'players' => null,
        'max_players' => null,
    ], now()->addMinute());
    $path = UploadedFile::fake()->image('vunex-logo.png')->store('branding', 'public');
    $branding = BrandingSetting::create([
        'accent_color' => BrandingSetting::DEFAULT_ACCENT,
        'logo_path' => $path,
        'logo_pixelated' => false,
    ]);
    $logoUrl = route('branding.logo', ['v' => $branding->updated_at->timestamp]);

    $this->get('http://portaal.test/')
        ->assertOk()
        ->assertSee($logoUrl);

    $this->get('http://portaal.test/regels')
        ->assertOk()
        ->assertSee($logoUrl);

    $this->get($logoUrl)->assertOk();
});

it('lets only settings managers update public website content', function () {
    $this->actingAs(siteUser())
        ->put('http://portaal.test/portal/admin/settings/site-content', [
            'hero_title' => 'Welkom in Westerdam',
            'hero_text' => 'Een verhaal voor iedereen.',
            'about_title' => 'Over onze stad',
            'about_text' => 'Bouw mee aan de stad.',
        ])
        ->assertForbidden();

    $admin = siteUser(['manage-settings']);

    $this->actingAs($admin)
        ->get('http://portaal.test/portal/admin/settings')
        ->assertOk()
        ->assertSee('Openbare website')
        ->assertSee('Terug naar de Vunex-website')
        ->assertSee(route('site.home'), false)
        ->assertSee('Afbeeldingen openbare website')
        ->assertSee('Spelregels beheren');

    $this->put('http://portaal.test/portal/admin/settings/site-content', [
        'hero_title' => 'Welkom in Westerdam',
        'hero_text' => 'Een verhaal voor iedereen.',
        'about_title' => 'Over onze stad',
        'about_text' => 'Bouw mee aan de stad.',
    ])->assertSessionHas('success', 'De inhoud van de website is bijgewerkt.');
});

it('escapes public website rules when rendering them', function () {
    $category = new SiteRuleCategory(['name' => 'Algemeen']);
    $category->setRelation('rules', collect([
        new SiteRule(['title' => 'Veiligheid', 'body' => "<script>alert('x')</script>"]),
    ]));
    $html = view('site.rules', [
        'categories' => collect([$category]),
        'content' => new SiteContent(),
        'branding' => new BrandingSetting(),
    ])->render();

    expect($html)
        ->toContain('Veiligheid')
        ->toContain('&lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt;')
        ->not->toContain("<script>alert('x')</script>");
});

it('lets settings managers create rule categories and rules for the separate rules page', function () {
    $this->actingAs(siteUser())
        ->post('http://portaal.test/portal/admin/settings/site-rules/categories', ['name' => 'Algemeen'])
        ->assertForbidden();

    $admin = siteUser(['manage-settings']);
    $this->actingAs($admin)
        ->post('http://portaal.test/portal/admin/settings/site-rules/categories', ['name' => 'Roleplay'])
        ->assertSessionHas('success', 'De regelcategorie is toegevoegd.');

    $category = SiteRuleCategory::where('name', 'Roleplay')->firstOrFail();
    $this->post("http://portaal.test/portal/admin/settings/site-rules/categories/{$category->id}/rules", [
        'title' => 'Speel eerlijk',
        'body' => 'Houd je aan de afspraken van de stad.',
    ])->assertSessionHas('success', 'De regel is toegevoegd.');

    $siteRule = SiteRule::firstOrFail();
    $this->put("http://portaal.test/portal/admin/settings/site-rules/{$siteRule->id}", [
        'title' => 'Speel eerlijk',
        'body' => 'Respecteer de afspraken van de stad.',
    ])->assertSessionHas('success', 'De regel is bijgewerkt.');

    $this->get('http://portaal.test/regels')
        ->assertOk()
        ->assertSee('De spelregels')
        ->assertSee('Roleplay')
        ->assertSee('Speel eerlijk')
        ->assertSee('Respecteer de afspraken van de stad.');
});

it('lets settings managers replace public homepage photos', function () {
    Storage::fake('public');
    $admin = siteUser(['manage-settings']);

    $this->actingAs($admin)
        ->call('PUT', 'http://portaal.test/portal/admin/settings/site-images', [], [], [
            'hero_image' => UploadedFile::fake()->image('vunex.jpg'),
        ])
        ->assertSessionHas('success', 'De afbeeldingen van de website zijn bijgewerkt.');

    $content = SiteContent::current();
    expect($content->hero_image_path)->not->toBeNull();
    Storage::disk('public')->assertExists($content->hero_image_path);

    $path = $content->hero_image_path;
    $this->post('http://portaal.test/portal/admin/settings/site-images', [
        '_method' => 'PUT',
        'remove_hero_image' => '1',
    ])->assertSessionHas('success', 'De afbeeldingen van de website zijn bijgewerkt.');

    expect(SiteContent::current()->hero_image_path)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
