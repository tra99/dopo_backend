<?php

namespace App\Http\Controllers\Api;

use App\Models\Challenge;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $byMission_id = $request->input('by_category_id');
            $bySchool_id = $request->input('by_school_id');

            $challenges = Challenge::with('school', 'mission', 'category.parent')
                ->when($bySchool_id, function ($query) use ($bySchool_id) {
                    $query->where('school_id', $bySchool_id);
                })
                ->when($byMission_id, function ($query) use ($byMission_id) {
                    $query->where('mission_id', $byMission_id);
                })
                ->get();

            $groupedData = [];
            foreach ($challenges as $challenge) {
                if (!isset($groupedData[$challenge->school_id])) {
                    $groupedData[$challenge->school_id] = [
                        'school' => [
                            'id' => $challenge->school->id,
                            'school_code' => $challenge->school->school_code,
                            'school_name_kh' => $challenge->school->school_name_kh,
                            'school_name_en' => $challenge->school->school_name_en
                        ],
                        'data' => []
                    ];
                }


                $groupedData[$challenge->school_id]['data'][] = [
                    'id' => $challenge->id,
                    'school_id' => $challenge->school_id,
                    'mission_id' => $challenge->mission_id,
                    'category_id' => $challenge->category_id,
                    'challenge' => $challenge->challenge,
                    'solution' => $challenge->solution,
                    'school' => $challenge->school,
                    'mission' => $challenge->mission,
                    'category' => $challenge->category
                ];
            }


            return response()->json(array_values($groupedData));
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
                    'school_id' => 'required|integer|exists:schools,id',
                    'mission_id' => 'required|integer|exists:missions,id',
                    'category_id' => 'required|integer|exists:categories,id',
                    'challenge' => 'required|string|max:5000',
                    'solution' => 'sometimes|string|max:5000',
                ],
                [
                    'school_id.required' => 'សូមបញ្ជាក់សាលារៀន ដែលអ្នកចុះគាំទ្រ (school_id)',
                    'mission_id.required' => 'សូមបញ្ជាក់បេសកកម្ម ដែលអ្នកចុះគាំទ្រ (mission_id)',
                    'category_id.required' => 'សូមបញ្ជាក់ស្ដង់ដារ ឬសូចនករ (category_id)',
                    'challenge.required' => 'សូមបញ្ជាក់ពីបញ្ហាដែលជួប (challenge)',
                ]
            );

            $challenge = Challenge::create($validatedData);

            return response()->json([
                'message' => 'បង្កើតដោយជោគជ័យ',
                'challenge' => $challenge
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
            $challenge = Challenge::with('school', 'mission', 'category')
                ->findOrFail($id);
            return response()->json($challenge);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានទិន្នន័យនេះទេ!'], 404);
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
                    'school_id' => 'sometimes|integer|exists:schools,id',
                    'mission_id' => 'sometimes|integer|exists:missions,id',
                    'category_id' => 'sometimes|integer|exists:categories,id',
                    'challenge' => 'sometimes|string|max:5000',
                    'solution' => 'sometimes|string|max:5000',
                ]
            );

            $challenge = Challenge::with('school', 'mission', 'category')
                ->findOrFail($id);

            $challenge->update($validatedData);
            $challenge->refresh();

            return response()->json([
                'message' => 'កែប្រែជោគជ័យ',
                $challenge
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានទិន្នន័យនេះទេ!'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $challenge = Challenge::findOrFail($id);
            $challenge->delete();

            return response()->json([]);
        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'មិនមានទិន្នន័យនេះទេ!'], 404);
        }
    }
}
