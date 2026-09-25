<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['is_admin' => true])->save();

        return $admin;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->getJson('/admin/api/events')->assertUnauthorized();
    }

    public function test_regular_users_can_manage_content_but_not_users(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($user)->get('/admin')->assertOk();
        $this->actingAs($user)->getJson('/admin/api/dashboard')->assertOk()->assertJsonPath('stats.users', null);
        $this->actingAs($user)->getJson('/admin/api/events')->assertOk();
        $this->actingAs($user)->postJson('/admin/api/posts', ['title' => 'By a user', 'body' => '<p>Hello</p>'])->assertCreated();

        $this->actingAs($user)->getJson('/admin/api/users')->assertForbidden();
        $this->actingAs($user)->getJson("/admin/api/users/{$other->id}")->assertForbidden();
        $this->actingAs($user)->postJson('/admin/api/users', ['name' => 'X', 'email' => 'x@example.com', 'password' => 'secret-password'])->assertForbidden();
        $this->actingAs($user)->putJson("/admin/api/users/{$user->id}", ['name' => 'Me', 'email' => $user->email, 'is_admin' => true])->assertForbidden();
        $this->actingAs($user)->deleteJson("/admin/api/users/{$other->id}")->assertForbidden();

        $this->assertFalse($user->fresh()->is_admin);
        $this->assertModelExists($other);
    }

    public function test_users_can_view_their_account_and_change_their_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->getJson('/admin/api/account')
            ->assertOk()->assertJsonPath('email', $user->email)->assertJsonPath('is_admin', false)->assertJsonMissingPath('password');

        $this->actingAs($user)->putJson('/admin/api/account/password', [
            'current_password' => 'wrong',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ])->assertJsonValidationErrors('current_password');

        $this->actingAs($user)->putJson('/admin/api/account/password', [
            'current_password' => 'password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'different',
        ])->assertJsonValidationErrors('password');

        $this->actingAs($user)->putJson('/admin/api/account/password', [
            'current_password' => 'password',
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ])->assertOk();

        $this->assertTrue(Hash::check('new-secret-password', $user->fresh()->password));
    }

    public function test_admin_can_open_spa_on_any_path(): void
    {
        $this->actingAs($this->admin())->get('/admin/events/create')->assertOk()->assertSee('admin-app');
    }

    public function test_dashboard_returns_stats(): void
    {
        Event::factory(2)->create();
        Post::factory(3)->create();

        $this->actingAs($this->admin())->getJson('/admin/api/dashboard')
            ->assertOk()
            ->assertJsonPath('stats.events', 2)
            ->assertJsonPath('stats.posts', 3);
    }

    public function test_admin_can_manage_events(): void
    {
        Storage::fake('public');
        $admin = $this->actingAs($this->admin());

        $response = $admin->post('/admin/api/events', [
            'title' => 'Launch Party',
            'starts_at' => '2026-12-01T19:00',
            'location' => 'Amsterdam',
            'is_published' => '1',
            'image' => UploadedFile::fake()->image('cover.jpg'),
        ], ['Accept' => 'application/json'])->assertCreated()->assertJsonPath('slug', 'launch-party');

        $event = Event::find($response->json('id'));
        $this->assertTrue($event->is_published);
        Storage::disk('public')->assertExists($event->image);

        $admin->post("/admin/api/events/{$event->id}", [
            '_method' => 'PUT',
            'title' => 'Launch Party 2',
            'slug' => 'launch-party',
            'starts_at' => '2026-12-01T19:00',
            'is_published' => '0',
            'remove_image' => '1',
        ], ['Accept' => 'application/json'])->assertOk()->assertJsonPath('title', 'Launch Party 2');

        Storage::disk('public')->assertMissing($event->image);
        $this->assertNull($event->fresh()->image);
        $this->assertFalse($event->fresh()->is_published);

        $admin->deleteJson("/admin/api/events/{$event->id}")->assertNoContent();
        $this->assertModelMissing($event);
    }

    public function test_event_validation(): void
    {
        $this->actingAs($this->admin())->postJson('/admin/api/events', [
            'title' => '',
            'starts_at' => '2026-12-01T19:00',
            'ends_at' => '2026-11-01T19:00',
        ])->assertUnprocessable()->assertJsonValidationErrors(['title', 'ends_at']);
    }

    public function test_admin_can_manage_posts(): void
    {
        $admin = $this->actingAs($this->admin());

        $id = $admin->postJson('/admin/api/posts', [
            'title' => 'First Post',
            'body' => 'Content here',
            'published_at' => '2026-01-01T10:00',
        ])->assertCreated()->json('id');

        $admin->postJson('/admin/api/posts', ['title' => 'First Post', 'body' => 'Again'])
            ->assertCreated()->assertJsonPath('slug', 'first-post-2');

        $event = Event::factory()->create();
        $admin->getJson('/admin/api/events/options')->assertOk()->assertJsonPath('0.id', $event->id);
        $admin->postJson("/admin/api/posts/{$id}", ['_method' => 'PUT', 'title' => 'First Post', 'body' => 'Report', 'event_id' => $event->id])
            ->assertOk()->assertJsonPath('event_id', $event->id);
        $admin->postJson("/admin/api/posts/{$id}", ['_method' => 'PUT', 'title' => 'First Post', 'body' => 'Report', 'event_id' => 999])
            ->assertJsonValidationErrors('event_id');

        $admin->getJson('/admin/api/posts?search=First')->assertOk()->assertJsonCount(2, 'data');

        $admin->postJson("/admin/api/posts/{$id}", ['_method' => 'PUT', 'title' => 'Edited', 'body' => 'New body'])
            ->assertOk()->assertJsonPath('title', 'Edited');

        $admin->deleteJson("/admin/api/posts/{$id}")->assertNoContent();
    }

    public function test_admin_can_manage_users_but_not_themselves(): void
    {
        $me = $this->admin();
        $admin = $this->actingAs($me);

        $id = $admin->postJson('/admin/api/users', [
            'name' => 'Editor',
            'email' => 'editor@example.com',
            'password' => 'secret-password',
            'is_admin' => true,
        ])->assertCreated()->json('id');

        $this->assertTrue(User::find($id)->is_admin);

        $admin->putJson("/admin/api/users/{$me->id}", ['name' => $me->name, 'email' => $me->email, 'is_admin' => false])
            ->assertUnprocessable();
        $admin->deleteJson("/admin/api/users/{$me->id}")->assertUnprocessable();
        $admin->deleteJson("/admin/api/users/{$id}")->assertNoContent();
    }

    public function test_rich_text_is_sanitized_when_saved_and_rendered(): void
    {
        $admin = $this->actingAs($this->admin());

        $id = $admin->postJson('/admin/api/posts', [
            'title' => 'Formatted',
            'body' => '<h2>Heading</h2><p>Hello <strong>world</strong><script>alert(1)</script></p><p><a href="javascript:alert(1)" onclick="x()">bad</a></p>',
            'published_at' => '2026-01-01T10:00',
        ])->assertCreated()->json('id');

        $body = Post::find($id)->body;
        $this->assertStringContainsString('<h2>Heading</h2>', $body);
        $this->assertStringNotContainsString('script', $body);
        $this->assertStringNotContainsString('javascript:', $body);
        $this->assertStringNotContainsString('onclick', $body);

        // Content that reaches the database some other way is still cleaned when shown.
        Post::whereKey($id)->update(['body' => '<p>Safe</p><script>alert(1)</script>']);
        $this->get('/blog/'.Post::find($id)->slug)->assertOk()->assertSee('<p>Safe</p>', false)->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_empty_editor_content_is_rejected(): void
    {
        $this->actingAs($this->admin())->postJson('/admin/api/posts', ['title' => 'Empty', 'body' => '<p></p>'])
            ->assertUnprocessable()->assertJsonValidationErrors('body');
    }
}
