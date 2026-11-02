<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteContent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
    $this->get('http://portaal.test/')
        ->assertOk()
        ->assertSee('Jouw verhaal begint hier.')
        ->assertSee('Samen houden we de stad leuk.')
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

it('lets only settings managers update public website content', function () {
    $this->actingAs(siteUser())
        ->put('http://portaal.test/portal/admin/settings/site-content', [
            'hero_title' => 'Welkom in Westerdam',
            'hero_text' => 'Een verhaal voor iedereen.',
            'about_title' => 'Over onze stad',
            'about_text' => 'Bouw mee aan de stad.',
            'rules_title' => 'Onze afspraken',
            'rules_text' => "Wees aardig.\nSpeel eerlijk.",
        ])
        ->assertForbidden();

    $admin = siteUser(['manage-settings']);

    $this->actingAs($admin)
        ->get('http://portaal.test/portal/admin/settings')
        ->assertOk()
        ->assertSee('Openbare website');

    $this->put('http://portaal.test/portal/admin/settings/site-content', [
        'hero_title' => 'Welkom in Westerdam',
        'hero_text' => 'Een verhaal voor iedereen.',
        'about_title' => 'Over onze stad',
        'about_text' => 'Bouw mee aan de stad.',
        'rules_title' => 'Onze afspraken',
        'rules_text' => "<script>alert('x')</script>\nSpeel eerlijk.",
    ])->assertSessionHas('success', 'De inhoud van de website is bijgewerkt.');
});

it('escapes public website rules when rendering them', function () {
    SiteContent::current()->update([
        'hero_title' => 'Welkom in Westerdam',
        'rules_text' => "<script>alert('x')</script>\nSpeel eerlijk.",
    ]);

    $this->get('http://portaal.test/')
        ->assertOk()
        ->assertSee('Welkom in Westerdam')
        ->assertSee('&lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt;', false)
        ->assertDontSee("<script>alert('x')</script>", false);
});
