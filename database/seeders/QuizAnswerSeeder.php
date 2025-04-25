<?php

namespace Database\Seeders;

use App\Models\QuizAnswer;
use Illuminate\Database\Seeder;

class QuizAnswerSeeder extends Seeder
{
    public function run()
    {
        QuizAnswer::create([
            'std_id' => 1, // Assuming student with id 1 exists
            'questions_id' => 1, // Assuming question with id 1 exists
            'point' => 1,
            'std_answer' => 'A',
            'correct_answer' => 'A',
            'lesson_id' => 1 // Assuming lesson with id 1 exists
        ]);
        QuizAnswer::create([
            'std_id' => 2, // Assuming student with id 2 exists
            'questions_id' => 2, // Assuming question with id 2 exists
            'point' => 0,
            'std_answer' => 'B',
            'correct_answer' => 'A',
            'lesson_id' => 2 // Assuming lesson with id 2 exists
        ]);
    }
}
