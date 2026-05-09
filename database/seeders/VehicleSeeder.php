<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleSeeder extends Seeder
{
    /**
     * Seed the vehicles table with sample shuttle/bus data.
     */
    public function run(): void
    {
        DB::table('vehicles')->insert([
            [
                'plate_number' => 'B 1234 TGO',
                'vehicle_type' => 'Bus',
                'capacity' => 40,
                'status' => 'active',
            ],
            [
                'plate_number' => 'B 5678 TGO',
                'vehicle_type' => 'Bus',
                'capacity' => 40,
                'status' => 'active',
            ],
            [
                'plate_number' => 'D 9012 TGO',
                'vehicle_type' => 'Minibus',
                'capacity' => 16,
                'status' => 'active',
            ],
            [
                'plate_number' => 'L 3456 TGO',
                'vehicle_type' => 'Shuttle',
                'capacity' => 8,
                'status' => 'active',
            ],
            [
                'plate_number' => 'AB 7890 TGO',
                'vehicle_type' => 'Shuttle',
                'capacity' => 8,
                'status' => 'maintenance',
            ],
        ]);
    }
}
