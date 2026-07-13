<?php

namespace Tests\Feature;

use App\Models\User;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_user_management(): void
    {
        $response = $this->get('/users');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_user_management(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/users');

        $response->assertOk()
            ->assertSeeVolt('user-management');
    }

    public function test_can_create_new_user_via_dashboard(): void
    {
        $admin = User::factory()->create();

        $component = Volt::actingAs($admin)
            ->test('user-management')
            ->set('name', 'New Member')
            ->set('email', 'newmember@example.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123');

        $component->call('createUser');

        $component->assertHasNoErrors();
        $this->assertDatabaseHas('users', [
            'name' => 'New Member',
            'email' => 'newmember@example.com',
        ]);
    }

    public function test_cannot_delete_self(): void
    {
        $admin = User::factory()->create();
        $this->actingAs($admin);

        $component = Volt::test('user-management');
        $component->call('deleteUser', $admin->id);

        $component->assertSee('Anda tidak dapat menghapus akun Anda sendiri!');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_can_delete_other_user(): void
    {
        $admin = User::factory()->create();
        $otherUser = User::factory()->create();
        $this->actingAs($admin);

        $component = Volt::test('user-management');
        $component->call('deleteUser', $otherUser->id);

        $component->assertSee('User berhasil dihapus!');
        $this->assertDatabaseMissing('users', ['id' => $otherUser->id]);
    }
}
