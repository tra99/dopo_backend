<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\Request;
use App\Models\School;
use App\Services\FileService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EvaluationController extends Controller
{
    protected $fileService;
    public function __construct(
        FileService $fileService
    ) {
        $this->fileService = $fileService;
    }

    /**
     * Get all evaluations.
     */
    public function index(Request $request)
    {
        try {
            $perPage = min($request->input('limit', 15), 50); // Default 15, max 50
            $search = $request->input('search');

            $evaluations = Evaluation::with('school', 'mission')
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('result', 'LIKE', "%{$search}%")
                            ->orWhere('description', 'LIKE', "%{$search}%");
                    });
                })
                ->paginate($perPage)
                ->appends($request->query());

            return response()->json($evaluations);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created evaluation.
     */
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    'id' => 'nullable|exists:evaluations,id',
                    'result' => 'required|string',
                    'score' => 'required|string',
                    'description' => 'nullable|string',
                    'school_id' => 'required|exists:schools,id',
                    'mission_id' => 'required|exists:missions,id',
                    'evaluation_criteria_id' => 'required|exists:evaluation_criterias,id',
                    'documents' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,bmp,gif,svg',
                ],
                [
                    'result.required' => 'ត្រូវការគោលបំណងរបស់បេសកកម្ម (purpose)',
                    'description' => 'បរិយាយត្រូវតែជាអក្សរ',
                    'school_id.required' => 'សូមបញ្ជាក់សាលារៀន ដែលអ្នកចុះគាំទ្រ',
                    'school_id.exists' => 'សាលារៀនដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    'mission_id.required' => 'សូមបញ្ជាក់បេសកកម្ម ដែលអ្នកចុះគាំទ្រ',
                    'mission_id.exists' => 'បេសកកម្មដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    'evaluation_criteria_id.required' => 'សូមបញ្ជាក់ចំនុចផ្ទៀងផ្ទាត់ ដែលអ្នកចុះគាំទ្រ',
                    'evaluation_criteria_id.exists' => 'ចំនុចផ្ទៀងផ្ទាត់ដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    'documents.mimes' => 'ឯកសារត្រូវតែមានប្រភេទ pdf, word ឬ រូបភាព',
                ]
            );

            unset($validatedData['documents']);
            $evaluation = Evaluation::where('evaluation_criteria_id', $validatedData['evaluation_criteria_id'])->where('school_id', $validatedData['school_id'])->where('mission_id', $validatedData['mission_id'])->first();
            if ($evaluation) {
                $evaluation->update($validatedData);
                $evaluation->refresh();
            } else {
                $evaluation = Evaluation::create($validatedData);
            }

            // Upload document if provided
            if ($request->hasFile('documents')) {
                $document = $request->file('documents');
                $school = School::find($validatedData['school_id']);
                $path = $school->school_name_en . '/mission_' . $validatedData['mission_id'];
                $validatedData['documents'] = $this->fileService->uploadFile($document, $path, 'evaluation', $evaluation->id . '_' . $document->getClientOriginalName());

                $evaluation->update(['documents' => $validatedData['documents']]);
                $evaluation->refresh();
            }

            return response()->json([
                'message' => 'ការវាយតម្លៃត្រូវបានបង្កើតដោយជោគជ័យ',
                'evaluation' => $evaluation->load('school', 'mission', 'evaluation_criteria'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * return the specified evaluation
     */
    public function show(string $id)
    {
        try {
            $evaluation = Evaluation::with('school', 'mission', 'evaluation_criteria')->findOrFail($id);
            return response()->json($evaluation);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'ការវាយតម្លៃ មិនមាននៅក្នុងប្រព័ន្ធទេ',
            ], 404);
        }
    }

    /**
     * Update the specified evaluation.
     */
    public function update(Request $request, string $id)
    {
        try {
            $evaluation = Evaluation::findOrFail($id);

            $validatedData = $request->validate(
                [
                    'result' => 'required|string',
                    'score' => 'required|string',
                    'description' => 'string',
                    'school_id' => 'required|exists:schools,id',
                    'mission_id' => 'required|exists:missions,id',
                    'evaluation_criteria_id' => 'required|exists:evaluation_criterias,id',
                    'documents' => 'nullable|mimes:pdf,doc,docx,jpg,jpeg,png,bmp,gif,svg',
                ],
                [
                    'result.required' => 'ត្រូវការគោលបំណងរបស់បេសកកម្ម (purpose)',
                    'description' => 'បរិយាយត្រូវតែជាអក្សរ',
                    'school_id.required' => 'សូមបញ្ជាក់សាលារៀន ដែលអ្នកចុះគាំទ្រ',
                    'school_id.exists' => 'សាលារៀនដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    'mission_id.required' => 'សូមបញ្ជាក់បេសកកម្ម ដែលអ្នកចុះគាំទ្រ',
                    'mission_id.exists' => 'បេសកកម្មដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    'evaluation_criteria_id.required' => 'សូមបញ្ជាក់ចំនុចផ្ទៀងផ្ទាត់ ដែលអ្នកចុះគាំទ្រ',
                    'evaluation_criteria_id.exists' => 'ចំនុចផ្ទៀងផ្ទាត់ដែលបានផ្តល់អោយ មិនមាននៅក្នុងប្រព័ន្ធឡើយ',
                    'documents.mimes' => 'ឯកសារត្រូវតែមានប្រភេទ pdf, word ឬ រូបភាព',
                ]
            );

            unset($validatedData['documents']);
            $evaluation->update($validatedData);

            // Upload document if provided
            if ($request->hasFile('documents')) {


                // Delete old document
                if ($evaluation->documents) {
                    $this->fileService->deleteFile($evaluation->documents);
                }

                $document = $request->file('documents');
                $school = School::find($validatedData['school_id']);
                $path = $school->school_name_en . '/mission_' . $validatedData['mission_id'];
                $validatedData['documents'] = $this->fileService->uploadFile($document, $path, 'evaluation', $evaluation->id . '_' . $document->getClientOriginalName());
                $validatedData['documents'] = $this->fileService->uploadFile($document, $path, 'evaluation');

                $evaluation->update(['documents' => $validatedData['documents']]);
            }

            $evaluation->refresh();

            return response()->json([
                'evaluation' => $evaluation,
                'message' => 'ការវាយតម្លៃត្រូវបានកែប្រែដោយជោគជ័យ',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified evaluation.
     */
    public function destroy(string $id)
    {
        try {
            $evaluation = Evaluation::findOrFail($id);

            if ($evaluation->documents) {
                $this->fileService->deleteFile($evaluation->documents);
            }

            $evaluation->delete();
            return response()->json(['message' => 'ការវាយតម្លៃត្រូវបានលុបចោលដោយជោគជ័យ']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
