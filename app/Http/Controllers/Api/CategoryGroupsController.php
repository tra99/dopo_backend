<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoryGroups;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CategoryGroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $school_type_en = $request->input('school_type_en');

            if ($school_type_en) {
                $categoryGroup = CategoryGroups::with(['categories' => function ($query) {
                    $query->whereNull('parent_id')->with('children');
                }])
                    ->where('school_type_en', $school_type_en)
                    ->first();

                return response()->json($categoryGroup);
            }

            $perPage = max($request->input('limit', 15), 50);

            $categoryGroups = CategoryGroups::withCount('questions')
                ->paginate($perPage)
                ->appends($request->query());

            return response()->json($categoryGroups);
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
                    'title' => 'required|string|max:255|unique:category_groups,title',
                    'school_type_kh' => 'required|string|max:255',
                    'school_type_en' => 'required|string|max:255',
                ],
                [
                    'title.required' => 'ត្រូវការចំណងជើង (title)',
                    'title.unique' => 'ចំណងជើងនេះត្រូវបានបញ្ចូលរួចហើយ (title)',
                    'school_type_kh' => 'សូមបញ្ជាក់ប្រភេទសាលារៀន (school_type_kh)',
                    'school_type_en' => 'សូមបញ្ជាក់ប្រភេទសាលារៀន (school_type_en)',
                ]
            );

            $categoryGroup = CategoryGroups::create($validatedData);

            return response()->json([
                $categoryGroup,
                'message' => 'បង្កើតដោយជោគជ័យ'
            ]);
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
            $categoryGroups = CategoryGroups::with('categories.questions')
                ->findorFail($id);

            return response()->json($categoryGroups);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានស្តង់ដា ឬសូចនករនេះទេ!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $validatedData = $request->validate([
                'title' => 'sometimes|string|max:255|unique:category_groups,title,' . $id,
                'school_type_kh' => 'sometimes|string|max:255',
                'school_type_en' => 'sometimes|string|max:255',
            ]);

            $categoryGroup = CategoryGroups::findOrFail($id);
            $categoryGroup->update($validatedData);
            $categoryGroup->refresh();
            return response()->json([
                $categoryGroup,
                'message' => 'កែប្រែដោយជោគជ័យ'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានស្តង់ដា ឬសូចនករនេះទេ!'], 404);
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

            $categoryGroup = CategoryGroups::findOrFail($id);
            $categoryGroup->delete();

            return response()->json([
                'message' => 'លុបដោយជោគជ័យ',
                'category_group' => $categoryGroup
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានស្តង់ដា ឬសូចនករនេះទេ!'], 404);
        }
    }
}
