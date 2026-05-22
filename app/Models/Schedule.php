<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $primaryKey = 'schedule_id';
    public $timestamps = false;

    protected $fillable = [
        'route_id',
        'vehicle_id',
        'departure_time',
        'arrival_estimate',
        'price',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'departure_time' => 'datetime',
            'arrival_estimate' => 'datetime',
            'price' => 'decimal:2',
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function route()
    {
        return $this->belongsTo(Route::class, 'route_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'schedule_id');
    }
}
