<?php

namespace Database\Seeders;
use App\Models\CourseStudent;
use Illuminate\Database\Seeder;

class CourseStudentSeeder extends Seeder
{
    public function run()
    {
        CourseStudent::create([
            'std_id' => 1,  // Assuming student with id 1 exists
            'course_id' => 1, // Assuming course with id 1 exists
            'completion_percentage' => 75
        ]);
        CourseStudent::create([
            'std_id' => 2,  // Assuming student with id 2 exists
            'course_id' => 2, // Assuming course with id 2 exists
            'completion_percentage' => 80
        ]);
    }
}
