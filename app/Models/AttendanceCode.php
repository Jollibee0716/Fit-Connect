<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCode extends Model
{
    protected $guarded = [];
    protected $casts = ['expires_at' => 'datetime', 'revoked_at' => 'datetime'];

    public function class()
    {
        return $this->belongsTo(GymClass::class, 'class_id');
    }
}