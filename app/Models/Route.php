<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $primaryKey = 'route_id';
    public $timestamps = false;

    protected $fillable = [
        'origin_city',
        'destination_city',
        'distance_km',
        'duration',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'route_id');
    }
}
