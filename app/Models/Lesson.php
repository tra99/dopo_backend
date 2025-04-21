<?php

// app/Models/Lesson.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lessons';
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'link_video',
        'end_date',
        'quiz_total_score',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'course_lesson_std',
            'lesson_id',
            'std_id'
        )
        ->withPivot(['is_completed_quiz', 'completed_at'])
        ->withTimestamps();
    }

    public function statuses()
    {
        return $this->hasMany(CourseLessonStd::class, 'lesson_id');
    }

    public function quizQuestions()
    {
        return $this->hasMany(QuizQuestion::class, 'lesson_id');
    }
}

