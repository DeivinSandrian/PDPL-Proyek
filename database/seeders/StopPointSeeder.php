<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StopPointSeeder extends Seeder
{
    /**
     * Seed stop points (pickup/dropoff locations) for major cities.
     */
    public function run(): void
    {
        DB::table('stop_points')->insert([
            // Jakarta
            [
                'name' => 'Terminal Pulo Gebang',
                'address' => 'Jl. Raya Bekasi Km. 26, Jakarta Timur',
                'type' => 'pickup',
            ],
            [
                'name' => 'Terminal Lebak Bulus',
                'address' => 'Jl. Lebak Bulus Raya, Jakarta Selatan',
                'type' => 'pickup',
            ],
            // Bandung
            [
                'name' => 'Terminal Leuwi Panjang',
                'address' => 'Jl. Soekarno Hatta No.205, Bandung',
                'type' => 'dropoff',
            ],
            [
                'name' => 'Stasiun Hall Bandung',
                'address' => 'Jl. Kebon Kawung No.43, Bandung',
                'type' => 'dropoff',
            ],
            // Semarang
            [
                'name' => 'Terminal Terboyo',
                'address' => 'Jl. Terboyo Industri, Semarang',
                'type' => 'pickup',
            ],
            // Surabaya
            [
                'name' => 'Terminal Purabaya (Bungurasih)',
                'address' => 'Jl. Letjen Sutoyo, Waru, Sidoarjo',
                'type' => 'dropoff',
            ],
            // Yogyakarta
            [
                'name' => 'Terminal Giwangan',
                'address' => 'Jl. Imogiri Timur, Yogyakarta',
                'type' => 'pickup',
            ],
            // Malang
            [
                'name' => 'Terminal Arjosari',
                'address' => 'Jl. Raden Intan, Malang',
                'type' => 'dropoff',
            ],
        ]);
    }
}
