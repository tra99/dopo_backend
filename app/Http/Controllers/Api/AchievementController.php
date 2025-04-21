<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\School;

class AchieveMentController extends Controller
{

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        try {
            // Get pagination limit with type casting and default value
            $perPage = (int) $request->input('limit', 15);
            $byMissionId = $request->input('by_mission_id');
            $bySchoolId = $request->input('by_school_id');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = strtolower($request->input('sort_direction', 'desc')) === 'asc' ? 'asc' : 'desc';

            // Validate pagination limit
            $perPage = max(1, min($perPage, 100)); // Restrict between 1 and 100

            // Fetch school with null check
            $school = $bySchoolId ? School::findOrFail($bySchoolId) : null;

            // Build query with proper relation loading
            $query = Achievement::with([
                'support_phase' => fn($q) => $q->select('id', 'title'),
                'categories.parent' => fn($q) => $q->select('id', 'title'),
                'categoriesOrSupportPhases',
                'categories.questions.evaluation_criterias' => fn($q) => $q->with([
                    'evaluations' => fn($q) => $q
                        ->when($bySchoolId, fn($q) => $q->where('school_id', $bySchoolId))
                        ->when($byMissionId, fn($q) => $q->where('mission_id', $byMissionId))
                        ->orderBy('created_at', 'desc')
                        ->first()  // Retrieve the first evaluation instead of an array
                ])
            ])
                ->when($byMissionId, fn($q) => $q->where('mission_id', $byMissionId))
                ->when($bySchoolId, fn($q) => $q->where('school_id', $bySchoolId))
                ->orderBy($sortBy, $sortDirection);

            // Execute paginated query
            $achieves = $query->paginate($perPage);

            // Transform the collection to include the 'point' values
            $achieves->getCollection()->transform(function ($achieve) {
                $achieve->grouped_categories = $achieve->categories->groupBy(
                    fn($category) => $category->parent?->title ?? 'No Parent'
                );

                foreach ($achieve->categories as $category) {
                    foreach ($category->questions as $question) {
                        // Calculate points for each question
                        $question->points = $question->evaluation_criterias->sum(function ($evaluationCriteria) {
                            // Sum the points for each evaluation
                            return $evaluationCriteria->evaluations->sum(function ($evaluation) {
                                return $evaluation->score ?? 0;
                            });
                        });
                    }

                    // Add 'point' to category (sum of all its questions' points)
                    $category->points = $category->questions->sum('points');
                }

                // Add 'point' to grouped categories
                foreach ($achieve->grouped_categories as $groupTitle => $groupedCategory) {
                    $groupedCategory->points = $groupedCategory->sum(function ($category) {
                        return $category->points;
                    });
                }

                unset($achieve->categories);  // Optionally remove the original categories
                return $achieve;
            });

            return response()->json($achieves, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the request data
            // Validate the request data
            $validatedData = $request->validate([
                'id'                           => 'nullable|exists:achievements,id',
                'support_phase_id'             => 'required|exists:support_phases,id',
                'mission_id'                   => 'required|exists:missions,id',
                'school_id'                    => 'required|exists:schools,id',
                'achievements'                 => 'required|array|min:1',
                'achievements.*.support_phase_id' => 'nullable|exists:support_phases,id',
                'achievements.*.category_id'   => 'nullable|required_without:achievements.*.support_phase_id|exists:categories,id',
                'achievements.*.description'   => 'nullable|string|max:5000',
            ]);

            $achievements = $validatedData['achievements'];
            unset($validatedData['achievements']);

            // Initialize an array for protected categories (ones that have evaluations)
            $protectedCategoryIds = [];

            // Find or create the main achievement based on the provided id
            if (isset($validatedData['id'])) {
                $mission_id = $validatedData['mission_id'];
                $school_id  = $validatedData['school_id'];
                // Load the achievement with its categories and nested evaluations
                $achieve = Achievement::with([
                    'categories.questions.evaluation_criterias.evaluations' => function ($q) use ($mission_id, $school_id) {
                        $q->where('mission_id', $mission_id)
                            ->where('school_id', $school_id)
                            ->orderBy('created_at', 'desc');
                    }
                ])->find($validatedData['id']);

                // Loop over existing attached categories and check if they have evaluations.
                foreach ($achieve->categories as $category) {
                    // Use collection filtering to check if any question's evaluation_criterias contain evaluations
                    $hasEvaluations = $category->questions->filter(function ($question) {
                        return $question->evaluation_criterias->filter(function ($criteria) {
                            return $criteria->evaluations->isNotEmpty();
                        })->isNotEmpty();
                    })->isNotEmpty();

                    if ($hasEvaluations) {
                        $protectedCategoryIds[] = $category->id;
                    }
                }

                // Remove id from validated data and update achievement
                unset($validatedData['id']);
                $achieve->update($validatedData);
            } else {
                // Create new achievement if no id is provided
                // Create new achievement if no id is provided
                $achieve = Achievement::create($validatedData);
            }

            // Build the list of categories from the incoming request data.
            $inputCategoryIds = [];
            $supportPhaseIds  = [];
            foreach ($achievements as $achievement) {
                $supportPhaseId = $achievement['support_phase_id'] ?? null;
                $categoryId     = $achievement['category_id'] ?? null;
                $description    = $achievement['description'] ?? null;

                if ($supportPhaseId) {
                    $supportPhaseIds[$supportPhaseId] = ['description' => $description];
                }
                if ($categoryId) {
                    // Here we add the category from the request.
                    $inputCategoryIds[$categoryId] = []; // no pivot data provided from input in this example
                }
            }

            // Merge the protected categories with the incoming ones.
            // This ensures that if a category with evaluations is already attached but not provided in the request,
            // it remains attached.
            $finalCategoryIds = $inputCategoryIds;
            foreach ($protectedCategoryIds as $protectedId) {
                if (!isset($finalCategoryIds[$protectedId])) {
                    $finalCategoryIds[$protectedId] = [];
                }
            }

            // Sync support phases normally and sync categories using the merged list.
            $achieve->support_phases()->sync($supportPhaseIds);
            $achieve->categories()->sync($finalCategoryIds);

            // Load relationships for the response
            $achieve->load('categoriesOrSupportPhases', 'categories');

            return response()->json(['achievement' => $achieve], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
