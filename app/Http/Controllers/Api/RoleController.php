<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Google\Service\Bigquery\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RoleController extends Controller
{

    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('limit', 10);
            $search = $request->input('search');
            $status = $request->input('status');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            $allowedSortFields = ['id', 'name', 'status', 'created_at', 'updated_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                return response()->json(['error' => 'Invalid sort_by field.'], 400);
            }

            $roles = Role::withCount('users')
                ->when($search, function ($query) use ($search) {
                    $searchLower = strtolower($search);
                    $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"]);
                })
                ->when(isset($status), function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->appends($request->query());

            return response()->json($roles, 200);
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

            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name',
            ], [
                'name.required' => 'ត្រូវការបញ្ចូលឈ្មោរតួនាទី (name)',
                'name.max' => 'ឈ្មោះតួនាទីមិនត្រូវលើសពី 255 តួនាទី (name)',
                'name.unique' => 'ឈ្មោះតួនាទីត្រូវបានបញ្ចូលរួចរាល់ (name)',
            ]);

            $role = Role::create([
                'name' => $validatedData['name'],
                'status' => 1,
            ]);

            return response()->json(['message' => 'បង្កើតដោយជោគជ័យ!', 'role' => $role], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified role.
     */
    public function show(string $id)
    {
        try {
            $role = Role::withCount('users')->findOrFail($id);

            return response()->json(['role' => $role], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'តួនាទីនេះមិនមានក្នុងប្រព័ន្ធទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $role = Role::withCount('users')->findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
                'status' => 'sometimes|boolean',
            ], [
                'name.required' => 'The role name is required.',
                'name.max' => 'The role name must not exceed 255 characters.',
                'name.unique' => 'The role name already exists.',
                'status.boolean' => 'The status field must be a boolean value.',
            ]);

            $role->update([
                'name' => $validatedData['name'],
                'status' => $validatedData['status'] ?? $role->status,
            ]);

            return response()->json([
                'message' => 'កែប្រែតួនាទីថ្មីដោយជោគជ័យ',
                'role' => $role
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'តួនាទីនេះមិនមានក្នុងប្រព័ន្ធទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return response()->json(['message' => 'Role deleted successfully!'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'តួនាទីនេះមិនមានក្នុងប្រព័ន្ធទេ!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function changeStatus(string $id)
    {
        try {
            $role = Role::withCount('users')->findOrFail($id);

            $role->update([
                'status' => !$role->status,
            ]);

            return response()->json([
                'message' => 'Role status updated successfully!',
                'role' => $role,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Role not found.'], 404);
        }
    }
}
