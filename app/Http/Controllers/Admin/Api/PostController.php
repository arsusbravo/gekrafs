<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Support\RichText;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $posts = Post::query()
            ->with(['user:id,name', 'event:id,title'])
            ->when($request->string('search')->toString(), fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate(15);

        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['image'] = $request->file('image')?->store('posts', 'public');

        $post = $request->user()->posts()->create($data);

        return response()->json($post, 201);
    }

    public function show(Post $post): JsonResponse
    {
        return response()->json($post);
    }

    public function update(Request $request, Post $post): JsonResponse
    {
        $data = $this->validated($request, $post);

        if ($request->hasFile('image')) {
            $this->deleteImage($post);
            $data['image'] = $request->file('image')->store('posts', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($post);
            $data['image'] = null;
        } else {
            unset($data['image']);
        }

        $post->update($data);

        return response()->json($post->fresh());
    }

    public function destroy(Post $post): JsonResponse
    {
        $this->deleteImage($post);
        $post->delete();

        return response()->json(null, 204);
    }

    private function validated(Request $request, ?Post $post = null): array
    {
        $data = $request->validate([
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
            'title' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', 'alpha_dash', Rule::unique('posts', 'slug')->ignore($post)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);

        $data['slug'] = ($data['slug'] ?? null) ?: $this->uniqueSlug($data['title'], $post);
        $data['body'] = RichText::sanitize($data['body']);

        if ($data['body'] === '') {
            throw ValidationException::withMessages(['body' => __('validation.required', ['attribute' => 'body'])]);
        }

        return $data;
    }

    private function uniqueSlug(string $title, ?Post $post): string
    {
        $base = Str::slug($title) ?: 'post';
        $slug = $base;
        $i = 2;

        while (Post::where('slug', $slug)->when($post, fn ($q) => $q->whereKeyNot($post->id))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function deleteImage(Post $post): void
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
    }
}
