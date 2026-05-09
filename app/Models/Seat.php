<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    use HasFactory;

    protected $primaryKey = 'seat_id';
    public $timestamps = false;

    protected $fillable = [
        'vehicle_id',
        'seat_number',
        'seat_class',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function bookingSeats()
    {
        return $this->hasMany(BookingSeat::class, 'seat_id');
    }
}
