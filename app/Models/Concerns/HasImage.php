<?php

namespace App\Models\Concerns;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * For models with an `image` column holding a path on the public disk.
 */
trait HasImage
{
    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($this->image);
    }
}
