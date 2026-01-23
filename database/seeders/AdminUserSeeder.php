<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@laterx.com'], // Admin email
            [
                'name' => 'Admin User',
                'password' => Hash::make('admin123'), // Set your password
                'role' => 'admin',
                'approved' => true, // Admin is always approved
            ]
        );
    }
}
