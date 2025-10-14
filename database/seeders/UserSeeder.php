<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@bizigo.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // New admin user
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Supplier user
        User::firstOrCreate(
            ['email' => 'neslihan@gmail.com'],
            [
                'name' => 'Neslihan',
                'password' => Hash::make('12345678'),
                'role' => 'supplier',
                'email_verified_at' => now(),
            ]
        );

        // Firm users
        User::firstOrCreate(
            ['email' => 'ahmet@bizigo.com'],
            [
                'name' => 'Ahmet Yılmaz',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'ayse@bizigo.com'],
            [
                'name' => 'Ayşe Demir',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'mehmet@bizigo.com'],
            [
                'name' => 'Mehmet Kaya',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'fatma@bizigo.com'],
            [
                'name' => 'Fatma Özkan',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'ali@bizigo.com'],
            [
                'name' => 'Ali Çelik',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        // Additional users for different firms
        User::firstOrCreate(
            ['email' => 'zeynep@bizigo.com'],
            [
                'name' => 'Zeynep Arslan',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );

        User::firstOrCreate(
            ['email' => 'mustafa@bizigo.com'],
            [
                'name' => 'Mustafa Şahin',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ]
        );
    }
} 