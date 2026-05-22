<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    /**
     * Seed sample bookings, passengers, booking_seats, payments, and e-tickets.
     */
    public function run(): void
    {
        // Booking 1: Budi books Jakarta → Bandung (schedule_id: 1), seats 01 & 02
        $bookingId1 = DB::table('bookings')->insertGetId([
            'user_id' => 2, // Budi
            'schedule_id' => 1,
            'booking_code' => 'TGO-001',
            'booking_channel' => 'online',
            'total_amount' => 300000.00, // 2 seats x 150k
            'status' => 'confirmed',
            'hold_expired_at' => null,
            'created_at' => '2026-05-10 10:30:00',
        ]);

        DB::table('booking_seats')->insert([
            ['booking_id' => $bookingId1, 'seat_id' => 1, 'price_at_booking' => 150000.00],
            ['booking_id' => $bookingId1, 'seat_id' => 2, 'price_at_booking' => 150000.00],
        ]);

        DB::table('passengers')->insert([
            ['booking_id' => $bookingId1, 'full_name' => 'Budi Santoso', 'identity_number' => '3201234567890001', 'phone' => '081234567890'],
            ['booking_id' => $bookingId1, 'full_name' => 'Ani Santoso', 'identity_number' => '3201234567890002', 'phone' => '081234567891'],
        ]);

        DB::table('payments')->insert([
            'booking_id' => $bookingId1,
            'amount' => 300000.00,
            'method' => 'Bank Transfer',
            'status' => 'paid',
            'gateway_transaction_id' => 'TRX-' . Str::upper(Str::random(10)),
            'paid_at' => '2026-05-10 10:45:00',
        ]);

        DB::table('e_tickets')->insert([
            'booking_id' => $bookingId1,
            'ticket_code' => 'ETIX-' . Str::upper(Str::random(8)),
            'qr_code' => 'https://api.qrserver.com/v1/create-qr-code/?data=TGO-001',
            'issued_at' => '2026-05-10 10:46:00',
        ]);

        // Booking 2: Siti books Surabaya → Malang (schedule_id: 6), seat 01
        $bookingId2 = DB::table('bookings')->insertGetId([
            'user_id' => 3, // Siti
            'schedule_id' => 6,
            'booking_code' => 'TGO-002',
            'booking_channel' => 'online',
            'total_amount' => 75000.00,
            'status' => 'pending',
            'hold_expired_at' => '2026-05-14 09:00:00',
            'created_at' => '2026-05-12 15:00:00',
        ]);

        // Seat ID for vehicle 4 (Shuttle, 8 seats) — seats start after vehicles 1,2,3
        // Vehicle 1: 40 seats (1-40), Vehicle 2: 40 seats (41-80), Vehicle 3: 16 seats (81-96), Vehicle 4: 8 seats (97-104)
        DB::table('booking_seats')->insert([
            ['booking_id' => $bookingId2, 'seat_id' => 97, 'price_at_booking' => 75000.00],
        ]);

        DB::table('passengers')->insert([
            ['booking_id' => $bookingId2, 'full_name' => 'Siti Nurhaliza', 'identity_number' => '3578012345670003', 'phone' => '082345678901'],
        ]);

        // Booking 3: Budi books Jakarta → Yogyakarta (schedule_id: 8), pending payment
        DB::table('bookings')->insert([
            'user_id' => 2, // Budi
            'schedule_id' => 8,
            'booking_code' => 'TGO-003',
            'booking_channel' => 'offline',
            'total_amount' => 350000.00,
            'status' => 'pending',
            'hold_expired_at' => '2026-05-17 18:00:00',
            'created_at' => '2026-05-13 09:00:00',
        ]);
    }
}
