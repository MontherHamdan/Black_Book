<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@admin.com'], // Check if this email already exists
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('123456789'), // Encrypt the password
                'is_admin' => true, // Set as admin if applicable
            ]
        );
    }
}
