<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class School extends Model
{
    use HasFactory, Notifiable;
    public $timestamps = true;

    public function missions()
    {
        return $this->belongsToMany(Mission::class, 'mission_school', 'school_id', 'mission_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }
}
