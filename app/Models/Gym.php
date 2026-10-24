<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gym extends Model
{
    protected $fillable = ['gym_owner_id', 'name', 'address', 'phone'];

    public function owner()
    {
        return $this->belongsTo(GymOwner::class, 'gym_owner_id');
    }
}