<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

trait CopiesSiteImages
{
    /**
     * Copy one of the site images (public/images) onto the public disk, where uploaded images live,
     * so the admin can later replace or delete it without touching the original.
     */
    protected function copyImage(string $file, string $folder): ?string
    {
        $source = public_path("images/{$file}");

        if (! is_file($source)) {
            return null;
        }

        Storage::disk('public')->put("{$folder}/{$file}", file_get_contents($source));

        return "{$folder}/{$file}";
    }
}
