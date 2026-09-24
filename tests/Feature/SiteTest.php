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

    public function test_visitors_can_switch_language(): void
    {
        $this->from('/about')->get('/language/nl')->assertRedirect('/about');
        $this->get('/about')->assertSee('Missie en belangrijkste focus');

        $this->get('/language/id');
        $this->get('/')->assertSee('Acara mendatang');

        $this->get('/language/xx')->assertNotFound();
    }

    public function test_seeders_create_events_and_their_reports(): void
    {
        Storage::fake('public');
        $this->seed();

        $this->assertSame(3, Event::count());
        $this->assertSame(2, Post::count());
        $this->assertSame(2, Post::whereNotNull('event_id')->count());

        // Seeders copy their pictures from public/images onto the public disk.
        $this->assertSame(0, Event::whereNull('image')->count() + Post::whereNull('image')->count());
        Storage::disk('public')->assertExists(['events/boards.jpg', 'posts/pelantikan.jpg']);

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
}
