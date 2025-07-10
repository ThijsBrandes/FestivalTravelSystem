<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company',
        'license_plate',
        'color',
        'total_seats',
        'available_seats',
        'status',
    ];
    protected $casts = [
        'total_seats' => 'integer',
        'available_seats' => 'integer',
        'status' => 'string',
    ];

    public function trips() {
        return $this->hasMany(Trip::class);
    }
}
