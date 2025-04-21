<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Answer extends Model
{
    use HasFactory;
    protected $fillable = ['id', 'question_id', 'school_id', 'survey_id', 'answer', 'point', 'user_id', 'school_survey_id', 'created_at', 'updated_at'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id')
            ->select(['id', 'question', 'description', 'category_id'])
        ;
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class, 'survey_id')
            ->select(['surveys.id', 'surveys.title', 'surveys.start_date', 'surveys.end_date']);
    }

    public function schoolSurvey()
    {
        return $this->belongsTo(SchoolSurvey::class, 'school_survey_id');
    }
}
