<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SupportPhase;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SupportPhaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $isParent = $request->input('is_parent');
            $perPage = $request->input('limit', 15);
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');
            $search = $request->input('search');

            // Validate sortBy and sortDirection inputs
            $validSortColumns = ['created_at', 'title'];  // Add more columns as needed
            if (!in_array($sortBy, $validSortColumns)) {
                $sortBy = 'created_at';  // Default column if invalid
            }

            $validSortDirections = ['asc', 'desc'];
            if (!in_array($sortDirection, $validSortDirections)) {
                $sortDirection = 'desc';  // Default direction if invalid
            }

            $supportPhase = SupportPhase::with('parent', 'children')  // Assume the parent relationship exists
                ->withCount('children')
                ->when($isParent, function ($query) {
                    // If is_parent is true, we check if the model is parentless
                    $query->whereDoesntHave('parent');  // Use `whereDoesntHave` for relationship
                })
                ->when($search, function ($query) use ($search) {
                    // Search based on the title
                    $query->where('title', 'LIKE', "%{$search}%");
                })
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->appends($request->query());

            return response()->json($supportPhase);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Error fetching support phases: " . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validatedDate = $request->validate(
                [
                    'title' => 'required|string|max:255',
                    'parent_id' => 'sometimes|integer|exists:support_phases,id',
                    'school_type_id' => 'required|integer|exists:category_groups,id',
                    'status' => 'sometimes|string|max:255'
                ],
                [
                    'title.required' => 'ត្រូវការឈ្មោះដំណាក់កាល (title)',
                    'parent_id.exists' => 'ដំណាក់កាលនេះមិនមានក្នុងប្រព័ន្ធទេ! (parend_id)',
                    'school_type_id' => 'ប្រភេទសាលារៀនមិនមានក្នុងប្រព័ន្ធទេ! (school_type_id)',
                ]
            );

            $supportPhase = SupportPhase::create($validatedDate);
            return response()->json([
                'suport_phase' => $supportPhase,
                'message' => 'បង្កើតដំណាក់កាលដោយជោគជ័យ'
            ]);
        } catch (\Exception $e) {
            Log::error('' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Retrieve the resource by ID, including related data (parent and children)
            $supportPhase = SupportPhase::with('parent', 'children')->findOrFail($id);

            // Check if the 'children' relationship is loaded correctly
            if ($supportPhase->children->isEmpty()) {
                Log::info("No children found for support phase ID: {$id}");
            }

            return response()->json($supportPhase);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Support phase not found!'], 404);
        } catch (\Exception $e) {
            Log::error("Error fetching support phase: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $supportPhase = SupportPhase::findOrFail($id);
            $validatedDate = $request->validate(
                [
                    'title' => 'sometimes|string|max:255',
                    'parent_id' => 'sometimes|integer|exists:support_phases,id',
                    'school_type_id' => 'sometimes|integer|exists:category_groups,id',
                    'status' => 'sometimes|string|max:255'
                ],
                [
                    'title.required' => 'ត្រូវការឈ្មោះដំណាក់កាល (title)',
                    'parent_id.exists' => 'ដំណា<ក់កាលនេះមិនមានក្នុងប្រព័ន្ធទេ! (parend_id)',
                    'school_type_id' => 'ប្រភេទសាលារៀនមិនមានក្នុងប្រព័ន្ធទេ! (school_type_id)',
                ]
            );

            $supportPhase->update($validatedDate);
            $supportPhase->refresh();
            return response()->json([
                'suport_phase' => $supportPhase,
                'message' => 'កែប្រែដោយជោគជ័យ'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានដំណាក់កាលនេះទេ!'], 404);
        } catch (\Exception $e) {
            Log::error('' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $supportPhase = SupportPhase::with('children')->findOrFail($id);

            if ($supportPhase->children->count() > 0) {
                return response()->json(['error' => 'មិនអាចលុបដំណាក់កាលនេះទេ'], 400);
            }
            $supportPhase->delete();

            return response()->json([
                'support_phase' => $supportPhase,
                'message' => 'លុបដោយជោគជ័យ',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'មិនមានដំណាក់កាលនេះទេ!'], 404);
        }
    }
}
