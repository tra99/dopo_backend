<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\FileService;
use Carbon\Carbon;

class AuthController extends Controller
{
    protected $fileService;

    public function __construct(
        FileService $fileService,
    ) {
        $this->fileService = $fileService;
    }


    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users',
                'password' => 'required|string',
                'c_password' => 'required|same:password',
                'school_id' => 'required|exists:schools,id'
            ]);

            $user = new User([
                'name' => $request->name,
                'email' => $request->email,
                'status' => 1,
                'latest_login' => Carbon::now(),
                'password' => bcrypt($request->password),
                'school_id' => $request->school_id
            ]);

            if ($user->save()) {
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->plainTextToken;

                return response()->json([
                    'message' => 'Successfully created user!',
                    'accessToken' => $token,
                ], 201);
            } else {
                return response()->json(['error' => 'Provide proper details']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     */

    public function login(Request $request)
    {
        try {
            // Validate incoming request data
            $request->validate([
                'email'    => 'required|string|email',
                'password' => 'required|string',
                'fcm_token' => 'nullable|string'
            ]);

            // Attempt to authenticate using email and password
            $credentials = $request->only('email', 'password');
            if (!auth()->attempt($credentials)) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            // Retrieve the user based on email
            $user = User::where('email', $request->email)->first();

            // Check if the user's account is active
            if ($user->status == 0) {
                return response()->json(['message' => 'Your account is not active'], 401);
            }

            // Update last login time and FCM token (if provided)
            $user->lastest_login = Carbon::now();
            if ($request->filled('fcm_token')) {
                $user->fcm_token = $request->fcm_token;
            }
            $user->save();

            // Revoke any existing tokens for the user
            // $request->user()->tokens()->delete();

            // Refresh the authenticated user
            $user = auth()->user();

            // Prepare roles data for token metadata (using pluck to create an id => name array)
            $roles = $user->roles->pluck('name', 'id')->toArray();

            // Retrieve school information; if it's a one-to-one relation, fetch the related model
            $school = $user->school ? $user->school->toArray() : [];

            // Create a personal access token with additional metadata and a custom expiration (1 week)
            $tokenResult = $user->createToken(
                'Personal Access Token',
                ['roles' => $roles, 'school' => $school],
                Carbon::now()->addWeek()
            );
            $token = $tokenResult->plainTextToken;

            // Get the latest token model and update its expiration explicitly if needed
            $tokenModel = $user->tokens()->latest()->first();
            $expiresAt = Carbon::now()->addWeek();
            $tokenModel->update(['expires_at' => $expiresAt]);

            // Refresh the user data and return a successful login response
            $user->refresh();

            return response()->json([
                'message'        => 'Login successful!',
                'accessToken'    => $token,
                'tokenExpiresAt' => $tokenModel->expires_at,
                'user'           => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function refreshToken(Request $request)
    {
        try {
            // Get the authenticated user
            $user = $request->user();

            // Delete the current token
            $currentToken = $user->currentAccessToken();
            if ($currentToken) {
                $currentToken->delete();
            }

            // Rebuild roles (same as in your login method)
            $roles = $user->roles->makeHidden('pivot')->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name
                ];
            })->values()->toArray();

            // Create a new token
            $tokenResult = $user->createToken('Personal Access Token', [$roles]);
            $newToken = $tokenResult->plainTextToken;

            // Get the token model and update expiration (for example, 1 week from now)
            $newTokenModel = $user->tokens()->latest()->first();
            $expiresAt = Carbon::now()->addWeek();
            $newTokenModel->update(['expires_at' => $expiresAt]);

            return response()->json([
                'message' => 'Token refreshed successfully!',
                'accessToken' => $newToken,
                'tokenExpiresAt' => $newTokenModel->expires_at,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        $user = auth()->user();
        $user = $user->load('school');
        $user = $user->load('roles');
        return response()->json($user);
    }

    public function update(Request $request)
    {
        try {
            $user = auth()->user();

            // Validation Rules
            $validatedData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|unique:users,email,' . $user->id,
                'status' => 'sometimes|boolean',
                'school_id' => 'sometimes|exists:schools,id',
                'description' => 'sometimes|string',
            ], [
                'name.max' => 'The Name field must not exceed 255 characters.',
                'email.unique' => 'The Email already exists.',
                'school_id.exists' => 'The selected School is invalid.',
            ]);

            $validatedData['description'] = $request->input('description', $user->description);
            $user->update($validatedData);

            // If avatar is updated, handle the file upload
            if ($request->hasFile('avatar')) {
                // Validate the file
                $request->validate(
                    [
                        'avatar' => 'sometimes|file|mimes:jpg,jpeg,png|max:2048', // Adjust validation rules as needed
                    ],
                    [
                        'avatar.mimes' => 'The avatar file must be a valid image (jpg, jpeg, png).'
                    ]
                );
                $file = $request->file('avatar');
                $school = School::find($user->school_id);
                $avatar = $this->fileService->uploadFile($file, $school->school_name_en, 'avatar');
                $user->update(['avatar' => $avatar]);
            }

            // Revoke all existing tokens to ensure the old token is no longer valid
            // $user->tokens()->delete();

            // Generate a new token after updating the data
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            // Return updated User with Roles (without pivot)
            $user = User::with('school', 'roles')->findOrFail($user->id);
            $user->roles = $user->roles->makeHidden('pivot');

            return response()->json([
                'message' => 'កែប្រែទិន្ន័យបានជោគជ័យ',
                'accessToken' => $token,
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            // Validate incoming request data
            $request->validate(
                [
                    'current_password' => 'required|string',
                    'new_password' => [
                        'required',
                        'string',
                        'min:6',
                        'regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]+$/',
                        'different:current_password',
                    ],
                    // 'new_password' => 'required|string|min:6|different:current_password',
                    'c_new_password' => 'required|same:new_password',
                ],
                [
                    'current_password.required' => 'សូមបញ្ជូលពាក្យសម្ងាត់បច្ចុប្បន្ន (current_password)',
                    'new_password.required' => 'សូមបញ្ជូលពាក្យសម្ងាត់ថ្មី (new_password)',
                    'new_password.min' => 'ពាក្យសម្ងាត់ថ្មីត្រូវមានទិន្ន័យនៃ 6 អក្សរ new_password',
                    'new_password.different' => 'សូមបញ្ជូលពាក់្យសម្ងាត់ថ្មីខុសពីពាក្យសម្ងាត់បច្ចុប្បន្ន',
                    'new_password.regex' => 'ពាក្យសម្ងាត់ត្រូវតែមានអក្សរ និងលេខ new_password',
                    'c_new_password.required' => 'សូមបញ្ជាក់ពាក្យសម្ងាត់ថ្មី c_new_password',
                    'c_new_password.same' => 'ពាក្យសម្ងាត់ថ្មីនិងបញ្ជូលពាក្យសម្ងាត់ថ្មីមិនត្រូវគ្នាទេ',
                ]
            );

            // Get the authenticated user
            $user = auth()->user();

            // Check if the current password is correct
            if (!\Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Current password is incorrect.'], 401);
            }

            // Update the user's password
            $user->password = bcrypt($request->new_password);
            $user->save();

            return response()->json([
                'message' => 'កែប្រែពាក្យសម្ងាត់ដោយជោគជ័យ!'
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->tokens()->delete();
            $request->user()->fcm_token = null;
            $request->user()->save();
            return response()->json([
                'message' => 'Successfully logged out'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
