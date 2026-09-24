<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        return view('site.events.index', [
            'upcoming' => Event::published()->upcoming()->paginate(9),
            'past' => Event::published()->where('starts_at', '<', now()->startOfDay())->latest('starts_at')->take(6)->get(),
        ]);
    }

    public function show(Event $event): View
    {
        abort_unless($event->is_published, 404);

        return view('site.events.show', [
            'event' => $event,
            'reports' => $event->reports()->published()->latest('published_at')->get(),
        ]);
    }
}
