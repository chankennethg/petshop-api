<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\FileFactory;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FileFactory::new()->count(15)->create();
    }
}
