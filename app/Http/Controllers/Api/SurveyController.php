<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Enums\SchoolTypeEnum;
use App\Http\Controllers\Controller;
use App\Mail\SurveyEmail;
use App\Models\CategoryGroups;
use App\Models\Question;
use App\Models\User;
use App\Services\FirebaseService;
use App\Models\Survey;
use App\Models\SchoolSurvey;
use App\Models\Notification;

class SurveyController extends Controller
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

            $perPage = $request->input('limit', 15);
            $search = $request->input('search');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $status = $request->input('by_status');
            $schoolType = $request->input('by_school_type');

            $surveys = Survey::withCount('answers')
                ->addSelect([
                    'questions_count' => Question::selectRaw('count(*)')
                        ->whereBetween('questions.published_at', [\DB::raw('surveys.start_date'), \DB::raw('surveys.end_date')])
                        ->whereColumn('questions.school_type', '=', 'surveys.school_type')
                ])
                ->when($search, function ($query) use ($search) {
                    $searchLower = mb_strtolower($search);
                    $query->whereRaw('LOWER(title) LIKE ?', ["%{$searchLower}%"]);
                })
                ->when($startDate, function ($query) use ($startDate) {
                    $query->whereDate('start_date', '>=', $startDate);
                })
                ->when(!is_null($status), function ($query) use ($status) {
                    $query->where('is_published', $status);
                })
                ->when($endDate, function ($query) use ($endDate) {
                    $query->whereDate('end_date', '<=', $endDate);
                })
                ->when($schoolType, function ($query) use ($schoolType) {
                    $query->where('school_type', $schoolType);  // Apply school type filter
                })
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->appends($request->query());

            return response()->json($surveys);
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

            $user = $request->user();

            $params = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'sometimes|string|max:1000',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'school_type' => ['required', 'in:' . implode(',', SchoolTypeEnum::values())],
                'is_published' => 'sometimes|boolean',
                'is_evaluated' => 'sometimes|boolean',
            ], [
                'title.required' => 'ត្រូវការគោលបំណងនៃការស្រង់ទិន្នន័យ',
                'start_date.required' => 'ត្រូវការពេលចាប់ផ្តើមស្រង់ទិន្នន័យ (start_date)',
                'end_date.required' => 'ត្រូវការពេលបញ្ចប់ស្រង់ទិន្នន័យ (end_date)',
                'start_date.date_format' => 'ទម្រង់ថ្ងៃខែឆ្នាំដែលទទួលយកបានគឺ 21/02/2025',
                'end_date.date_format' => 'ទម្រង់ថ្ងៃខែឆ្នាំដែលទទួលយកបានគឺ 21/02/2025',
                'start_date.after' => 'ថ្ងៃចាប់ផ្តើមគឺត្រូវតែមិនទាន់មកដល់',
                'end_date.after' => 'ថ្ងៃបញ្ចប់គឺត្រូវតែបន្ទាប់ពីថ្ងៃចាប់ផ្តើម',
                'description' => 'បរិយាយត្រូវតែជាអក្សរ',
            ]);


            $params['description'] = $request->input('description', null);
            $params['is_published'] = $request->input('is_published', 0);
            $params['is_evaluated'] = $request->input('is_evaluated', 0);


            // check survey title is unique
            // if (!Survey::where('title', $params['title'])->exists()) {
            $survey = Survey::create([
                'title' => $params['title'],
                'start_date' => $params['start_date'],
                'school_type' => $params['school_type'],
                'end_date' => $params['end_date'],
                'is_published' => $params['is_published'],
                'description' => $params['description'],
            ]);

            if ($params['is_published'] == 1 || $params['is_published'] == true) {
                $user = User::with('roles')->whereHas('roles', function ($query) {
                    $query->where('roles.id', 1);
                })
                    ->get();
                $user->each(function ($user) use ($survey) {
                    if ($user != auth()->user()) {

                        $notification = Notification::create([
                            'user_id' => $user->id,
                            'action' => 'បង្កើតការវាយតម្លៃថ្មី',
                            'title' => 'ការវាយតម្លៃថ្មីត្រូវធ្វើនៅថ្ងៃ ' . $survey->start_date,
                            'message' => $survey->title,
                            'link' => 'assessment/survey/' . $survey->id,
                        ]);

                        // Send notification to google firebase
                        if ($user->fcm_token) {
                            $this->firebaseService->sendNotification($user->fcm_token, 'ការវាយតម្លៃថ្មីត្រូវធ្វើនៅថ្ងៃ ' . $survey->start_date, $survey->title, $notification->toArray());
                        }

                        // Send notitification to email
                        $schoolType = CategoryGroups::where('school_type_en', $survey->school_type)->first();
                        $survey_notification = [
                            'title' => $survey->title,
                            'start_date' => $survey->start_date,
                            'end_date' => $survey->end_date,
                            'school_type' => $schoolType->school_type_kh,
                            'description' => $survey->description,
                        ];

                        Mail::to($user->email)->send(new SurveyEmail($survey_notification));
                    }
                });
            }

            return response()->json([
                'survey' => $survey,
                'school_survey' => $schoolSurvey ?? null
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
        $survey = Survey::with('answers')
            ->findOrFail($id);

        // get question that has published_at in the survey start_date to end_date
        $question = Question::whereBetween('published_at', [$survey->start_date, $survey->end_date])
            ->where('school_type', '=', $survey->school_type)
            ->get();
        $survey['questions'] = $question;

        return response()->json([
            'survey' => $survey
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = auth()->user();
            $survey = Survey::findOrFail($id);

            $params = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|nullable|string|max:1000',
                'is_published' => 'sometimes|boolean',
                'is_self_evalution' => 'sometimes|boolean',
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date|after_or_equal:start_date',
                'school_type' => ['sometimes', 'in:' . implode(',', SchoolTypeEnum::values())],
            ],);

            if ($request->has('start_date')) {
                $params['start_date'] = Carbon::parse($request->input('start_date'))->format('Y-m-d');
                if (Carbon::parse($params['start_date'])->lt(Carbon::today())) {
                    return response()->json(['error' => 'Start date must be today or in the future.'], 422);
                }
            }

            if ($request->has('end_date')) {
                $params['end_date'] = Carbon::parse($request->input('end_date'))->format('Y-m-d');
            }

            $survey->update($params);


            // check is_published And Send notification
            if ($params['is_published'] == 1 || $params['is_published'] == true) {
                $user = User::with('roles')->whereHas('roles', function ($query) {
                    $query->where('roles.id', 1);
                })->whereNotNull('fcm_token')
                    ->get();

                $user->each(function ($user) use ($survey) {
                    if ($user != auth()->user()) {

                        Notification::create([
                            'user_id' => $user->id,
                            'action' => 'ការវាយតម្លៃដាក់ជាសាធារណៈ',
                            'title' => 'ការវាយតម្លៃថ្មីត្រូវធ្វើនៅថ្ងៃ ' . $survey->start_date,
                            'message' => $survey->title,
                            'link' => 'assessment/survey/' . $survey->id,
                        ]);

                        // Send notification to google firebase
                        if ($user->fcm_token) {
                            $this->firebaseService->sendNotification($user->fcm_token, 'New Survey was public', $survey->title, $survey->toArray());
                        }

                        // Send notitification to email
                        $survey_notification = [
                            'title' => $survey->title,
                            'start_date' => $survey->start_date,
                            'end_date' => $survey->end_date,
                            'school_type' => $survey->school_type,
                            'description' => $survey->description,
                        ];

                        Mail::to($user->email)->send(new SurveyEmail($survey_notification));
                    }
                });
            }

            // check is_evaluated for own school
            $params['is_self_evalution'] = $request->input('is_self_evalution', 0);
            if ($params['is_self_evalution'] == 1 || $params['is_self_evalution'] == true) {
                $schoolSurvey = SchoolSurvey::where('survey_id', $survey->id)->where('school_id', $user->school_id)->first();
                if (!$schoolSurvey) {
                    $schoolSurvey = SchoolSurvey::create([
                        'school_id' => $user->school_id,
                        'survey_id' => $survey->id,
                        // 'is_self_evalution' => $params['is_self_evalution'],
                    ]);
                } else {
                    $schoolSurvey->update([
                        'school_id' => $user->school_id,
                        'survey_id' => $survey->id,
                        // 'is_self_evalution' => $params['is_self_evalution'],
                    ]);
                    $schoolSurvey->save();
                }
            }

            return response()->json([
                'message' => 'Survey updated successfully!',
                'survey' => $survey,
                // 'school_survey' => $schoolSurvey ?? null
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Step 1: Retrieve the survey and its associated answers
            $survey = Survey::with('answers')->findOrFail($id);

            // Step 2: Check if the survey has any associated answers
            if ($survey->answers->count() > 0) {
                // If there are answers, prevent deletion and return a message
                return response()->json(['error' => 'Cannot delete survey because it has associated answers.'], 400);
            }

            // Step 3: If no answers, delete the survey
            $survey->delete();

            // Step 4: Return success response after deletion
            return response()->json(['message' => 'Survey deleted successfully']);
        } catch (\Exception $e) {
            // Step 5: Handle any exceptions and return the error message
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
