<?php

namespace App\Http\Controllers\Api;

use App\Enums\SchoolTypeEnum;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Enums\CategoryEnum;
use App\Models\CategoryGroups;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource, with optional filtering by type, pagination, and sorting.
     */
    public function index(Request $request)
    {
        try {
            $type = $request->input('type');
            $perPage = $request->input('limit', 15);
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');


            // new filters
            $search = $request->input('search');
            $by_status = $request->input('by_status');
            $by_school_type = $request->input('by_school_type');

            $school_type = $by_school_type ? CategoryGroups::where('school_type_en', $by_school_type)->first() : null;

            $categories = Category::withCount('children')
                ->with('children')
                ->withCount('questions')
                ->with('schoolType')
                ->when($type, function ($query) use ($type) {
                    $query->where('type', $type);
                })
                ->when($search, function ($query) use ($search) {
                    $query->where('title', 'LIKE', "%{$search}%");
                })
                ->when(isset($by_status), function ($query) use ($by_status) {
                    $query->where('status', $by_status);
                })
                ->when($school_type, function ($query) use ($school_type) {
                    $query->where('school_type_id', $school_type->id);
                })
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->appends($request->query());

            return response()->json($categories);
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
                    'title' => 'required|string|max:255',
                    'parent_id' => 'nullable|exists:categories,id',
                    'type' => ['required', 'in:' . implode(',', CategoryEnum::values())],
                    'status' => 'sometimes|boolean',
                    'school_type_id' => 'required|integer|exists:category_groups,id',
                ],
                [
                    'title.required' => 'សូមបំពេញឈ្មោះស្តង់ដារ​ ឬសូចនករ​ (title)',
                    'school_type.required' => 'សូមបញ្ជាក់ប្រភេទសាលារៀន (school_type)',
                    'school_type_id.required' => 'សូមបញ្ជាក់ប្រភេទសាលារៀន (school_type_id)',
                ]
            );

            $category = Category::create($validatedData);

            return response()->json([
                'message' => 'Category created successfully',
                'category' => $category
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
            $category = Category::with('parent', 'children', 'questions', 'schoolType')->findOrFail($id);
            $category['questions'] = json_decode($category->questions, true);
            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានស្តង់ដា ឬសូចនករនេះទេ!'], 404);
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
            $category = Category::findOrFail($id);

            $validatedData = $request->validate(
                [
                    'title' => 'sometimes|required|string|max:255',
                    'parent_id' => 'nullable|exists:categories,id',
                    'type' => ['sometimes', 'in:' . implode(',', CategoryEnum::values())],
                    'status' => 'sometimes|boolean',
                    'school_type_id' => 'integer|exists:category_groups,id',
                ],
                [
                    'parent_id.exists' => 'ស្តង់ដានេះមិនមានទេ!',
                    'school_type_id.exists' => 'ប្រភេទសាលានេះមិនមានទេ!',
                ]
            );

            $category->update($validatedData);

            return response()->json([
                'message' => 'កែប្រែដោយជោគជ័យ',
                'category' => $category
            ]);
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
            $category = Category::with('children')->findOrFail($id);

            if ($category->children->count() > 0) {
                return response()->json(['error' => 'មិនអាចលុបស្តង់ដា​រ​ ឬសូចនករនេះទេ'], 400);
            }
            $category->delete();

            return response()->json(['message' => 'លុបដោយជោគជ័យ']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'មិនមានស្តង់ដារ ឬសូចនករនេះទេ!'], 404);
        }
    }


    /**
     * Get parent categories by type.
     */
    /**
     * Get parent categories by type with optional search.
     */
    public function getParentByType(Request $request)
    {
        try {
            $type = $request->input('type');
            $search = $request->input('search');

            $parents = match ($type) {
                'category' => Category::whereNull('parent_id')
                    ->when($search, fn($query) => $query->where('title', 'like', "%{$search}%"))
                    ->get(),
                'sochanakor' => Category::where('type', 'category')
                    ->when($search, fn($query) => $query->where('title', 'like', "%{$search}%"))
                    ->get(),
                'group' => Category::where('type', 'sochanakor')
                    ->when($search, fn($query) => $query->where('title', 'like', "%{$search}%"))
                    ->get(),
                default => collect([]),
            };

            return response()->json($parents);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
