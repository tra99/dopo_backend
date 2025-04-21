<?php

// app/Models/CourseStudent.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseStudent extends Model
{
    protected $table = 'course_student';
    protected $fillable = [
        'std_id',
        'course_id',
        'completion_percentage',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'std_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}

