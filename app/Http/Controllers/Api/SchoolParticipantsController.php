<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\SchoolParticipants;
use App\Services\FileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SchoolParticipantsController extends Controller
{
    protected $fileService;
    public function __construct(
        FileService $fileService
    ) {
        $this->fileService = $fileService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        DB::beginTransaction();
        try {
            $search = $request->input('search', '');
            $by_school_id = $request->input('by_school_id');
            $by_mission_id = $request->input('by_mission_id');

            $schoolParticipants = SchoolParticipants::with('school')
                ->where('organization', 'like', '%' . $search . '%')
                ->when($by_school_id, function ($query) use ($by_school_id) {
                    $query->where('school_id', $by_school_id);
                })
                ->when($by_mission_id, function ($query) use ($by_mission_id) {
                    $query->where('mission_id', $by_mission_id);
                })
                ->get();

            // Group the participants by school
            $groupedData = [];
            foreach ($schoolParticipants as $participant) {
                // If school_id doesn't exist in the grouped data, add it
                if (!isset($groupedData[$participant->school_id])) {
                    $groupedData[$participant->school_id] = [
                        'school' => [
                            'id' => $participant->school->id,
                            'school_code' => $participant->school->school_code,
                            'school_name_kh' => $participant->school->school_name_kh,
                            'school_name_en' => $participant->school->school_name_en,
                        ],
                        'data' => []
                    ];
                }

                // Add the current participant to the respective school's data
                $groupedData[$participant->school_id]['data'][] = [
                    'id' => $participant->id,
                    'school_id' => $participant->school_id,
                    'mission_id' => $participant->mission_id,
                    'organization' => $participant->organization,
                    'number_of_male' => $participant->number_of_male,
                    'number_of_female' => $participant->number_of_female,
                    'number_of_total' => $participant->number_of_male + $participant->number_of_female,
                    'file_uri' => $participant->file_uri,
                    'created_at' => $participant->created_at,
                    'updated_at' => $participant->updated_at,
                ];
            }

            // Prepare the final response structure
            $response = array_values($groupedData);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Start the transaction
        DB::beginTransaction();
        try {
            $validatedData = $request->validate(
                [
                    '*.school_id' => 'required|integer|exists:schools,id',
                    '*.mission_id' => 'required|integer|exists:missions,id',
                    '*.organization' => 'required|string|max:255',
                    '*.number_of_female' => 'required|integer|min:0',
                    '*.number_of_total' => 'required|integer|min:0|gte:*.number_of_female',
                    // '*.file' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg,bmp,webp,pdf|max:10240',
                ],
                [
                    '*.school_id.required' => 'សូមបញ្ជាក់សាលារៀន (school_id)',
                    '*.school_id.integer' => 'សាលារៀនត្រូវតែជាលេខ (school_id)',
                    '*.school_id.exists' => 'សាលារៀនដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ (school_id)',
                    '*.mission_id.required' => 'សូមបញ្ជាក់បេសកកម្ម (mission_id)',
                    '*.mission_id.integer' => 'បេសកកម្មត្រូវតែជាលេខ (mission_id)',
                    '*.organization.required' => 'សូមបញ្ជាក់គ្រឹះស្ថាន (organization)',
                    '*.number_of_total.required' => 'សូមបញ្ជាក់ចំនួនអ្នកចូលរួមប្រុស (number_of_total)',
                    '*.number_of_female.required' => 'សូមបញ្ជាក់ចំនួនអ្នកចូលរួមស្រី (number_of_female)',
                    '*.number_of_total.gte' => 'ចំនួនអ្នកចូលសរុប ត្រូវតែធំជាងចំនួនចូលរួមស្រី (number_of_total)',
                    // '*.file.mimes' => 'ឯកសារត្រូវតែមានទម្រង់ pdf, jpg, jpeg, png (file)',
                    // '*.file.max:10240' => 'ឯកសារមិនត្រូវធំជាង 10MB',
                ]
            );

            $schoolParticipants = [];
            foreach ($validatedData as $schoolParticipantData) {
                // Calculate number_of_male based on total minus female, then remove number_of_total from the data
                $schoolParticipantData['number_of_male'] = $schoolParticipantData['number_of_total'] - $schoolParticipantData['number_of_female'];
                unset($schoolParticipantData['number_of_total']);

                // Check if the record already exists
                $existingParticipant = SchoolParticipants::where('school_id', $schoolParticipantData['school_id'])
                    ->where('mission_id', $schoolParticipantData['mission_id'])
                    ->where('organization', $schoolParticipantData['organization'])
                    ->first();

                if ($existingParticipant) {
                    // Update the existing record
                    $existingParticipant->update($schoolParticipantData);
                    $existingParticipant->refresh();
                    // Optionally, recalculate total if needed
                    $existingParticipant->number_of_total = $schoolParticipantData['number_of_male'] + $schoolParticipantData['number_of_female'];
                    $schoolParticipants[] = $existingParticipant;
                } else {
                    // Create a new record
                    $newParticipant = SchoolParticipants::create($schoolParticipantData);
                    // Optionally, add a total field for output
                    $newParticipant->number_of_total = $schoolParticipantData['number_of_male'] + $schoolParticipantData['number_of_female'];
                    $schoolParticipants[] = $newParticipant;
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'បង្កើតដោយជោគជ័យ',
                'school_participants' => $schoolParticipants
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // public function store(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate(
    //             [
    //                 'school_id' => 'required|integer|exists:schools,id',
    //                 'mission_id' => 'required|integer|exists:missions,id',
    //                 'organization' => 'required|string|max:255',
    //                 'number_of_male' => 'required|integer|min:0',
    //                 'number_of_female' => 'required|integer|min:0',
    //                 'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg,bmp,webp,pdf|max:10240',
    //             ],
    //             [
    //                 'school_id.required' => 'សូមបញ្ជាក់សាលារៀន (school_id)',
    //                 'school_id.integer' => 'សាលារៀនត្រូវតែជាលេខ (school_id)',
    //                 'school_id.exists' => 'សាលារៀនដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ (school_id)',
    //                 'mission_id.required' => 'សូមបញ្ជាក់បេសកកម្ម (mission_id)',
    //                 'mission_id.integer' => 'បេសកកម្មត្រូវតែជាលេខ (mission_id)',
    //                 'organization.required' => 'សូមបញ្ជាក់គ្រឹះស្ថាន (organization)',
    //                 'number_of_male.required' => 'សូមបញ្ជាក់ចំនួនអ្នកចូលរួមប្រុស (number_of_male)',
    //                 'number_of_female.required' => 'សូមបញ្ជាក់ចំនួនអ្នកចូលរួមស្រី (number_of_female)',
    //                 'file.mimes' => 'ឯកសារត្រូវតែមានទម្រង់ pdf, jpg, jpeg, png (file)',
    //                 'file.max:10240' => 'ឯកសារមិនត្រូវធំជាង 10MB',
    //             ]
    //         );


    //         $findSchoolParticipants = SchoolParticipants::where('school_id', $validatedData['school_id'])
    //             ->where('mission_id', $validatedData['mission_id'])
    //             ->where('organization', $validatedData['organization']);

    //         if ($findSchoolParticipants->exists()) {
    //             return response()->json(['error' => 'អ្នកចូលរួមរបស់សាលានេះមេានរួមរាល់ហើយ!'], 422);
    //         }


    //         if ($request->hasFile('file')) {
    //             $school = School::find($validatedData['school_id']);

    //             $validatedData['file_uri'] = $this->fileService->uploadFile($validatedData['file'], $school->school_name_en, 'school participants');
    //             unset($validatedData['file']);
    //         }

    //         $schoolParticipants = SchoolParticipants::create($validatedData);

    //         return response()->json([
    //             'message' => 'បង្កើតដោយជោគជ័យ',
    //             'school_participants' => $schoolParticipants
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $schoolParticipants = SchoolParticipants::findOrFail($id);
            return response()->json($schoolParticipants);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានអ្នកចូលរួមនេះទេ!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $schoolParticipants = SchoolParticipants::findOrFail($id);

            $validatedData = $request->validate(
                [
                    'school_id' => 'sometimes|integer|exists:schools,id',
                    'mission_id' => 'sometimes|integer|exists:missions,id',
                    'organization' => 'sometimes|string|max:255',
                    'number_of_male' => 'sometimes|integer|min:0',
                    'number_of_female' => 'sometimes|integer|min:0',
                    'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,svg,bmp,webp,pdf|max:10240',
                ],
                [
                    'file.max' => 'ឯកសារមិនត្រូវធំជាង 10MB',
                ]
            );

            if ($request->hasFile('file')) {
                $school = School::find($validatedData['school_id']);

                $validatedData['file_uri'] = $this->fileService->uploadFile($validatedData['file'], $school->school_name_en, 'school participants');
            }
            unset($validatedData['file']);

            $schoolParticipants->update($validatedData);
            $schoolParticipants->refresh();

            return response()->json([
                'message' => 'កែប្រែដោយជោគជ័យ',
                'school_participants' => $schoolParticipants
            ]);
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
            $schoolParticipants = SchoolParticipants::findOrFail($id);
            $schoolParticipants->delete();

            return response()->json([
                'message' => 'លុបដោយជោគជ័យ',
                'school_participants' => $schoolParticipants
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានអ្នកចូលរួមនេះទេ!'], 404);
        }
    }
}
