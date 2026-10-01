<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Development Administrator requested by user
        User::updateOrCreate(
            ['email' => 'sazara@gmail.com'],
            [
                'name'     => 'Sazara Global Admin',
                'password' => Hash::make('Admin@Sazara2026!'),
                'role'     => 'admin',
            ]
        );

        // Production default admin (fallback)
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@sazaraglobal.com')],
            [
                'name'     => env('ADMIN_NAME', 'Sazara Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'SazaraAdmin2026!')),
                'role'     => 'admin',
            ]
        );
    }
}
