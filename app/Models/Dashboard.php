<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    protected $fillable = ['id', 'dashboard_type', 'title', 'description', 'is_draggable', 'is_resizable', 'is_bounded', 'dashboard', 'parent_id', 'icon', 'sort', 'params'];

    public function widgets()
    {
        return $this->hasMany(Widget::class);
    }
}
