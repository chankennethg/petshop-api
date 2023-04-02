<?php

namespace Database\Seeders;

use Hash;
use Illuminate\Database\Seeder;
use Database\Factories\UserFactory;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Admin account
        UserFactory::new()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@buckhill.co.uk',
            'password' => Hash::make('admin'),
            'is_admin' => true,
        ]);

        // Default User accounts
        UserFactory::new()->count(20)->create([
            'is_admin' => false,
        ]);
    }
}
