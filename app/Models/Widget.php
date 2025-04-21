<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    protected $fillable = [
        'id', 'dashboard_id', 'title', 
        'axis_x', 'axis_y', 'width', 'height', 
        'description', 'component', 'sort',
        'question', 'params'
    ];

    public function dashboard()
    {
        return $this->belongsTo(Dashboard::class);
    }
}
