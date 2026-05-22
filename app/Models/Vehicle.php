<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $primaryKey = 'vehicle_id';
    public $timestamps = false;

    protected $fillable = [
        'plate_number',
        'vehicle_type',
        'capacity',
        'status',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'vehicle_id');
    }

    public function seats()
    {
        return $this->hasMany(Seat::class, 'vehicle_id');
    }
}
