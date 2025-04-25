<?php


namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        Course::create([
            'title' => 'Laravel Basics',
            'teacher_id' => 1, // Assuming user with id 1 exists
            'completion_date' => now()->addMonths(3),
            'description' => 'Learn the fundamentals of Laravel framework.'
        ]);
        Course::create([
            'title' => 'Advanced Laravel',
            'teacher_id' => 2, // Assuming user with id 2 exists
            'completion_date' => now()->addMonths(6),
            'description' => 'Master advanced features of Laravel including queues and broadcasting.'
        ]);
    }
}
