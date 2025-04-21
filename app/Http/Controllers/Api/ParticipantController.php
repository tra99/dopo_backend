<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mission;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Services\FileService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ParticipantController extends Controller
{
    protected $fileService;

    public function __construct(
        FileService $fileService,
    ) {
        $this->fileService = $fileService;
    }
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('limit', 15);
            $search = $request->input('search');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            $participants = Participant::withCount('missions')
                ->addSelect([
                    'lastest_mission' => Mission::select('end_date')
                        ->join('mission_participant', 'mission_participant.mission_id', '=', 'missions.id')
                        ->whereColumn('mission_participant.participant_id', '=', 'participants.id')
                        ->orderBy('end_date', 'desc')
                        ->limit(1)
                ])
                ->when($search, function ($query) use ($search) {
                    $searchLower = mb_strtolower($search);
                    $query->where(function ($query) use ($searchLower) {
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])
                            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchLower}%"])
                            ->orWhereRaw('LOWER(phone) LIKE ?', ["%{$searchLower}%"]);
                    });
                })
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->appends($request->query());

            return response()->json($participants);
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
            $validatedData = $request->validate(
                [
                    'title' => 'sometimes|string|max:255|nullable',
                    'name' => 'required|string|max:255',
                    'position' => 'sometimes|string|max:255',
                    'email' => 'sometimes|email|unique:participants,email',
                    'phone' => 'required|string|max:255',
                    'organization' => 'required|string|max:255',
                    'user_id' => 'sometimes|exists:users,id|nullable',
                    'address' => 'sometimes|string|max:255|nullable',
                    'telegram' => 'sometimes|string|max:255|nullable',
                ],
                [
                    'name.required' => 'ត្រូវការឈ្មោះអ្នកចូលរួម (name)',
                    'organization.required' => 'ត្រូវការឈ្មោះអង្គភាព (organization)',
                ]
            );

            $participant = Participant::create($validatedData);

            return response()->json([
                'message' => 'អ្នកចូលរួមត្រូវបានបង្កើតដោយជោគជ័យ',
                'participant' => $participant
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeWithImport(Request $request)
    {
        try {
            $request->validate([
                'participants_list' => 'required|file|mimes:xlsx,xls,csv'
            ], [
                'participants_list.required' => 'ត្រូវការបញ្ជូលឯកសារទិន្នន័យ (participants_list)',
                'participants_list.file' => 'ឯកសារត្រូវតែជាប្រភេទ excel (participants_list)',
            ]);

            $file = $request->file('participants_list');

            // Read Excel File
            $data = $this->fileService->readExcelFile($file);
            // Optionally, process $data further (e.g., skip headers or map rows)
            $responseData = [
                'success' => [],
                'errors' => []
            ];

            if ($data) {
                $i = 0;
                foreach ($data as $row) {
                    // Optionally, skip the header or other rows
                    if ($i > 1) {
                        try {
                            $user = User::where('email', $row[5])->first();
                            $user = $user ? $user->id : null;

                            if($row[2] ) {
                                $participant = Participant::create([
                                    "title" => $row[1],
                                    "name" => $row[2],
                                    "position" => $row[3],
                                    "phone" => $row[4],
                                    "email" => $row[5],
                                    "organization" => $row[6],
                                    "user_id" => $user
                                ]);
                                $responseData['success'][] = $row;
                            }

                        } catch (\Exception $e) {
                            $responseData['errors'][] = ['row' => $row, 'message' => $e->getMessage()];
                        }
                    }
                    $i++;
                }
            }

            return response()->json([
                'data' => $responseData,
                'message' => "ទិន្នន័យបញ្ជូលជោគជ័យ"
            ], 200);
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
            $participant = Participant::with('missions', 'user')->findOrFail($id);
            return response()->json($participant);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានអ្នកចូលរួមនេះទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $validatedData = $request->validate(
                [
                    'title' => 'sometimes|string|max:255',
                    'name' => 'sometimes|string|max:255',
                    'position' => 'sometimes|string|max:255',
                    'email' => 'sometimes|email',
                    'phone' => 'sometimes|string|max:255',
                    'organization' => 'sometimes|string|max:255',
                    'user_id' => 'sometimes|exists:users,id',
                ],
                [
                    'user_id.exists' => 'មិនមានអ្នកប្រើប្រាស់នេះទេ!',
                ]
            );

            $participant = Participant::findOrFail($id);
            $participant->update($validatedData);

            return response()->json([
                'message' => 'អ្នកចូលរួមត្រូវបានកែប្រែដោយជោគជ័យ',
                'participant' => $participant
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានអ្នកចូលរួមនេះទេ!'], 404);
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
            $participant = Participant::findOrFail($id);
            $participant->delete();
            return response()->json(['message' => 'អ្នកចូលរួមត្រូវបានលុបចោលដោយជោគជ័យ'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានអ្នកចូលរួមនេះទេ!'], 404);
        }
    }
}
