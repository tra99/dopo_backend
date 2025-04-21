<?php

// app/Models/Certificate.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificate';
    protected $fillable = [
        'user_id',
        'course_id',
        'date_of_certificate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}

