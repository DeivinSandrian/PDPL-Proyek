<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RouteSeeder extends Seeder
{
    /**
     * Seed the routes table with popular Indonesian intercity routes.
     */
    public function run(): void
    {
        DB::table('routes')->insert([
            [
                'origin_city' => 'Jakarta',
                'destination_city' => 'Bandung',
                'distance_km' => 150,
                'duration' => '03:00:00',
            ],
            [
                'origin_city' => 'Bandung',
                'destination_city' => 'Jakarta',
                'distance_km' => 150,
                'duration' => '03:00:00',
            ],
            [
                'origin_city' => 'Jakarta',
                'destination_city' => 'Semarang',
                'distance_km' => 450,
                'duration' => '07:00:00',
            ],
            [
                'origin_city' => 'Semarang',
                'destination_city' => 'Surabaya',
                'distance_km' => 350,
                'duration' => '05:30:00',
            ],
            [
                'origin_city' => 'Surabaya',
                'destination_city' => 'Malang',
                'distance_km' => 90,
                'duration' => '02:00:00',
            ],
            [
                'origin_city' => 'Yogyakarta',
                'destination_city' => 'Semarang',
                'distance_km' => 130,
                'duration' => '02:30:00',
            ],
            [
                'origin_city' => 'Jakarta',
                'destination_city' => 'Yogyakarta',
                'distance_km' => 570,
                'duration' => '09:00:00',
            ],
        ]);
    }
}
