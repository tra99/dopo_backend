<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{

    protected $casts = [
        'options' => 'array',
    ];
    
    protected $fillable = [
        'id', 'name', 'options'
    ];
}
