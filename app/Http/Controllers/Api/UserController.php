<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\School;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
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
            $status = $request->input('status');
            $sortBy = $request->input('sort_by', 'created_at');
            $sortDirection = $request->input('sort_direction', 'desc');

            $users = User::with('school', 'roles')
                ->when($search, function ($query) use ($search) {
                    $searchLower = mb_strtolower($search); // Convert search term to lowercase for case-insensitive comparison
                    $query->where(function ($query) use ($searchLower) {
                        $query->whereRaw('LOWER(name) LIKE ?', ["%{$searchLower}%"])  // Case-insensitive search for 'name'
                            ->orWhereRaw('LOWER(email) LIKE ?', ["%{$searchLower}%"]); // Case-insensitive search for 'email'
                    });
                })
                ->when(isset($status), function ($query) use ($status) {
                    $query->where('status', $status);
                })
                ->orderBy($sortBy, $sortDirection)
                ->paginate($perPage)
                ->appends($request->query());

            // Add latest login information for each user
            $users->getCollection()->transform(function ($user) {
                $user->roles = $user->roles->makeHidden('pivot');

                return $user;
            });

            return response()->json([
                'users' => $users,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        try {
            // Validation Rules
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|unique:users,email',
                'status' => 'required|boolean',
                'password' => [
                    'required',
                    'string',
                    'min:6',
                    'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/',
                ],
                'school_id' => 'required|exists:schools,id',
                'description' => 'sometimes|string|nullable',
                'roles' => 'sometimes|string',
                'roles.*' => 'exists:roles,id',
            ], [
                'name.required' => 'The Name field is required',
                'email.required' => 'The Email field is required',
                'password.required' => 'The Password field is required',
                'password.min' => 'The Password must be at least 6 characters',
                'password.regex' => 'The Password must contain at least one letter and one number',
                'roles.*.exists' => 'The selected Role is invalid',
                'school_id.exists' => 'The selected School is invalid',
            ]);

            // Data Processing
            $validatedData['description'] = $request->input('description', '');
            $validatedData['password'] = bcrypt($validatedData['password']);
            $validatedData['status'] = true;

            // Get Roles (if provided)
            $roles = json_decode($validatedData['roles'], true);
            unset($validatedData['roles']);

            // reate User
            $user = User::create($validatedData);

            // Assign Roles
            $userRoles = [];
            foreach ($roles as $role) {
                $userRoles[] = [
                    'user_id' => $user->id,
                    'role_id' => $role,
                ];
            }
            if (!empty($userRoles)) {
                DB::table('user_role')->insert($userRoles); // Bulk insert
            }

            // Return User with Roles (without pivot)
            $user = User::with('school', 'roles')->findOrFail($user->id);
            $user->roles = $user->roles->makeHidden('pivot');

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function storeWithImport(Request $request)
    {
        try {
            $request->validate([
                'users_list' => 'required|file|mimes:xlsx,xls,csv'
            ], [
                'users_list.required' => 'ត្រូវការបញ្ជូលឯកសារទិន្នន័យ (users_list)',
                'users_list.file' => 'ឯកសារត្រូវតែជាប្រភេទ excel (users_list)',
            ]);

            $file = $request->file('users_list');

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
                        // DB::beginTransaction();
                        try {
                            $school = School::where('school_name_kh', $row[4])->orWhere('school_name_en', $row[4])->first();
                            $schoolId = $school ? $school->id : null;

                            $user = User::create([
                                "name" => $row[1],
                                "email" => $row[2],
                                "password" => bcrypt($row[3]),
                                "description" => $row[5],
                                "school_id" => $schoolId,
                                "status" => true
                            ]);

                            // Insert user role
                            $userRole = DB::table('user_role')->insert([
                                'user_id' => $user->id,
                                'role_id' => 5,
                            ]);

                            $responseData['success'][] = $row;
                        } catch (\Exception $e) {
                            // DB::rollBack();
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
     * Display the specified user.
     */
    public function show(string $id)
    {
        $user = User::with('school', 'roles')->findOrFail($id);
        $user->roles = $user->roles->makeHidden('pivot');

        return response()->json($user);
    }


    /**
     * Update the specified user.
     */
    public function update(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            // Validation Rules
            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|unique:users,email,' . $user->id,
                'status' => 'sometimes|boolean',
                'password' => [
                    'sometimes',
                    'string',
                    'min:6',
                    'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/',
                ],
                'school_id' => 'sometimes|exists:schools,id',
                'description' => 'sometimes|string|nullable',
                'roles' => 'sometimes|string',
                'roles.*' => 'exists:roles,id',
            ], [
                'name.max' => 'The Name field must not exceed 255 characters.',
                'email.unique' => 'The Email already exists.',
                'password.min' => 'The Password must be at least 6 characters.',
                'password.regex' => 'The Password must contain at least one letter and one number.',
                'roles.*.exists' => 'The selected Role is invalid.',
                'school_id.exists' => 'The selected School is invalid.',
            ]);


            // Update User Fields
            if (isset($validatedData['password'])) {
                $validatedData['password'] = bcrypt($validatedData['password']);
            }
            $validatedData['description'] = $request->input('description', $user->description);
            $user->update($validatedData);

            if ($request->hasFile('avatar')) {
                // Validate the file
                $request->validate(
                    [
                        'avatar' => 'sometimes|file|mimes:jpg,jpeg,png|max:2048', // Adjust validation rules as needed
                    ],
                    [
                        'avatar.mimes' => 'រួបតំណាងត្រូវតែជារួបភាព (avatar)'
                    ]
                );
                $file = $request->file('avatar');
                $school = School::find($user->school_id);
                $avatar = $this->fileService->uploadFile($file, $school->school_name_en, 'avatar');
                $user->update(['avatar' => $avatar]);
            }

            // Handle Roles
            if ($request->has('roles')) {
                $roles = json_decode($validatedData['roles'], true);

                // Detach existing roles and attach new ones
                $user->roles()->sync($roles);
            }

            // Refresh the user to get updated relationships
            $user->refresh();

            // Return updated User with Roles (without pivot)
            $user = User::with('school', 'roles')->findOrFail($user->id);
            $user->roles = $user->roles->makeHidden('pivot');

            return response()->json([
                'message' => 'User updated successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     *  Change Password
     */
    public function changePassword(Request $request, string $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'password' => ['required', 'string', 'min:8', 'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/'],
                'confirm_password' => 'required|string|same:password',
            ], [
                'password.required' => 'The Password field is required',
                'password.min' => 'The Password must be at least 8 characters',
                'password.regex' => 'The Password must contain at least one letter and one number',
                'confirm_password.same' => 'The Confirm Password field confirmation does not match',
            ]);

            $user->password = bcrypt($request->password);
            $user->save();

            return response()->json([
                'message' => 'Password changed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Remove the specified user.
     */
    public function destroy(string $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
