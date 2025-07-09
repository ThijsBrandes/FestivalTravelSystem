<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_id',
        'user_id',
        'starting_location',
        'destination',
        'departure_time',
        'arrival_time',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function bus() {
        return $this->belongsTo(Bus::class);
    }
}
