<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'festival_id',
        'starting_location',
        'destination',
        'departure_time',
        'arrival_time',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];

    public function bus() {
        return $this->belongsTo(Bus::class);
    }

    public function festival() {
        return $this->belongsTo(Festival::class);
    }

    public function bookings() {
        return $this->hasMany(Booking::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($trip) {
            if ($trip->festival) {
                $trip->destination = $trip->festival->location;
            }
        });
    }
}

