<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_published_content(): void
    {
        $event = Event::factory()->create(['starts_at' => now()->addWeek(), 'title' => 'Summer Meetup']);
        $post = Post::factory()->create(['title' => 'Hello World']);

        $this->get('/')->assertOk()->assertSee('Summer Meetup')->assertSee('Hello World');
        $this->get('/events')->assertOk()->assertSee('Summer Meetup');
        $this->get('/events/'.$event->slug)->assertOk()->assertSee('Summer Meetup');
        $this->get('/blog')->assertOk()->assertSee('Hello World');
        $this->get('/blog/'.$post->slug)->assertOk()->assertSee('Hello World');
    }

    public function test_unpublished_content_is_hidden(): void
    {
        $event = Event::factory()->create(['is_published' => false, 'starts_at' => now()->addWeek(), 'title' => 'Secret Event']);
        $draft = Post::factory()->create(['published_at' => null, 'title' => 'Draft Post']);
        $scheduled = Post::factory()->create(['published_at' => now()->addDay()]);

        $this->get('/events')->assertDontSee('Secret Event');
        $this->get('/events/'.$event->slug)->assertNotFound();
        $this->get('/blog')->assertDontSee('Draft Post');
        $this->get('/blog/'.$draft->slug)->assertNotFound();
        $this->get('/blog/'.$scheduled->slug)->assertNotFound();
    }

    public function test_static_pages_render(): void
    {
        $this->get('/about')->assertOk()->assertSee('Gerakan Ekonomi Kreatif Nasional')->assertSee('17 Creative Economy Sub-Sectors');
        $this->get('/contact')->assertOk()->assertSee('Endang den Boer-Wahyuni');
    }

    public function test_language_is_taken_from_the_url(): void
    {
        $this->get('/about')->assertOk()->assertSee('Mission and Main Focus');
        $this->get('/nl/about')->assertOk()->assertSee('Missie en belangrijkste focus');
        $this->get('/id')->assertOk()->assertSee('Acara mendatang');

        // English stays at the root, even after visiting a Dutch page.
        $this->get('/nl');
        $this->get('/about')->assertSee('Mission and Main Focus');

        $this->get('/fr/about')->assertNotFound();
    }

    public function test_localized_detail_pages_and_links(): void
    {
        $event = Event::factory()->create(['starts_at' => now()->addWeek()]);
        $post = Post::factory()->create();

        $this->get("/nl/events/{$event->slug}")->assertOk()->assertSee('Wanneer');
        $this->get("/id/blog/{$post->slug}")->assertOk();

        // Links on a Dutch page stay in Dutch.
        $this->get('/nl/events')
            ->assertSee('href="'.url("/nl/events/{$event->slug}").'"', false)
            ->assertSee('href="'.url('/nl/blog').'"', false)
            ->assertDontSee('href="'.url('/blog').'"', false);
    }

    public function test_language_switcher_links_to_the_same_page(): void
    {
        $event = Event::factory()->create();

        $this->get("/nl/events/{$event->slug}")
            ->assertSee('href="'.url("/events/{$event->slug}").'"', false)
            ->assertSee('href="'.url("/id/events/{$event->slug}").'"', false)
            ->assertSee('hreflang="x-default" href="'.url("/events/{$event->slug}").'"', false);

        // Pagination keeps its query string in the other language.
        $this->get('/nl/blog?page=2')->assertSee('href="'.url('/id/blog?page=2').'"', false);
    }

    public function test_login_page_follows_the_last_visited_language(): void
    {
        $this->get('/id');
        $this->get('/login')->assertSee('Kata sandi');

        $this->from('/login')->get('/language/nl')->assertRedirect('/login');
        $this->get('/login')->assertSee('Wachtwoord');
    }

    public function test_seeders_create_events_and_their_reports(): void
    {
        $disk = Storage::fake('public');
        $this->seed();

        $this->assertSame(3, Event::count());
        $this->assertSame(2, Post::count());
        $this->assertSame(2, Post::whereNotNull('event_id')->count());

        // Seeders copy their pictures from public/images onto the public disk.
        $this->assertSame(0, Event::whereNull('image')->count() + Post::whereNull('image')->count());
        $disk->assertExists(['events/boards.jpg', 'posts/pelantikan.jpg']);

        $this->travelTo('2026-09-23');
        $this->get('/')->assertSee('Indonesian Culinary &amp; Creative Fest 2026', false);
        $this->get('/events')->assertSee('Business Seminar: Investing in Wonderful Indonesia');
    }

    public function test_events_and_reports_link_to_each_other(): void
    {
        $event = Event::factory()->create(['title' => 'Gala Night']);
        $report = Post::factory()->for($event)->create(['title' => 'Gala Night Report']);
        Post::factory()->for($event)->create(['title' => 'Draft Report', 'published_at' => null]);

        $this->get('/events/'.$event->slug)->assertSee('Gala Night Report')->assertDontSee('Draft Report');
        $this->get('/blog/'.$report->slug)->assertSee('Gala Night')->assertSee('/events/'.$event->slug, false);
    }

    public function test_image_urls_are_relative_to_the_current_host(): void
    {
        // Uploads are served from public/uploads, independent of APP_URL (http/https or another domain).
        config(['app.url' => 'http://some-other-host.test']);

        $event = Event::factory()->create(['image' => 'events/cover.jpg']);
        $post = Post::factory()->create(['image' => 'posts/cover.jpg']);

        $this->assertSame('/uploads/events/cover.jpg', $event->image_url);
        $this->assertSame('/uploads/posts/cover.jpg', $post->image_url);
        $this->get('/events/'.$event->slug)->assertSee('src="/uploads/events/cover.jpg"', false);
    }
}
