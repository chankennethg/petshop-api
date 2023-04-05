<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'title' => fake()->sentence(),
            'slug' => fake()->slug(),
            'content' => fake()->paragraph(rand(3,10)),
            'metadata' => [
                'image' => File::all()->random()->uuid,
                'author' => fake()->name(),
            ]
        ];
    }
}
