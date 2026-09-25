<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Support\RichText;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $events = Event::query()
            ->when($request->string('search')->toString(), fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->latest('starts_at')
            ->paginate(15);

        return response()->json($events);
    }

    /**
     * Lightweight list of all events, used for select inputs.
     */
    public function options(): JsonResponse
    {
        return response()->json(Event::latest('starts_at')->get(['id', 'title', 'starts_at']));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validated($request);
        $data['image'] = $request->file('image')?->store('events', 'public');

        $event = $request->user()->events()->create($data);

        return response()->json($event, 201);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json($event);
    }

    public function update(Request $request, Event $event): JsonResponse
    {
        $data = $this->validated($request, $event);

        if ($request->hasFile('image')) {
            $this->deleteImage($event);
            $data['image'] = $request->file('image')->store('events', 'public');
        } elseif ($request->boolean('remove_image')) {
            $this->deleteImage($event);
            $data['image'] = null;
        } else {
            unset($data['image']);
        }

        $event->update($data);

        return response()->json($event->fresh());
    }

    public function destroy(Event $event): JsonResponse
    {
        $this->deleteImage($event);
        $event->delete();

        return response()->json(null, 204);
    }

    private function validated(Request $request, ?Event $event = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', 'alpha_dash', Rule::unique('events', 'slug')->ignore($event)],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:191'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
            'is_published' => ['boolean'],
        ]);

        $data['slug'] = ($data['slug'] ?? null) ?: $this->uniqueSlug($data['title'], $event);
        $data['description'] = RichText::sanitize($data['description'] ?? null) ?: null;
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }

    private function uniqueSlug(string $title, ?Event $event): string
    {
        $base = Str::slug($title) ?: 'event';
        $slug = $base;
        $i = 2;

        while (Event::where('slug', $slug)->when($event, fn ($q) => $q->whereKeyNot($event->id))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    private function deleteImage(Event $event): void
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
    }
}
