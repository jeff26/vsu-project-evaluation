<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project; // Ensure you have a Project model created
use App\Models\ProjectEvaluator;
use App\Models\ProjectTrust;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    /**
     * Display a listing of the institutional projects.
     */
    public function index(): JsonResponse
    {
       $label = request()->label ?? null;
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

            ->when($label && $label !== 'admin', function ($query) use ($label) {
                $query->where('label', '=', $label);
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
            'title'              => 'required|string|max:1000',
            'project_thrusts_id' => 'required',
            'unit_center'        => 'nullable|string|max:255',
            'attachment'         => 'nullable|file|mimes:pdf|max:10240', // Validates it is a PDF under 10MB
            'label'         => 'required'
        ]);

        // Prepare the basic text payload
        $data = [
            'title'              => $validated['title'],
            'project_thrusts_id' => $validated['project_thrusts_id'],
            'unit_center'        => $validated['unit_center'] ?? 'Unassigned Center',
            'label'        => $validated['label'],
        ];

        // Handle the file upload if one exists
        if ($request->hasFile('attachment')) {
            // Stores the file in storage/app/public/project_attachments
            $data['attachment_path'] = $request->file('attachment')->store('project_attachments', 'public');
        }

        $project = Project::create($data);
        // Find all Evaluator profiles assigned to this specific Thrust
        $evaluatorEmails = ProjectEvaluator::where('project_thrusts_id', $validated['project_thrusts_id'])
            ->pluck('email');

        // Find the actual User account IDs that match those emails
        $userIds = User::whereIn('email', $evaluatorEmails)
            ->pluck('id')
            ->toArray();

        // Auto-assign them to the project via the pivot table
        // sync() automatically adds new assignments and drops unselected ones cleanly
        $project->evaluators()->sync($userIds);

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
            'title'              => 'required|string|max:1000',
            'project_thrusts_id' => 'required',
            'unit_center'        => 'nullable|string|max:255',
            'attachment'         => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data = [
            'title'              => $validated['title'],
            'project_thrusts_id' => $validated['project_thrusts_id'],
            'unit_center'        => $validated['unit_center'] ?? 'Unassigned Center',
        ];

        // Handle file replacement
        if ($request->hasFile('attachment')) {
            // Delete the old file from the server to save space
            if ($project->attachment_path) {
                Storage::disk('public')->delete($project->attachment_path);
            }

            // Save the new file
            $data['attachment_path'] = $request->file('attachment')->store('project_attachments', 'public');
        }

        $project->update($data);

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
        $label = request()->label ?? null;
        // Serving standard institutional focus lines dynamically
        $thrusts = ProjectTrust::query()->when($label && $label !== 'admin', function ($query) use ($label) {
            $query->where('label', '=', $label);
        })->get();

        return response()->json($thrusts, 200);
    }

    public function assignEvaluators(Request $request, $id): JsonResponse
    {
        $project = Project::findOrFail($id);

        // getuserid


        // sync() automatically adds new assignments and drops unselected ones cleanly
        $project->evaluators()->sync($request->input('evaluator_ids', []));

        return response()->json([
            'status' => 'success',
            'message' => 'Project evaluation panel configuration matrix updated.'
        ], 200);
    }

    public function getProjectEvaluators(): JsonResponse
    {
        $thrust_id = request()->thrust_id;
        $evaluators =  ProjectEvaluator::query()
            ->leftJoin('users', 'project_evaluators.email', '=', 'users.email')
            ->select('users.*')
            ->where('project_evaluators.project_thrusts_id', '=', $thrust_id)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $evaluators
        ], 200);
    }
}
