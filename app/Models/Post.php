<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use App\Support\RichText;
use Database\Factories\PostFactory;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['event_id', 'title', 'slug', 'excerpt', 'body', 'image', 'published_at'])]
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, HasImage;

    protected $appends = ['image_url', 'is_published'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Serialize dates as app-timezone wall time so admin date inputs round-trip cleanly.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The event this post reports on, if any.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * The body as safe HTML for the website.
     */
    public function getBodyHtmlAttribute(): string
    {
        return RichText::sanitize($this->body);
    }

    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->published_at !== null && $this->published_at->lte(now());
    }
}
