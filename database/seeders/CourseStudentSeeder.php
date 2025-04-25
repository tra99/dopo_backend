<?php

namespace Database\Seeders;
use App\Models\CourseStudent;
use Illuminate\Database\Seeder;

class CourseStudentSeeder extends Seeder
{
    public function run()
    {
        CourseStudent::create([
            'std_id' => 1,  
            'course_id' => 1, 
            'completion_percentage' => 75
        ]);
        CourseStudent::create([
            'std_id' => 2,  
            'course_id' => 2,
            'completion_percentage' => 80
        ]);
    }
}
