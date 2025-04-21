<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\SchoolEvaluationEmail;
use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Notification;
use App\Models\School;
use App\Models\SchoolSurvey;
use App\Models\Survey;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Mail;

class AnswerController extends Controller
{
    protected $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        try {
            // Get query parameters with default values
            $perPage = $request->input('limit', 15);
            $search = $request->input('search');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            // New filters
            $byUserId = $request->input('by_user_id');
            $bySurveyId = $request->input('by_survey_id'); // Filter through the question relationship
            $byQuestionId = $request->input('by_question_id');
            $bySchoolId = $request->input('by_school_id');

            // Ensure safe sorting by checking allowed columns
            $allowedSortColumns = ['id', 'question_id', 'user_id', 'points', 'created_at'];
            if (!in_array($sortBy, $allowedSortColumns)) {
                $sortBy = 'created_at';
            }

            // Query answers with optional filters
            $answers = Answer::when($search, function ($query) use ($search) {
                $query->whereHas('question', function ($q) use ($search) {
                    $q->where('question', 'LIKE', "%{$search}%");
                });
            })
                ->when($byUserId, function ($query) use ($byUserId) {
                    $query->where('user_id', $byUserId);
                })
                ->when($bySurveyId, function ($query) use ($bySurveyId) {
                    $query->whereHas('question', function ($q) use ($bySurveyId) {
                        $q->where('survey_id', $bySurveyId); // Filter survey_id via the question table
                    });
                })
                ->when($byQuestionId, function ($query) use ($byQuestionId) {
                    $query->where('question_id', $byQuestionId);
                })
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->appends($request->query());

            // Decode JSON answer options before returning
            $answers->getCollection()->transform(function ($answer) {
                $answer->answer = json_decode($answer->answer, true);
                return $answer;
            });

            DB::commit();
            return response()->json($answers, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Store a new answer.
     */
    public function storeAndUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            // Validate the request data
            $validatedData = $request->validate(
                [
                    'id' => 'nullable|integer|exists:school_survey,id',
                    'is_self_evaluation' => 'required|boolean',
                    'survey_id' => 'required|integer|exists:surveys,id',
                    'school_id' => 'required|integer|exists:schools,id',
                    'date' => 'required|date',
                    'start_time' => 'nullable|required_if:is_self_evaluation,false|string',
                    'interviewer' => 'nullable|required_if:is_self_evaluation,false|string',
                    'organization' => 'nullable|required_if:is_self_evaluation,false|string',
                    'interviewer_phone' => 'nullable|required_if:is_self_evaluation,false|string',
                    'answers.*.question_id' => 'required|exists:questions,id',
                    'answers.*.survey_id' => 'required|exists:surveys,id',
                    'answers.*.answers' => 'required|array|min:1',
                    'answers.*.answers.*.title' => 'required|string|max:255',
                    'answers.*.answers.*.point' => 'required|integer|min:0',
                ],
                []
            );

            $answers = $validatedData['answers'];
            unset($validatedData['answers']);
            // create school survey for interviewer
            if (isset($validatedData['id'])) {
                $school_survey = SchoolSurvey::find($validatedData['id']);
                $school_survey->update($validatedData);
                $school_survey->refresh();
            } else {
                $school_survey = SchoolSurvey::create($validatedData);
            }


            $response = [];

            // Loop through each answer data from the validated data
            foreach ($answers as $data) {
                // Check if answers exist before processing
                if (empty($data['answers'])) {
                    return response()->json(['error' => 'Answers cannot be empty.'], 422);
                }

                // Recalculate total points
                $totalPoints = array_sum(array_column($data['answers'], 'point'));

                // Encode the answers to JSON
                $encodedAnswers = json_encode($data['answers'], JSON_UNESCAPED_UNICODE);

                if (!$encodedAnswers) { // Check if encoding failed
                    return response()->json(['error' => 'Failed to encode answers.'], 500);
                }

                // Ensure 'survey_id' is present and not null
                if (empty($data['survey_id'])) {
                    return response()->json(['error' => 'survey_id is required and cannot be null.'], 422);
                }

                // Create or update answer based on 'id' presence
                if (isset($data['id'])) {
                    // Update existing answer
                    $answer = Answer::findOrFail($data['id']);
                    $answer->update([
                        'question_id' => $data['question_id'],
                        'survey_id' => $data['survey_id'],
                        'user_id' => $user->id,
                        'answer' => $encodedAnswers,
                        'point' => $totalPoints,
                        'school_survey_id' => $school_survey->id,
                    ]);
                } else {
                    // Create new answer
                    $answer = Answer::create([
                        'question_id' => $data['question_id'],
                        'survey_id' => $data['survey_id'],
                        'user_id' => $user->id,
                        'answer' => $encodedAnswers,
                        'point' => $totalPoints,
                        'school_survey_id' => $school_survey->id,
                    ]);
                }

                // Decode JSON answer options before returning
                $answer->answer = json_decode($answer->answer, true);
                $response[] = $answer;
            }

            DB::commit();
            $school_survey->refresh();
            // send notification
            $user = User::with('roles')->whereHas('roles', function ($query) {
                $query->where('roles.id', 1);
            })->get();

            $user->each(function ($user) use ($school_survey) {
                $school = School::find($school_survey->school_id);
                $survey = Survey::find($school_survey->survey_id);

                if ($user != auth()->user()) {
                    $title = 'ទទួលបានការវាយតម្លៃពី ' . $school->school_type_kh . $school->school_name;
                    $message = 'សាលារៀន' . $school->school_type_kh . $school->school_name_kh . 'បានឆ្លើយសំណួរនៃបញ្ជីវាយតម្លៃ ' . $survey->title . 'ទទួលបានពិន្ទុ ' . $school_survey->score;

                    $notification = Notification::create([
                        'user_id' => $user->id,
                        'action' => 'ទទួលបានការវាយតម្លៃពីសាលារៀន',
                        'title' => $title,
                        'message' => $message,
                        'link' => '',
                    ]);

                    // Send notification to google firebase
                    if ($user->fcm_token) {
                        $this->firebaseService->sendNotification($user->fcm_token, $message, $title, $notification->toArray());
                    }

                    // Send notitification to email
                    // $schoolType = CategoryGroups::where('school_type_en', $survey->school_type)->first();
                    $school_evaluation = [
                        'survey_title' => $title,
                        'start_date' => $survey->start_date,
                        'end_date' => $survey->end_date,
                        'school_name_kh' => $school->school_name_kh,
                        'school_type_kh' => $school->school_type_kh,
                        'province_kh' => $school->province_kh,
                        'score' => $school_survey->score,
                    ];

                    Mail::to($user->email)->send(new SchoolEvaluationEmail($school_evaluation));
                }
            });

            // Return success response with all processed answers
            return response()->json([
                'message' => 'ជោគជ័យ',
                'school_survey' => $school_survey,
                'answers' => $response
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            // Handle unexpected errors
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Show a single answer.
     */
    public function show($id)
    {
        try {
            $answer = Answer::with(['question:id,question', 'survey'])
                ->findOrFail($id);

            // Decode answer JSON before returning
            $answer->answer = json_decode($answer->answer, true);

            return response()->json($answer);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Answer not found.'], 404);
        }
    }

    /**
     * Update an existing answer.
     */
    public function update(Request $request, $id)
    {
        try {
            $answer = Answer::findOrFail($id);

            $validatedData = $request->validate([
                'answers' => 'sometimes|array|min:1',
                'answers.*.title' => 'required|string|max:255',
                'answers.*.point' => 'required|integer|min:0',
            ]);

            if ($request->has('answers')) {
                // Recalculate total points
                $totalPoints = array_sum(array_column($validatedData['answers'], 'point'));

                // Encode answers to JSON
                $encodedAnswers = json_encode($validatedData['answers'], JSON_UNESCAPED_UNICODE);
                if (!$encodedAnswers) {
                    return response()->json(['error' => 'Failed to encode answers.'], 500);
                }

                // Update the answer
                $answer->answer = $encodedAnswers;
                $answer->point = $totalPoints;
            }

            $answer->save();

            return response()->json([
                'message' => 'Answer updated successfully!',
                'answer' => [
                    'id' => $answer->id,
                    'question_id' => $answer->question_id,
                    'user_id' => $answer->user_id,
                    'answer' => json_decode($answer->answer, true),
                    'point' => $answer->point,
                    'updated_at' => $answer->updated_at,
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Answer not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Delete an answer.
     */
    public function destroy($id)
    {
        try {
            $answer = Answer::findOrFail($id);
            $answer->delete();

            return response()->json(['message' => 'Answer deleted successfully!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Answer not found.'], 404);
        }
    }
}
