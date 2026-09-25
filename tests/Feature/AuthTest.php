<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('Log in');
    }

    public function test_public_registration_is_disabled(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Jane',
            'email' => 'jane@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
        $this->get('/')->assertDontSee('/register');
        $this->get('/login')->assertDontSee('/register');
    }

    public function test_regular_user_is_redirected_to_admin_after_login(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_is_redirected_to_admin_after_login(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_users_can_log_out(): void
    {
        $this->actingAs(User::factory()->create())->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }
}
