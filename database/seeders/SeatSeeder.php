<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeatSeeder extends Seeder
{
    /**
     * Seed seats for each vehicle based on its capacity.
     */
    public function run(): void
    {
        $vehicles = DB::table('vehicles')->get();

        foreach ($vehicles as $vehicle) {
            $seats = [];
            for ($i = 1; $i <= $vehicle->capacity; $i++) {
                $seatClass = $i <= 4 ? 'VIP' : 'Regular';

                $seats[] = [
                    'vehicle_id' => $vehicle->vehicle_id,
                    'seat_number' => str_pad($i, 2, '0', STR_PAD_LEFT), // 01, 02, ...
                    'seat_class' => $seatClass,
                ];
            }
            DB::table('seats')->insert($seats);
        }
    }
}
