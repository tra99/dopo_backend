<?php
namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function index()
    {
        return Lesson::with('course')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id'        => 'required|exists:course,id',
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string',
            'link_video'       => 'nullable|url',
            'end_date'         => 'nullable|date',
            'quiz_total_score' => 'integer|min:0',
        ]);

        $lesson = Lesson::create($data);

        return response()->json($lesson, 201);
    }

    public function show(Lesson $lesson)
    {
        return $lesson->load('course', 'quizQuestions', 'students');
    }

    public function update(Request $request, Lesson $lesson)
    {
        $data = $request->validate([
            'title'            => 'sometimes|required|string|max:255',
            'description'      => 'nullable|string',
            'link_video'       => 'nullable|url',
            'end_date'         => 'nullable|date',
            'quiz_total_score' => 'integer|min:0',
        ]);

        $lesson->update($data);

        return response()->json($lesson);
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();
        return response()->noContent();
    }
}
