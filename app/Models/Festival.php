<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Festival extends Model
{
    /** @use HasFactory<\Database\Factories\FestivalFactory> */
    use HasFactory, SoftDeletes;

    public function bookings() {
        return $this->hasMany(Booking::class);
    }
}
