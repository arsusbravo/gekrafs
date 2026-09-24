<?php

namespace App\Models;

use App\Models\Concerns\HasImage;
use App\Support\RichText;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title', 'slug', 'description', 'location', 'starts_at', 'ends_at', 'image', 'is_published'])]
class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory, HasImage;

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
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
     * News posts that report on this event.
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * The description as safe HTML for the website.
     */
    public function getDescriptionHtmlAttribute(): string
    {
        return RichText::sanitize($this->description);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): void
    {
        $query->where('starts_at', '>=', now()->startOfDay())->orderBy('starts_at');
    }
}
