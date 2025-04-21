<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\MissionEmail;
use App\Mail\ReportTrackingEmail;
use App\Models\Mission;
use App\Models\MissionSchool;
use App\Models\Notification;
use App\Models\Participant;
use App\Services\FileService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;

class MissionController extends Controller
{
    protected $fileService, $firebaseService;

    public function __construct(
        FileService $fileService,
        // FirebaseService $firebaseService
    ) {
        $this->fileService = $fileService;
        // $this->firebaseService = $firebaseService;
    }

    /**
     * Get all missions.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('limit', 15), 50); // Default 15, max 50
            $search = $request->input('search');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            // New filters
            $bySchoolId = $request->input('by_school_id');
            $byStatus = $request->input('by_status');
            $byParticipant = $request->input('by_participants_id');

            $missions = Mission::with('schools', 'participants')
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('purpose', 'LIKE', "%{$search}%");
                    });
                })
                ->when($start_date, function ($query) use ($start_date) {
                    $query->whereDate('start_date', '>=', $start_date);
                })
                ->when($end_date, function ($query) use ($end_date) {
                    $query->whereDate('end_date', '<=', $end_date);
                })
                ->when($bySchoolId, function ($query) use ($bySchoolId) {
                    $query->whereHas('schools', function ($q) use ($bySchoolId) {
                        $q->where('schools.id', $bySchoolId);
                    });
                })
                ->when($byStatus, function ($query) use ($byStatus) {
                    $query->where('status', $byStatus);
                })
                ->when($byParticipant, function ($query) use ($byParticipant) {
                    $query->whereHas('participants', function ($q) use ($byParticipant) {
                        $q->where('participants.id', $byParticipant);
                    });
                })
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->appends($request->query());

            return response()->json($missions);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created mission.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    'purpose' => 'required|string',
                    'school_ids' => 'required|array',
                    'school_ids.*' => 'exists:schools,id',
                    'start_date' => ['required', 'date'],
                    'end_date' => ['required', 'date', 'after:start_date'],
                    'participant_ids' => 'required|array',
                    'participant_ids.*' => 'exists:participants,id',
                    'description' => 'string',
                ],
                [
                    'purpose.required' => 'ត្រូវការគោលបំណងរបស់បេសកកម្ម (purpose)',
                    'school_ids.required' => 'សូមបញ្ជាក់សាលារៀន ដែលបេសកកម្មត្រូវចុះទៅ (school_ids)',
                    'school_ids.exists' => 'សាលារៀនដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    'participant_ids.required' => 'សូមផ្តល់អ្នកចុះគាំទ្រ ក្នុងបេសកកម្មនេះ',
                    'participant_ids.exists' => 'អ្នកចុះគាំទ្រដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    'start_date.required' => 'ត្រូវការពេលចាប់ផ្តើមរបស់បេសកកម្ម (start_date)',
                    'end_date.required' => 'ត្រូវការពេលបញ្ចប់របស់បេសកកម្ម (end_date)',
                    'start_date.date_format' => 'ទម្រង់ថ្ងៃខែឆ្នាំដែលទទួលយកបានគឺ 21/02/2025',
                    'end_date.date_format' => 'ទម្រង់ថ្ងៃខែឆ្នាំដែលទទួលយកបានគឺ 21/02/2025',
                    // 'start_date.after' => 'ថ្ងៃចាប់ផ្តើមគឺត្រូវតែមិនទាន់មកដល់',
                    'end_date.after' => 'ថ្ងៃបញ្ចប់គឺត្រូវតែបន្ទាប់ពីថ្ងៃចាប់ផ្តើម',
                    'description' => 'បរិយាយត្រូវតែជាអក្សរ'
                ]
            );

            // Extra validation, 
            // -- make sure all given schools are in the same province
            $mission = Mission::create($validatedData);

            // Attach schools to the mission
            $mission->schools()->attach($validatedData['school_ids']);
            // Attach participants to the mission
            $mission->participants()->attach($validatedData['participant_ids']);
            $mission->load('schools');

            // Notify the participants
            foreach ($validatedData['participant_ids'] as $participant_id) {
                $is_email_send = false;

                $participant = Participant::with('user')->find($participant_id);


                if ($participant->user || $participant->email) {
                    // send notification to email
                    $missionData = [
                        'start_date'  => $mission->start_date,
                        'end_date'    => $mission->end_date,
                        'purpose'     => $mission->purpose,
                        'description' => $mission->description,
                        'schools'     => collect($mission['schools'])->pluck('school_name_kh')->toArray()
                    ];


                    if ($participant->email) {
                        $is_email_send = true;
                        Mail::to($participant->email)->send(new MissionEmail($missionData));
                    }

                    // send notification to google fcm
                    if ($participant->user) {
                        $notification = Notification::create([
                            'user_id' => $participant->user_id,
                            'action' => 'មានបេសកម្មថ្មី',
                            'title' => 'បេសកកម្មថ្មីនៅថ្ងៃ ' . $mission->start_date,
                            'message' => $mission->purpose,
                            'link' => 'patronage/missions/' . $mission->id
                        ]);

                        if ($participant->user->fcm_token) {
                            $this->firebaseService->sendNotification($participant->user->fcm_token, 'បេសកកម្មថ្មីនៅថ្ងៃ ' . $mission->start_date, $mission->purpose, $notification->toArray());
                        }

                        // check if email not yet send
                        if (!$is_email_send) {
                            Mail::to($participant->user->email)->send(new MissionEmail($missionData));
                        }
                    }
                }
            }
            return response()->json([
                'message' => 'បេសកកម្មត្រូវបានបង្កើតដោយជោគជ័យ',
                'mission' => $mission,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * return the specified mission
     */
    public function show(string $id)
    {
        $mission = Mission::with('schools', 'participants', 'missionSchools')->findOrFail($id);
        return response()->json($mission);
    }

    /**
     * Update the specified mission.
     */
    public function update(Request $request, string $id)
    {
        try {
            $mission = Mission::with('participants')->findOrFail($id);
            $oldParticipants = $mission->participants->pluck('id')->toArray();;

            $validatedData = $request->validate(
                [
                    'purpose' => 'required|string',
                    'start_date' => ['required', 'date'],
                    'end_date' => ['required', 'date', 'after:start_date'],
                    'description' => 'string'
                ],
                [
                    'purpose.required' => 'ត្រូវការគោលបំណងរបស់បេសកកម្ម (purpose)',
                    'start_date.required' => 'ត្រូវការពេលចាប់ផ្តើមរបស់បេសកកម្ម (start_date)',
                    'end_date.required' => 'ត្រូវការពេលបញ្ចប់របស់បេសកកម្ម (end_date)',
                    'start_date.date_format' => 'ទម្រង់ថ្ងៃខែឆ្នាំដែលទទួលយកបានគឺ 21/02/2025',
                    'end_date.date_format' => 'ទម្រង់ថ្ងៃខែឆ្នាំដែលទទួលយកបានគឺ 21/02/2025',
                    // 'start_date.after' => 'ថ្ងៃចាប់ផ្តើមគឺត្រូវតែមិនទាន់មកដល់',
                    'end_date.after' => 'ថ្ងៃបញ្ចប់គឺត្រូវតែបន្ទាប់ពីថ្ងៃចាប់ផ្តើម',
                    'description' => 'បរិយាយត្រូវតែជាអក្សរ'
                ]
            );

            $mission->update($validatedData);
            if ($request->get('school_ids')) {
                $validatedData = $request->validate(
                    [
                        'school_ids' => 'required|array',
                        'school_ids.*' => 'exists:schools,id',
                    ],
                    [
                        'school_ids.required' => 'សូមបញ្ជាក់សាលារៀន ដែលបេសកកម្មត្រូវចុះទៅ (school_ids)',
                        'school_ids.exists' => 'សាលារៀនដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    ]
                );
                $mission->schools()->sync($validatedData['school_ids']);
            }
            if ($request->get('participant_ids')) {
                $validatedData = $request->validate(
                    [
                        'participant_ids' => 'required|array',
                        'participant_ids.*' => 'exists:participants,id',
                    ],
                    [
                        'participant_ids.required' => 'សូមផ្តល់អ្នកចុះគាំទ្រ ក្នុងបេសកកម្មនេះ',
                        'participant_ids.exists' => 'អ្នកចុះគាំទ្រដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    ]
                );
                $mission->participants()->sync($validatedData['participant_ids']);
            }
            $mission->refresh();
            $newParticipants = $mission->participants->pluck('id')->toArray();
            foreach (array_diff($newParticipants, $oldParticipants) as $participant_id) {
                $is_email_send = false;

                $participant = Participant::with('user')->find($participant_id);
                if ($participant->user || $participant->email) {
                    // send notification to email
                    $missionData = [
                        'start_date'  => $mission->start_date,
                        'end_date'    => $mission->end_date,
                        'purpose'     => $mission->purpose,
                        'description' => $mission->description,
                        'schools'     => collect($mission['schools'])->pluck('school_name_kh')->toArray()
                    ];

                    if ($participant->email) {
                        $is_email_send = true;
                        Mail::to($participant->email)->send(new MissionEmail($missionData));
                    }

                    // send notification to google fcm
                    if ($participant->user) {
                        $notification = Notification::create([
                            'user_id' => $participant->user_id,
                            'action' => 'មានបេសកម្មថ្មី',
                            'title' => 'បេសកកម្មថ្មីនៅថ្ងៃ ' . $mission->start_date,
                            'message' => $mission->purpose,
                            'link' => 'patronage/missions/' . $mission->id
                        ]);

                        if ($participant->user->fcm_token) {
                            $this->firebaseService->sendNotification($participant->user->fcm_token, 'បេសកកម្មថ្មីនៅថ្ងៃ ' . $mission->start_date, $mission->purpose, $notification->toArray());
                        }

                        // check if email not yet send
                        if (!$is_email_send) {
                            Mail::to($participant->user->email)->send(new MissionEmail($missionData));
                        }
                    }
                }
            }

            return response()->json([
                'message' => 'បេសកកម្មត្រូវបានកែប្រែដោយជោគជ័យ',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified mission.
     */
    public function destroy(string $id)
    {
        try {
            $mission = Mission::findOrFail($id);
            $mission->delete();
            return response()->json(['message' => 'បេសកកម្មត្រូវបានលុបចោលដោយជោគជ័យ']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     *  View Mission school
     */

    public function updateMissionSchool(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate(
                [
                    'conclusion' => 'sometimes|string|max:2000',
                    'perspective' => 'sometimes|string|max:2000',
                    'appendix' => 'sometimes|string|max:2000',

                    'report_file' => 'file|mimes:pdf|max:10240',
                    'attendance_file' => 'file|mimes:pdf|max:10240',
                    'slide_file' => 'file|mimes:pdf|max:10240',
                    'assessment_file' => 'file|mimes:pdf|max:10240',

                ],
                [
                    'conclusion.required' => 'ត្រូវការសេចក្តីសន្និដ្ឋាន​ សម្រាប់បេសកម្មតាមសាលា (conclusion)',
                    'perspective.required' => 'ត្រូវការការបញ្ចាំងរបស់ក្រុមការងារ សម្រាប់បេសកម្មតាមសាលា (perspective)',
                    'appendix.required' => 'ត្រូវការឧបសម្ព័ន្ធ សម្រាប់បេសកម្មតាមសាលា (appendix)',
                    'conclusion.max' => 'សេចក្តីសន្និដ្ឋាន​សម្រាប់បេសកម្មតាមសាលា មិនត្រូវលើសពី ២០០០តួ(conclusion)',
                    'perspective.max' => 'ការបញ្ចាំងរបស់ក្រុមការងារសម្រាប់បេសកម្មតាមសាលា មិនត្រូវលើសពី ២០០០តួ(perspective)',
                    'appendix.max' => 'ឧបសម្ព័ន្ធសម្រាប់បេសកម្មតាមសាលា ​មិនត្រូវលើសពី ២០០០តួ (appendix)',
                ]
            );

            $mission = Mission::findOrFail($id);
            if (!$mission) {
                return response()->json(['message' => 'រកមិនទាន់បេសកម្ម និងសាលារៀនេះទេ!'], 404);
            }

            $mission->conclusion = $request->conclusion ?? $mission->conclusion;
            $mission->perspective = $request->perspective ?? $mission->perspective;
            $mission->appendix = $request->appendix ?? $mission->appendix;
            $mission->save();

            // Upload files
            if ($request->hasFile('report_file')) {
                if ($mission->report_uri) {
                    $this->fileService->deleteFile((string)$mission->report_file_path);
                }
                $reportUri = $this->fileService->uploadFile($validatedData['report_file'], 'mission_' . $id . '/report', 'mission');
                $mission->report_uri = $reportUri;
                $mission->save();
            }
            if ($request->hasFile('attendance_file')) {
                if ($mission->attendance_uri) {
                    $this->fileService->deleteFile((string)$mission->attendance_uri);
                }
                $attendanceUri = $this->fileService->uploadFile($validatedData['attendance_file'], 'mission_' . $id . '/attendant', 'mission');
                $mission->attendance_uri = $attendanceUri;
                $mission->save();
            }
            if ($request->hasFile('slide_file')) {
                if ($mission->slide_uri) {
                    $this->fileService->deleteFile((string)$mission->slide_uri);
                }
                $slideUri = $this->fileService->uploadFile($validatedData['slide_file'], 'mission_' . $id . '/slide', 'mission');
                $mission->slide_uri = $slideUri;
                $mission->save();
            }
            if ($request->hasFile('assessment_file')) {
                if ($mission->assessment_uri) {
                    $this->fileService->deleteFile((string)$mission->assessment_uri);
                }
                $assessmentUri = $this->fileService->uploadFile($validatedData['assessment_file'], 'mission_' . $id . '/assessment', 'mission');
                $mission->assessment_uri = $assessmentUri;
                $mission->save();
            }

            $mission->refresh();

            return response()->json($mission);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'រកមិនទាន់បេសកកម្ម និងសាលារៀនេះទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // send Notification when staff send tricking report
    public function sendNotification(Request $request, $id)
    {
        try {
            $mission = Mission::with('schools')->findOrFail($id);

            $user = User::with('roles')->whereHas('roles', function ($query) {
                $query->where('roles.id', 1);
            })
                ->get();
            $user->each(function ($user) use ($mission) {

                $schools = [];
                foreach ($mission->schools as $school) {
                    $schools[] = $school->school_name_kh;
                }

                if ($user != auth()->user()) {

                    $notification = Notification::create([
                        'user_id' => $user->id,
                        'action' => 'បង្កើតការវាយតម្លៃថ្មី',
                        'title' => 'ការវាយតម្លៃថ្មីត្រូវធ្វើនៅថ្ងៃ ' . $mission->start_date,
                        'message' => $mission->purpose,
                        'link' => 'patronage/mission/' . $mission->id,
                    ]);

                    // Send notification to google firebase
                    if ($user->fcm_token) {
                        $this->firebaseService->sendNotification($user->fcm_token, 'ការវាយតម្លៃថ្មីត្រូវធ្វើនៅថ្ងៃ ' . $mission->start_date, $mission->purpose, $notification->toArray());
                    }

                    // Send notitification to email
                    $trackingReport = [
                        'mission_purpose' => $mission->purpose,
                        'start_date' => $mission->start_date,
                        'end_date' => $mission->end_date,
                        'schools' => $schools,
                    ];

                    Mail::to($user->email)->send(new ReportTrackingEmail($trackingReport));
                }
            });

            return response()->json(['message' => 'បញ្ជូនសេចក្តីជូនដំណឹងដោយជោគជ័យ']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'រកមិនទាន់បេសកកម្មនេះទេ!'], 404);
        }
    }
}
