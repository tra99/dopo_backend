<?php
namespace App\Http\Controllers;

use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class QuizAnswerController extends Controller
{
    public function index()
    {
        return QuizAnswer::with(['student', 'question', 'lesson'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'std_id'         => 'required|exists:users,id',
            'questions_id'   => 'required|exists:quiz_questions,id',
            'point'          => 'required|integer',
            'std_answer'     => 'nullable|string',
            'correct_answer' => 'nullable|string',
            'lesson_id'      => 'required|exists:lessons,id',
        ]);

        $qa = QuizAnswer::create($data);

        return response()->json($qa, 201);
    }

    public function show(QuizAnswer $quizAnswer)
    {
        return $quizAnswer->load(['student', 'question', 'lesson']);
    }

    public function update(Request $request, QuizAnswer $quizAnswer)
    {
        $data = $request->validate([
            'point'          => 'integer',
            'std_answer'     => 'nullable|string',
            'correct_answer' => 'nullable|string',
        ]);

        $quizAnswer->update($data);

        return response()->json($quizAnswer);
    }

    public function destroy(QuizAnswer $quizAnswer)
    {
        $quizAnswer->delete();
        return response()->noContent();
    }
}
