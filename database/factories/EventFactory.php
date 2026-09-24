<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $title = rtrim(fake()->sentence(4), '.');
        $startsAt = fake()->dateTimeBetween('-1 month', '+3 months');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'description' => fake()->paragraphs(3, true),
            'location' => fake()->city(),
            'starts_at' => $startsAt,
            'ends_at' => (clone $startsAt)->modify('+3 hours'),
            'is_published' => true,
        ];
    }
}
