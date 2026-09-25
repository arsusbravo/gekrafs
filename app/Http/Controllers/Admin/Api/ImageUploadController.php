<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Images placed inside rich text (event descriptions and news articles).
 */
class ImageUploadController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:4096'],
        ]);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        $path = $request->file('image')->store('content', 'public');

        return response()->json(['url' => $disk->url($path)], 201);
    }
}
