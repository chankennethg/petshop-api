<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\PromotionFactory;

class PromotionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PromotionFactory::new()->count(50)->create();
    }
}
