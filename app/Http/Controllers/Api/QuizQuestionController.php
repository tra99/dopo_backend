<?php
namespace App\Http\Controllers;

use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizQuestionController extends Controller
{
    public function index()
    {
        return QuizQuestion::with('lesson')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question_text'  => 'required|string',
            'type'           => 'required|string',
            'answer_options' => 'nullable|array',
            'lesson_id'      => 'required|exists:lessons,id',
            'description'    => 'nullable|string',
        ]);

        $qq = QuizQuestion::create($data);

        return response()->json($qq, 201);
    }

    public function show(QuizQuestion $quizQuestion)
    {
        return $quizQuestion->load('lesson', 'answers');
    }

    public function update(Request $request, QuizQuestion $quizQuestion)
    {
        $data = $request->validate([
            'question_text'  => 'sometimes|required|string',
            'type'           => 'sometimes|required|string',
            'answer_options' => 'nullable|array',
            'description'    => 'nullable|string',
        ]);

        $quizQuestion->update($data);

        return response()->json($quizQuestion);
    }

    public function destroy(QuizQuestion $quizQuestion)
    {
        $quizQuestion->delete();
        return response()->noContent();
    }
}
