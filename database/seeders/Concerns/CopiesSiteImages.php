<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

trait CopiesSiteImages
{
    /**
     * Copy one of the site images (storage/app/public/images) into its own folder, so the
     * admin can later replace or delete it without touching the original.
     */
    protected function copyImage(string $file, string $folder): ?string
    {
        $disk = Storage::disk('public');

        if (! $disk->exists("images/{$file}")) {
            return null;
        }

        $disk->copy("images/{$file}", "{$folder}/{$file}");

        return "{$folder}/{$file}";
    }
}
