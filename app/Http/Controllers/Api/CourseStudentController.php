<?php
namespace App\Http\Controllers;

use App\Models\CourseStudent;
use Illuminate\Http\Request;

class CourseStudentController extends Controller
{
    public function index()
    {
        return CourseStudent::with(['student', 'course'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'std_id'               => 'required|exists:users,id',
            'course_id'            => 'required|exists:course,id',
            'completion_percentage'=> 'integer|min:0|max:100',
        ]);

        $cs = CourseStudent::create($data);

        return response()->json($cs, 201);
    }

    public function show(CourseStudent $courseStudent)
    {
        return $courseStudent->load('student', 'course');
    }

    public function update(Request $request, CourseStudent $courseStudent)
    {
        $data = $request->validate([
            'completion_percentage'=> 'integer|min:0|max:100',
        ]);

        $courseStudent->update($data);

        return response()->json($courseStudent);
    }

    public function destroy(CourseStudent $courseStudent)
    {
        $courseStudent->delete();
        return response()->noContent();
    }
}
