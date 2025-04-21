<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SchoolType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SchoolTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('limit', 15);
            $search = $request->input('search');
            $school_types = SchoolType::when($search, function ($query) use ($search) {
                $searchLower = mb_strtolower($search);
                $query->whereRaw('LOWER(en_name) LIKE ?', ["%{$searchLower}%"])
                    ->orWhereRaw('LOWER(kh_name) LIKE ?', ["%{$searchLower}%"]);
            })
            ->paginate($perPage);
            return response()->json($school_types);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $params = $request->validate([
                'en_name' => 'required |string|max:255',
                'kh_name' => 'required |string|max:255'
            ]);

            $school_type = SchoolType::create($params);

            return response()->json([
                'school_type' => $school_type,
                'message' => 'បង្កើតប្រភេទសាលារៀនដោយជោគជ័យ'
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
            $school_type = SchoolType::findOrFail($id);
            return response()->json([
                'school_type' => $school_type
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានប្រភេទសាលារៀននេះទេ!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $school_type = SchoolType::findorFail($id);
            $params = $request->validate([
                'en_name' => 'sometimes|string|max:255',
                'kh_name' => 'sometimes|string|max:255'
            ]);

            $school_type->update($params);
            $school_type->refresh();

            return response()->json([
                'message' => 'កែប្រែដោយជោគជ័យ',
                'school_type' => $school_type
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានប្រភេទសាលារៀននេះទេ!'], 404);
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
            $school_type = SchoolType::findOrFail($id);
            $school_type->delete();
            return response()->json(['message' => 'ប្រភេទសាលារៀនត្រូវបានលុបចោលដោយជោគជ័យ', 'school_type' => $school_type], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'មិនមានប្រភេទសាលារៀននេះទេ!'], 404);
        }
    }
}
