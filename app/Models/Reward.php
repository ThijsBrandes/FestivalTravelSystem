<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reward extends Model
{
    /** @use HasFactory<\Database\Factories\RewardFactory> */
    use HasFactory, SoftDeletes;

    public function users() {
        return $this->belongsToMany(User::class)->withTimestamps()->withPivot('redeemed_at');
    }
}
