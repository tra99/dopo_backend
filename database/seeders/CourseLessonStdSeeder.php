<?php

namespace Database\Seeders;

use App\Models\CourseLessonStd;
use Illuminate\Database\Seeder;

class CourseLessonStdSeeder extends Seeder
{
    public function run()
    {
        CourseLessonStd::create([
            'std_id' => 1, // Assuming student with id 1 exists
            'lesson_id' => 1, // Assuming lesson with id 1 exists
            'is_completed_quiz' => true,
            'completed_at' => now()
        ]);
        CourseLessonStd::create([
            'std_id' => 2, // Assuming student with id 2 exists
            'lesson_id' => 2, // Assuming lesson with id 2 exists
            'is_completed_quiz' => false,
            'completed_at' => null
        ]);
    }
}
