<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymOwner extends Model
{
    protected $fillable = ['user_id', 'gym_name', 'phone_number', 'gym_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gym()
    {
        return $this->hasOne(Gym::class);
    }
}