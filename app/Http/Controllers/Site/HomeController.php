<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    private const EVENT_LIMIT = 3;

    public function __invoke(): View
    {
        return view('site.home', [
            'events' => $this->latestEvents(),
            'posts' => Post::published()->latest('published_at')->take(3)->get(),
        ]);
    }

    /**
     * The latest events: upcoming ones first (soonest first), then the most recent past events.
     *
     * @return \Illuminate\Support\Collection<int, Event>
     */
    private function latestEvents()
    {
        $events = Event::published()->upcoming()->take(self::EVENT_LIMIT)->get();

        if ($events->count() < self::EVENT_LIMIT) {
            $events = $events->concat(
                Event::published()->past()->take(self::EVENT_LIMIT - $events->count())->get()
            );
        }

        return $events;
    }
}
