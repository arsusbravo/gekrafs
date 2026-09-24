<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('site.blog.index', [
            'posts' => Post::published()->with('user')->latest('published_at')->paginate(9),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published, 404);

        $post->load(['user', 'event' => fn ($query) => $query->published()]);

        return view('site.blog.show', ['post' => $post]);
    }
}
