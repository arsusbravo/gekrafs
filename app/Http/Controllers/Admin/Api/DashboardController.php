<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'stats' => [
                'events' => Event::count(),
                'upcoming_events' => Event::published()->upcoming()->count(),
                'posts' => Post::count(),
                'published_posts' => Post::published()->count(),
                'users' => User::count(),
            ],
            'next_events' => Event::upcoming()->take(5)->get(['id', 'title', 'slug', 'starts_at', 'location', 'is_published']),
            'latest_posts' => Post::latest()->take(5)->get(['id', 'title', 'slug', 'published_at', 'created_at']),
        ]);
    }
}
