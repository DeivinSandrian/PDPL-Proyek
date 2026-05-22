<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Seed schedules — upcoming departures for the seeded routes and vehicles.
     */
    public function run(): void
    {
        DB::table('schedules')->insert([
            // Jakarta → Bandung (route_id: 1, Bus B 1234 TGO, vehicle_id: 1)
            [
                'route_id' => 1,
                'vehicle_id' => 1,
                'departure_time' => '2026-05-15 06:00:00',
                'arrival_estimate' => '2026-05-15 09:00:00',
                'price' => 150000.00,
                'status' => 'available',
            ],
            [
                'route_id' => 1,
                'vehicle_id' => 3,
                'departure_time' => '2026-05-15 12:00:00',
                'arrival_estimate' => '2026-05-15 15:00:00',
                'price' => 120000.00,
                'status' => 'available',
            ],
            // Bandung → Jakarta (route_id: 2, Bus B 5678 TGO, vehicle_id: 2)
            [
                'route_id' => 2,
                'vehicle_id' => 2,
                'departure_time' => '2026-05-15 07:00:00',
                'arrival_estimate' => '2026-05-15 10:00:00',
                'price' => 150000.00,
                'status' => 'available',
            ],
            // Jakarta → Semarang (route_id: 3, Bus B 1234 TGO, vehicle_id: 1)
            [
                'route_id' => 3,
                'vehicle_id' => 1,
                'departure_time' => '2026-05-16 20:00:00',
                'arrival_estimate' => '2026-05-17 03:00:00',
                'price' => 300000.00,
                'status' => 'available',
            ],
            // Semarang → Surabaya (route_id: 4, Minibus D 9012 TGO, vehicle_id: 3)
            [
                'route_id' => 4,
                'vehicle_id' => 3,
                'departure_time' => '2026-05-16 08:00:00',
                'arrival_estimate' => '2026-05-16 13:30:00',
                'price' => 200000.00,
                'status' => 'available',
            ],
            // Surabaya → Malang (route_id: 5, Shuttle L 3456 TGO, vehicle_id: 4)
            [
                'route_id' => 5,
                'vehicle_id' => 4,
                'departure_time' => '2026-05-15 09:00:00',
                'arrival_estimate' => '2026-05-15 11:00:00',
                'price' => 75000.00,
                'status' => 'available',
            ],
            // Yogyakarta → Semarang (route_id: 6, Bus B 5678 TGO, vehicle_id: 2)
            [
                'route_id' => 6,
                'vehicle_id' => 2,
                'departure_time' => '2026-05-17 06:30:00',
                'arrival_estimate' => '2026-05-17 09:00:00',
                'price' => 100000.00,
                'status' => 'available',
            ],
            // Jakarta → Yogyakarta (route_id: 7, Bus B 1234 TGO, vehicle_id: 1)
            [
                'route_id' => 7,
                'vehicle_id' => 1,
                'departure_time' => '2026-05-18 18:00:00',
                'arrival_estimate' => '2026-05-19 03:00:00',
                'price' => 350000.00,
                'status' => 'available',
            ],
        ]);
    }
}
