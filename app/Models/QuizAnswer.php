<?php

// app/Models/QuizAnswer.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAnswer extends Model
{
    protected $table = 'quiz_answers';
    protected $fillable = [
        'std_id',
        'questions_id',
        'point',
        'std_answer',
        'correct_answer',
        'lesson_id',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'std_id');
    }

    public function question()
    {
        return $this->belongsTo(QuizQuestion::class, 'questions_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}

