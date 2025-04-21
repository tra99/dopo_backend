<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Enums\QuestionTypeEnum;
use App\Enums\SchoolTypeEnum;

class QuestionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {

            $perPage = min($request->input('limit', 15), 50); // Default 15, max 50
            $search = $request->input('search');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            // New filters
            $byCategoryId = $request->input('by_category_id');
            $byStandard = $request->input('by_standard_id');
            $bySchoolType = $request->input('by_school_type');

            $questions = Question::with(['category:id,title,parent_id'])
                ->with('evaluation_criterias')
                ->withCount('answers')
                ->when($search, function ($query) use ($search) {
                    $query->where('question', 'LIKE', "%{$search}%");
                })
                ->when($byStandard, function ($query) use ($byStandard) {
                    $query->whereHas('category', function ($q) use ($byStandard) {
                        $q->where('parent_id', $byStandard);
                    });
                })
                ->when($bySchoolType, function ($query) use ($bySchoolType) {
                    $query->where('school_type', $bySchoolType);
                })
                ->when($byCategoryId, function ($query) use ($byCategoryId) {
                    $query->where('category_id', $byCategoryId);
                })
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->appends($request->query());

            $questions->getCollection()->transform(function ($question) {
                $question->answer_option = json_decode($question->answer_option, true);
                $question->rule = json_decode($question->rule, true);
                return $question;
            });

            return response()->json($questions, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        try {
            $question = Question::with('category')
                ->withCount('answers')
                ->findOrFail($id);

            // Decode JSON fields if they are not null
            $question['answer_option'] = $question->answer_option ? json_decode($question->answer_option, true) : null;
            $question['rule'] = $question->rule ? json_decode($question->rule, true) : null;

            return response()->json($question);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានសំណួរនេះទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validate the data for multiple questions
            $validatedData = $request->validate([
                '*.id' => 'nullable|exists:questions,id',
                '*.category_id' => 'required|exists:categories,id',
                '*.question' => 'required|string|max:255',
                '*.description' => 'nullable|string',
                '*.question_type' => ['required', 'in:' . implode(',', QuestionTypeEnum::values())],
                '*.school_type' => ['required', 'string', 'exists:category_groups,school_type_en'],
                '*.answer_option' => 'sometimes|array|min:1',
                '*.rule' => 'sometimes|array|min:1',
                '*.answer_option.*.point' => 'required|integer|min:0',
                '*.answer_option.*.title' => 'required|string|max:255',
                '*.published_at' => 'sometimes|date|after:today',
            ]);

            foreach ($validatedData as $data) {
                if ($data['question_type'] == QuestionTypeEnum::RADIO->value && empty($data['answer_option'])) {
                    return response()->json(['error' => 'Radio questions must have at least one answer option.'], 400);
                }

                if ($data['question_type'] == QuestionTypeEnum::TEXT->value && empty($data['rule'])) {
                    return response()->json(['error' => 'Checkbox questions must have at least one rule.'], 400);
                }

                if (count($data['answer_option'] ?? []) > 0 && count($data['rule'] ?? []) > 0) {
                    return response()->json(['error' => 'Checkbox questions cannot have answer options and rules at the same time.'], 400);
                }
            }

            $questions = [];
            foreach ($validatedData as $data) {
                // Ensure 'answer_option' is properly encoded before saving
                $data['answer_option'] = isset($data['answer_option']) ? json_encode($data['answer_option'], JSON_UNESCAPED_UNICODE) : null;
                $data['rule'] = isset($data['rule']) ? json_encode($data['rule'], JSON_UNESCAPED_UNICODE) : null;

                // Check if the question has an id (i.e., update or create)
                if (isset($data['id'])) {
                    $question = Question::find($data['id']);
                    if ($question) {
                        // Remove the id before updating the question
                        unset($data['id']);
                        $question->update($data);
                    }
                } else {
                    // Create a new question if no 'id' is provided
                    $question = Question::create($data);
                }

                // Decode 'answer_option' after saving to ensure it's in the correct format
                $question->answer_option = json_decode($question->answer_option, true);
                $question->rule = json_decode($question->rule, true);

                // Store the updated question in the questions array
                $questions[] = $question;
            }

            DB::commit();
            // Return the array of created/updated questions
            return response()->json($questions, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {

            $question = Question::findOrFail($id);

            if (!$question) {
                return response()->json([
                    'message' => 'Question not found',
                ], 404);
            }

            $validatedData = $request->validate([
                'question' => 'sometimes|required|string|max:255',
                'description' => 'sometimes|nullable|string|max:1000',
                'category_id' => 'sometimes|required|exists:categories,id',
                'answer_option' => 'sometimes|array',
                'answer_option.*.title' => 'required|string|max:255',
                'answer_option.*.point' => 'required|integer|min:0',
                'question_type' => ['sometimes', 'required', 'in:' . implode(',', QuestionTypeEnum::values())],
                'published_at' => ['date', 'after:today'],
                'school_type' => 'sometimes|string|exists:category_groups,school_type_en',
                'rule' => 'sometimes|array',
            ], [
                'question.required' => 'The question field is required.',
                'question.max' => 'The question field must not exceed 255 characters.',
                'description.max' => 'The description field must not exceed 1000 characters.',
                'category_id.required' => 'The category_id is required.',
                'category_id.exists' => 'The selected category does not exist.',
                'answer_option.array' => 'The answer_option field must be an array.',
                'answer_option.*.title.required' => 'Each answer must have a value.',
                'answer_option.*.answer.max' => 'Each answer must not exceed 255 characters.',
                'answer_option.*.point.required' => 'Each answer must have a point.',
                'answer_option.*.point.integer' => 'Each point must be an integer.',
                'answer_option.*.point.min' => 'Each point must be at least 0.',
                'question_type.required' => 'The question_type field is required.',
                'question_type.in' => 'The question_type must be one of the following: ' . implode(', ', QuestionTypeEnum::values()),
            ]);


            if ($request->has('answer_option')) {
                $validatedData['answer_option'] = json_encode($validatedData['answer_option'], JSON_UNESCAPED_UNICODE);
            }

            $question->update($validatedData);

            return response()->json([
                'message' => 'Question updated successfully!',
                'question' => [
                    'id' => $question->id,
                    'question' => $question->question,
                    'description' => $question->description,
                    'category_id' => $question->category_id,
                    'answer_option' => json_decode($question->answer_option, true),
                    'rule' => json_decode($question->rule, true),
                    'question_type' => $question->question_type,
                    'created_at' => $question->created_at,
                    'updated_at' => $question->updated_at,
                ],
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានសំណួរនេះទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $question = Question::withCount('answers')->findOrFail($id);

            if ($question->answers_count > 0) {
                return response()->json(['error' => 'មិនអាចលុបសំណួរណាដែលមានចម្លើយបានទេ!'], 400);
            }

            $question->delete();

            return response()->json([
                'message' => 'លុបសំណួរដោយជោគជ័យ!',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានសំណួរនេះទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
