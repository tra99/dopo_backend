<?php

// app/Models/QuizQuestion.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    protected $table = 'quiz_questions';
    protected $fillable = [
        'question_text',
        'type',
        'answer_options',
        'lesson_id',
        'description',
    ];

    protected $casts = [
        'answer_options' => 'array',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'questions_id');
    }
}

