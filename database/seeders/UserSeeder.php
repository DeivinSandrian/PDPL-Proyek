<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed users — 1 admin and 2 sample customers.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin TravelGo',
                'email' => 'admin@travelgo.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'customer',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'customer',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
