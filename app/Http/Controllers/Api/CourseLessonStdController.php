<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseLessonStd;
use Illuminate\Http\Request;

class CourseLessonStdController extends Controller
{
    public function index()
    {
        return CourseLessonStd::with(['student', 'lesson'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'std_id' => 'required|exists:users,id',
            'lesson_id' => 'required|exists:lessons,id',
            'is_completed_quiz' => 'boolean',
            'completed_at' => 'nullable|date',
        ]);

        $cls = CourseLessonStd::create($data);

        return response()->json($cls, 201);
    }

    public function show(CourseLessonStd $courseLessonStd)
    {
        return $courseLessonStd->load('student', 'lesson');
    }

    public function update(Request $request, CourseLessonStd $courseLessonStd)
    {
        $data = $request->validate([
            'is_completed_quiz' => 'boolean',
            'completed_at' => 'nullable|date',
        ]);

        $courseLessonStd->update($data);

        return response()->json($courseLessonStd);
    }

    public function destroy(CourseLessonStd $courseLessonStd)
    {
        $courseLessonStd->delete();
        return response()->noContent();
    }
}
