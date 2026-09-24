<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('site.home', [
            'events' => Event::published()->upcoming()->take(3)->get(),
            'posts' => Post::published()->latest('published_at')->take(3)->get(),
        ]);
    }
}
