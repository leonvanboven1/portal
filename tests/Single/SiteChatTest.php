<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\SiteChatConversation;
use App\Models\SiteChatMessage;
use App\Models\SiteChatStaffPresence;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

function liveChatStaff(bool $withPermission = true): User
{
    $role = Role::create([
        'name' => 'Chatmedewerker '.Str::random(6),
        'slug' => 'chatmedewerker-'.Str::random(8),
    ]);

    if ($withPermission) {
        $role->permissions()->sync(Permission::where('slug', 'manage-live-chat')->pluck('id'));
    }

    $user = User::create([
        'name' => 'Chatmedewerker',
        'email' => Str::uuid().'@example.com',
        'minecraft_username' => 'Chat'.Str::random(8),
        'minecraft_uuid' => str_replace('-', '', (string) Str::uuid()),
        'minecraft_plain_uuid' => (string) Str::uuid(),
        'password' => Hash::make('geheim123'),
        'token' => Str::random(32),
        'minecraft_verified' => true,
    ]);
    $user->roles()->attach($role);

    return $user;
}

it('lets an anonymous visitor chat with available staff and see who replies', function () {
    $this->getJson('http://portaal.test/live-chat/status')
        ->assertOk()
        ->assertJsonPath('available', false);

    $this->postJson('http://portaal.test/live-chat', [
        'visitor_name' => 'Bezoeker',
        'body' => 'Ik heb een vraag.',
    ])->assertCreated()
        ->assertJsonPath('conversation.visitor_name', 'Bezoeker')
        ->assertJsonPath('messages.0.body', 'Ik heb een vraag.');

    $conversation = SiteChatConversation::firstOrFail();
    expect($conversation->messages)->toHaveCount(1)
        ->and($conversation->messages->first()->sender_type)->toBe('visitor');

    $staff = liveChatStaff();
    $this->actingAs($staff)
        ->postJson("http://portaal.test/portal/admin/live-chat/conversations/{$conversation->id}/messages", [
            'body' => 'Hoi Bezoeker, ik help je graag.',
        ])->assertCreated();

    $this->getJson('http://portaal.test/live-chat/status')
        ->assertOk()
        ->assertJsonPath('available', false)
        ->assertJsonPath('conversation.staff_name', $staff->minecraft_username)
        ->assertJsonPath('messages.1.sender_name', $staff->minecraft_username);
});

it('shows staff as available after their authenticated panel heartbeat', function () {
    $staff = liveChatStaff();

    $this->actingAs($staff)
        ->postJson('http://portaal.test/portal/admin/live-chat/presence')
        ->assertNoContent();

    expect(SiteChatStaffPresence::where('user_id', $staff->id)->exists())->toBeTrue();

    $this->getJson('http://portaal.test/live-chat/status')
        ->assertOk()
        ->assertJsonPath('available', true);

    $this->get('http://portaal.test/portal/admin/live-chat')
        ->assertOk()
        ->assertSee('Open gesprekken');
});

it('keeps chat administration behind its dedicated permission', function () {
    $staff = liveChatStaff(withPermission: false);

    $this->actingAs($staff)
        ->get('http://portaal.test/portal/admin/live-chat')
        ->assertForbidden();

    $this->actingAs($staff)
        ->postJson('http://portaal.test/portal/admin/live-chat/presence')
        ->assertForbidden();
});
