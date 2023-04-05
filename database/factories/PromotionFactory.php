<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $validFrom = $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d');
        $validTo = $this->faker->dateTimeBetween($validFrom, '+3 months')->format('Y-m-d');

        return [
            'uuid' => fake()->unique()->uuid(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'metadata' => [
                "valid_from" => $validFrom,
                "valid_to" => $validTo,
                "image" => File::all()->random()->uuid,
            ]
        ];
    }
}
