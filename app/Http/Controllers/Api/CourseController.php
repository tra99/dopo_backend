<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return Course::with('teacher')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'teacher_id'      => 'required|exists:users,id',
            'completion_date' => 'nullable|date',
            'description'     => 'nullable|string',
        ]);

        $course = Course::create($data);

        return response()->json($course, 201);
    }

    public function show(Course $course)
    {
        return $course->load('teacher', 'students', 'lessons');
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title'           => 'sometimes|required|string|max:255',
            'teacher_id'      => 'sometimes|required|exists:users,id',
            'completion_date' => 'nullable|date',
            'description'     => 'nullable|string',
        ]);

        $course->update($data);

        return response()->json($course);
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->noContent();
    }
}
