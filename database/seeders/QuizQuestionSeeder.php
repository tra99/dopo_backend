<?php

namespace Database\Seeders;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;

class QuizQuestionSeeder extends Seeder
{
    public function run()
    {
        QuizQuestion::create([
            'question_text' => 'What is Laravel?',
            'type' => 'multiple_choice',
            'answer_options' => json_encode([
                ['ans' => 'A', 'point' => 1],
                ['ans' => 'B', 'point' => 0],
                ['ans' => 'C', 'point' => 0]
            ]),
            'lesson_id' => 1, // Assuming lesson with id 1 exists
            'description' => 'Basic question on Laravel framework.'
        ]);
        QuizQuestion::create([
            'question_text' => 'What is Eloquent?',
            'type' => 'multiple_choice',
            'answer_options' => json_encode([
                ['ans' => 'A', 'point' => 1],
                ['ans' => 'B', 'point' => 0],
                ['ans' => 'C', 'point' => 0]
            ]),
            'lesson_id' => 2, // Assuming lesson with id 2 exists
            'description' => 'A question on Eloquent ORM.'
        ]);
    }
}
