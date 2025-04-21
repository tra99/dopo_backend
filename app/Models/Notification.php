<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'message',
        'title',
        'link',
        'is_read',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
