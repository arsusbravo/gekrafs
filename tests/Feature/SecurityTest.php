<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_sent_on_the_site_and_the_admin(): void
    {
        $this->get('/')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        $this->actingAs(User::factory()->create())->get('/admin')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN');
    }

    public function test_hsts_is_only_sent_over_https(): void
    {
        $this->get('http://localhost/')->assertHeaderMissing('Strict-Transport-Security');
        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security', 'max-age=31536000');
    }

    public function test_language_switch_does_not_redirect_to_other_sites(): void
    {
        $this->withHeader('referer', 'https://evil.example/phishing')
            ->get('/language/nl')
            ->assertRedirect(url('/nl'));

        $this->withHeader('referer', url('/login'))
            ->get('/language/id')
            ->assertRedirect(url('/login'));
    }

    public function test_production_requires_stronger_passwords(): void
    {
        $this->app['env'] = 'production';
        $user = User::factory()->create();

        $this->actingAs($user)->putJson('/admin/api/account/password', [
            'current_password' => 'password',
            'password' => 'short1',
            'password_confirmation' => 'short1',
        ])->assertJsonValidationErrors('password');

        $this->actingAs($user)->putJson('/admin/api/account/password', [
            'current_password' => 'password',
            'password' => 'gekrafs-belanda-2026',
            'password_confirmation' => 'gekrafs-belanda-2026',
        ])->assertOk();
    }

    public function test_editor_image_uploads_are_rate_limited(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        for ($i = 0; $i < 30; $i++) {
            $this->actingAs($user)->postJson('/admin/api/images', ['image' => UploadedFile::fake()->image("photo{$i}.jpg")])->assertCreated();
        }

        $this->actingAs($user)->postJson('/admin/api/images', ['image' => UploadedFile::fake()->image('one-too-many.jpg')])
            ->assertTooManyRequests();
    }

    public function test_event_images_must_be_raster_images(): void
    {
        $svg = UploadedFile::fake()->createWithContent('cover.svg', '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"></svg>');

        $this->actingAs(User::factory()->create())->postJson('/admin/api/events', [
            'title' => 'Event with SVG',
            'starts_at' => '2030-01-01T10:00',
            'image' => $svg,
        ])->assertJsonValidationErrors('image');
    }

    public function test_seeded_admins_get_no_password_from_the_code(): void
    {
        $this->seed(UserSeeder::class);

        $admin = User::firstWhere('email', 'info@arsus.nl');
        $this->assertTrue($admin->is_admin);
        $this->assertFalse(Hash::check('arsus@29', $admin->password));
    }

    public function test_seeding_again_keeps_existing_passwords_and_roles(): void
    {
        $user = User::factory()->create(['email' => UserSeeder::AUTHOR_EMAIL, 'password' => 'my-own-password-1']);

        $this->seed(UserSeeder::class);

        $user->refresh();
        $this->assertFalse($user->is_admin);
        $this->assertTrue(Hash::check('my-own-password-1', $user->password));
    }
}
