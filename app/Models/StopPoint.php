<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StopPoint extends Model
{
    use HasFactory;

    protected $primaryKey = 'stop_point_id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'address',
        'type',
    ];
}
