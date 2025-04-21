<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissionSchool extends Model
{
    protected $table = 'mission_school';
    protected $fillable = ['id', 'mission_id', 'school_id', 'evaluation_scores', 'report_uri', 'attendance_uri', 'assessment_uri', 'slide_uri', 'perspective', 'conclusion', 'appendix'];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
