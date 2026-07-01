<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    // 1. LIST ALL USERS
    public function index(): JsonResponse
    {
        // Capture the input safely
        $label = request()->label ?? null;

        $users = User::select('id', 'name', 'email', 'role', 'label', 'created_at')
            // Check the condition in the first parameter
            ->when($label && $label != 'admin', function ($query) use ($label) {
                // Now $label refers to your actual request variable
                $query->where('label', $label);
            })
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $users,
            'label' => $label
        ], 200);
    }

    // 2. CREATE NEW USER
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => ['required', Rule::in(['admin', 'evaluator'])],
            'label'     => 'required'
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User account generated successfully.',
            'data' => $user
        ], 201);
    }

    public function show($id): JsonResponse
    {
        $user = User::select('id', 'name', 'email', 'role', 'label', 'created_at')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ], 200);
    }

    // 3. UPDATE EXISTING USER
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role'     => ['required', Rule::in(['admin', 'evaluator'])],
            'label' => 'required'
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // Keep current password if left blank
        }

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User parameter records updated.',
            'data' => $user
        ], 200);
    }

    // 4. DELETE USER RECORD
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Safeguard: Prevent Admin from deleting their own active session account
        if (auth()->id() === $user->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Action denied. You cannot terminate your own active administrative session.'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Account permanently dropped from institutional registries.'
        ], 200);
    }
}
