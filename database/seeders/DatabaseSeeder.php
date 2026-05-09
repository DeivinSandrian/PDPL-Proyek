<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,       // Admin + sample customers
            VehicleSeeder::class,    // Buses, minibuses, shuttles
            RouteSeeder::class,      // Intercity routes
            StopPointSeeder::class,  // Pickup/dropoff terminals
            ScheduleSeeder::class,   // Upcoming departures
            SeatSeeder::class,       // Auto-generate seats per vehicle
            BookingSeeder::class,    // Sample bookings with payments & e-tickets
        ]);
    }
}
