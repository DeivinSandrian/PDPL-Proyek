<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingSeat extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_seat_id';
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'seat_id',
        'price_at_booking',
    ];

    protected function casts(): array
    {
        return [
            'price_at_booking' => 'decimal:2',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class, 'seat_id');
    }
}
