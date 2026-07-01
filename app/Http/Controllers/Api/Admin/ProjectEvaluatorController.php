<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectEvaluator;
use App\Models\ProjectTrust;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectEvaluatorController extends Controller
{
    /**
     * Display a listing of evaluators with filtering capabilities.
     */
    public function index(Request $request)
    {
        $query = ProjectEvaluator::with('thrust');

        // Handle the search query from the frontend
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Handle the Thrust dropdown filter
        if ($request->filled('thrust_id')) {
            $query->where('project_thrusts_id', $request->thrust_id);
        }

        // Return the results
        $evaluators = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $evaluators
        ]);
    }

    /**
     * Store a newly created evaluator and automatically trigger User creation.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'role' => 'required|in:Chairperson,Member',
            'project_thrusts_id' => 'required|exists:project_thrusts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Creating this will automatically trigger the 'booted' model event
        // to create the User login credentials
        $evaluator = ProjectEvaluator::create($request->all());

        return response()->json([
            'message' => 'Evaluator created successfully.',
            'data' => $evaluator->load('thrust')
        ], 201);
    }

    /**
     * Update the specified evaluator and sync changes to their User account.
     */
    public function update(Request $request, $id)
    {
        $evaluator = ProjectEvaluator::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // Ignore the current evaluator's email when checking for uniqueness
            'email' => 'required|email',
            'role' => 'required|in:Chairperson,Member',
            'project_thrusts_id' => 'required|exists:project_thrusts,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Capture old email to find the associated User account
        $oldEmail = $evaluator->email;

        // Update the Evaluator profile
        $evaluator->update($request->all());

        // Sync the changes to the User account (if it exists)
        $user = User::where('email', $oldEmail)->first();
        if ($user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email, // Safely updates login email
            ]);
        }

        return response()->json([
            'message' => 'Evaluator updated successfully.',
            'data' => $evaluator->load('thrust')
        ]);
    }

    /**
     * Remove the evaluator and their login access.
     */
    public function destroy($id)
    {
        $evaluator = ProjectEvaluator::findOrFail($id);
        $email = $evaluator->email;

        // Delete the Evaluator profile
        $evaluator->delete();

        // check first if the user is not use in other program
        $thrust = ProjectEvaluator::query()->where('email', '=', $email)->exists();
        if (!$thrust) {
            // Delete the corresponding User account to revoke login access
            User::where('email', $email)->delete();
        }

        return response()->json([
            'message' => 'Evaluator and associated system access removed successfully.'
        ]);
    }
}
