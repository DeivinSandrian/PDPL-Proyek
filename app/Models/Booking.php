<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_id';
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'schedule_id',
        'booking_code',
        'booking_channel',
        'total_amount',
        'status',
        'hold_expired_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'hold_expired_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class, 'booking_id');
    }

    public function passengers()
    {
        return $this->hasMany(Passenger::class, 'booking_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id');
    }

    public function eTicket()
    {
        return $this->hasOne(ETicket::class, 'booking_id');
    }
}
