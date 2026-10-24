<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['gym_id','name','description','price','stock','sold','is_active'];
    protected $casts = ['is_active' => 'boolean'];
}