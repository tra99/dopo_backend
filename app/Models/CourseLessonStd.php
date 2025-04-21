<?php

// app/Models/CourseLessonStd.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLessonStd extends Model
{
    protected $table = 'course_lesson_std';
    protected $fillable = [
        'std_id',
        'lesson_id',
        'is_completed_quiz',
        'completed_at',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'std_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}

