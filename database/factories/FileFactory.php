<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dir = storage_path('app/' . config('app.file_uploads_dir'));
        $image = fake()->image($dir, 360, 360, 'animals', false, true, null, 'png');

        return [
            'uuid' => fake()->unique()->uuid(),
            'name' => $image,
            'path' => config('app.file_uploads_dir') . '/' . $image,
            'size' => fake()->randomNumber(5),
            'type' => 'image/png',
        ];
    }
}
