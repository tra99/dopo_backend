<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSurvey extends Model
{
    protected $table = 'school_survey';
    protected $fillable = ['school_id', 'survey_id', 'is_self_evalution', 'date', 'start_time', 'interviewer', 'organization', 'interviewer_phone', 'score'];
    public $timestamps = false;

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
