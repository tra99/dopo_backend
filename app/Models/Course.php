<?php

// app/Models/Course.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'course';
    protected $fillable = [
        'title',
        'teacher_id',
        'completion_date',
        'description',
        'image',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(
            User::class,
            'course_student',
            'course_id',
            relatedPivotKey: 'std_id'
        )
            ->withPivot('completion_percentage')
            ->withTimestamps();
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'course_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'course_id');
    }
}
