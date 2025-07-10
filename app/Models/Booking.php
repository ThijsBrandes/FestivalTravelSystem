<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'festival_id',
        'trip_id',
        'status',
        'total_price',
        'ticket_quantity',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function festival() {
        return $this->belongsTo(Festival::class);
    }

    public function trip() {
        return $this->belongsTo(Trip::class);
    }
}
