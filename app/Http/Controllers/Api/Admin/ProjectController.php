<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project; // Ensure you have a Project model created
use App\Models\ProjectTrust;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    /**
     * Display a listing of the institutional projects.
     */
    public function index(): JsonResponse
    {
       // var_dump(request()->all());
        // Fetch projects ordered by the latest entries
        $projects = Project::query()
            ->with(['thrust','evaluators']) // Eager load your thrust relationship data matrix

            // 🌟 Filter by title if 'search' query variable is present and filled
            ->when(request()->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })

            // 🌟 Filter by relation foreign key if 'thrust_id' variable is passed
            ->when(request()->thrust_id, function ($query, $thrustId) {
                $query->where('project_thrusts_id', $thrustId);
            })

            ->latest()
            ->get();

        // Return a direct JSON array to satisfy Array.isArray() in Vue
        return response()->json($projects, 200);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:1000',
            'project_thrusts_id'      => 'required',
            'unit_center' => 'nullable|string|max:255',
        ]);

        $project = Project::create([
            'title'       => $validated['title'],
            'project_thrusts_id'      => $validated['project_thrusts_id'],
            'unit_center' => $validated['unit_center'] ?? 'Unassigned Center',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Project registered successfully.',
            'data'    => $project
        ], 201);
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Project record not found.'
            ], 404);
        }

        $validated = $request->validate([
            'title'       => 'required|string|max:1000',
            'project_thrusts_id'      => 'required',
            'unit_center' => 'nullable|string|max:255',
        ]);

        $project->update([
            'title'       => $validated['title'],
            'project_thrusts_id'      => $validated['project_thrusts_id'],
            'unit_center' => $validated['unit_center'] ?? 'Unassigned Center',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Project updated successfully.',
            'data'    => $project
        ], 200);
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy($id): JsonResponse
    {
        $project = Project::find($id);

        if (!$project) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Project record not found.'
            ], 404);
        }

        $project->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Project deleted successfully.'
        ], 200);
    }

    public function thrusts(): \Illuminate\Http\JsonResponse
    {
        // Serving standard institutional focus lines dynamically
        $thrusts = ProjectTrust::query()->get();

        return response()->json($thrusts, 200);
    }

    public function assignEvaluators(Request $request, $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'evaluator_ids'   => 'nullable|array',
            'evaluator_ids.*' => 'exists:users,id'
        ]);

        // sync() automatically adds new assignments and drops unselected ones cleanly
        $project->evaluators()->sync($request->input('evaluator_ids', []));

        return response()->json([
            'status' => 'success',
            'message' => 'Project evaluation panel configuration matrix updated.'
        ], 200);
    }
}
