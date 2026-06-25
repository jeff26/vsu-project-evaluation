<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectTrust; // Uses the model we configured earlier
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectThrustController extends Controller
{
    /**
     * Display a listing of institutional thrust focus areas.
     */
    public function index(Request $request): JsonResponse
    {
        $thrusts = ProjectTrust::query()
            // Automatically appends an internal count column: projects_count
            ->withCount(['projects'])

            // 🌟 Use when() to filter by name if search input is filled
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return response()->json($thrusts, 201);
    }

    /**
     * Store a newly created strategic thrust area.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_thrusts,name',
        ]);

        $thrust = ProjectTrust::create([
            'name' => $validated['name']
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Strategic thrust line registered successfully.',
            'data'    => $thrust
        ], 201);
    }

    /**
     * Update the specified strategic thrust.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $thrust = ProjectTrust::find($id);

        if (!$thrust) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Thrust record context not found.'
            ], 404);
        }

        // Validate uniqueness but ignore the record we are currently updating
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:project_thrusts,name,' . $id,
        ]);

        $thrust->update([
            'name' => $validated['name']
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Strategic thrust tracking updated successfully.',
            'data'    => $thrust
        ], 200);
    }

    /**
     * Remove the specified strategic thrust from storage.
     */
    public function destroy($id): JsonResponse
    {
        $thrust = ProjectTrust::find($id);

        if (!$thrust) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Thrust record context not found.'
            ], 404);
        }

        // 🛡️ Data Integrity Check: Block deletion if projects are still using it
        if ($thrust->projects()->exists()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Operational Protection Error: Cannot remove this focus area while active projects remain linked to it.'
            ], 422);
        }

        $thrust->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Strategic thrust removed successfully.'
        ], 200);
    }
}
