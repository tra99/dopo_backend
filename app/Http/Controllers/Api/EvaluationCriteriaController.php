<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EvaluationCriteria;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class EvaluationCriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $perPage = min($request->input('limit', 15), 50);


            $by_question_id = $request->input('by_question_id');

            $evaluationCriteria = EvaluationCriteria::
                when($by_question_id, function ($query) use ($by_question_id) {
                    $query->where('question_id', $by_question_id);
                })
                ->paginate($perPage)
                ->appends($request->query());
            return response()->json($evaluationCriteria);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $dataValidate = $request->validate(
                [
                    'title' => 'required|string|max:512',
                    'question_id' => 'required|exists:questions,id',
                    'options' => 'required|array|min:1',
                    'options.*.title' => 'required|string|max:255',
                    'options.*.point' => 'required|integer|min:0',
                ],
                [
                    'title.required' => 'ត្រូវការចំណងជើង (title)',
                    'question_id.required' => 'សូមបញ្ជាក់សំណួរ សម្រាប់ផ្ទៀងផ្ទាត់ (question_id)',
                    'question_id.exists' => 'សំណួរដែលផ្តល់ឲ្យមិនមាននៅក្នុងប្រព័ន្ធឡើយ! (question_id)',
                    'options.required' => 'សូមបញ្ជាក់ចម្លើយដែលអាចជ្រើសរើស (options)'
                ]
            );

            $dataValidate['options'] = json_encode($dataValidate['options']);
            $evaluationCriteria = EvaluationCriteria::create($dataValidate);

            return response()->json([
                'message' => 'បបង្កើតដោយជោគជ័យ',
                'evaluation_criteria' => $evaluationCriteria
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $evaluationCriteria = EvaluationCriteria::with('question')->findOrFail($id);
            return response()->json($evaluationCriteria);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ការវាយតម្លៃនេះ មិនមាននៅក្នុងប្រព័ន្ធទេ!',
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $evaluationCriteria = EvaluationCriteria::findOrFail($id);

            // dd($evaluationCriteria);
            $dataValidate = $request->validate(
                [
                    'title' => 'sometimes|string|max:512',
                    'question_id' => 'sometimes|exists:questions,id',
                    'options' => 'sometimes|array|min:1',
                    'options.*.title' => 'sometimes|string|max:255',
                    'options.*.point' => 'sometimes|integer|min:0',
                ],
                [
                    'question_id.exists' => 'សំណួរដែលផ្តល់ឲ្យមិនមាននៅក្នុងប្រព័ន្ធឡើយ! (question_id)',
                    'options.required' => 'សូមបញ្ជាក់ចម្លើយដែលអាចជ្រើសរើស (options)'
                ]
            );


            $evaluationCriteria->update($dataValidate);
            $evaluationCriteria->refresh();

            return response()->json([
                'message' => 'កែប្រែដោយជោគជ័យ',
                'evaluation_criteria' => $evaluationCriteria
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ការការផ្ទៀងផ្ទាត់សំណួរនេះ មិនមាននៅក្នុងប្រព័ន្ធទេ!',
            ], 404);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $evaluationCriteria = EvaluationCriteria::findOrFail($id);
            $evaluationCriteria->delete();
            return response()->json([
                'evaluation_criteria' => $evaluationCriteria,
                'message' => 'លុបចោលដោយជោគជ័យ'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'ការវាយតម្លៃនេះ មិនមាននៅក្នុងប្រព័ន្ធទេ!'], 404);
        }
    }
}
