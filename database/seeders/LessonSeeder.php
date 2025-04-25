<?php

namespace Database\Seeders;

use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run()
    {
        Lesson::create([
            'course_id' => 1, // Assuming course with id 1 exists
            'title' => 'Introduction to Laravel',
            'description' => 'This lesson covers the basics of Laravel.',
            'link_video' => 'https://www.youtube.com/watch?v=xyz',
            'end_date' => now()->addMonth(),
            'quiz_total_score' => 10
        ]);
        Lesson::create([
            'course_id' => 2, // Assuming course with id 2 exists
            'title' => 'Advanced Laravel Techniques',
            'description' => 'This lesson dives deep into advanced Laravel features.',
            'link_video' => 'https://www.youtube.com/watch?v=abc',
            'end_date' => now()->addMonths(2),
            'quiz_total_score' => 20
        ]);
    }
}
