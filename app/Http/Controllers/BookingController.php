<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingSeat;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['user', 'schedule.route', 'bookingSeats.seat'])->get();
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,schedule_id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,seat_id',
            'booking_channel' => 'required|in:online,offline',
        ]);

        $schedule = Schedule::findOrFail($request->schedule_id);

        $takenSeatIds = BookingSeat::whereHas('booking', function($q) use ($request) {
            $q->where('schedule_id', $request->schedule_id)
              ->whereIn('status', ['pending', 'confirmed']);
        })->pluck('seat_id')->toArray();

        foreach ($request->seat_ids as $seatId) {
            if (in_array($seatId, $takenSeatIds)) {
                return response()->json(['message' => "Seat ID $seatId is already taken"], 422);
            }
        }

        try {
            DB::beginTransaction();

            $booking = Booking::create([
                'user_id' => auth()->id(),
                'schedule_id' => $request->schedule_id,
                'booking_code' => strtoupper(Str::random(8)),
                'booking_channel' => $request->booking_channel,
                'total_amount' => $schedule->price * count($request->seat_ids),
                'status' => 'pending',
            ]);

            foreach ($request->seat_ids as $seatId) {
                BookingSeat::create([
                    'booking_id' => $booking->booking_id,
                    'seat_id' => $seatId,
                    'price_at_booking' => $schedule->price,
                ]);
            }

            DB::commit();

            return response()->json($booking->load('bookingSeats.seat'), 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to create booking', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Booking $booking)
    {
        return response()->json($booking->load(['user', 'schedule.route', 'bookingSeats.seat', 'passengers', 'payment']));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,expired',
        ]);

        $booking->update($request->only(['status']));
        return response()->json($booking);
    }
}
