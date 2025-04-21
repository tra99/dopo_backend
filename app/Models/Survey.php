<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    // protected $casts = [
    //     'start_date' => 'datetime',
    //     'end_date' => 'datetime',
    // ];

    protected $fillable = [
        'title',
        'description',
        'is_published',
        'is_evaluated',
        'school_type',
        'start_date',
        'end_date'
    ];

    public function schoolSurvey()
    {
        return $this->hasMany(SchoolSurvey::class);
    }

    /**
     * Relationship: A Survey has many Answers
     */
    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}
