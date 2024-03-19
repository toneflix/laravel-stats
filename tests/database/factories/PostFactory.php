<?php

namespace ToneflixCode\Stats\Tests\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ToneflixCode\Stats\Tests\Models\Post;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(10, true),
            'slug' => $this->faker->slug(),
            'created_at' => now()->subDays(rand(1, 365))
        ];
    }
}