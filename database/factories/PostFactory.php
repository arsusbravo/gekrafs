<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    public function definition(): array
    {
        $title = rtrim(fake()->sentence(6), '.');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'excerpt' => fake()->sentence(20),
            'body' => fake()->paragraphs(6, true),
            'published_at' => fake()->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
