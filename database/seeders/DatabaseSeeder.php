<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Eloquent\Models\UserModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // admin (fixed)
        UserModel::updateOrCreate(
            ['email' => 'admin@project.com'],
            ['name' => 'Admin', 'password' => Hash::make('password123')]
        );

        // random users
        UserModel::factory()->count(100)->create();

        // user fixed
        UserModel::factory()->create([
            'name' => 'User',
            'email' => 'user@project.com',
        ]);
    }
}