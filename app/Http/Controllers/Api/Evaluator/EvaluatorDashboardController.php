<?php

namespace App\Http\Controllers\Api\Evaluator;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EvaluatorDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Fetch all institutional projects where this user is assigned as an evaluator
        $assignedProjects = Project::whereHas('evaluators', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['evaluations' => function ($query) use ($user) {
                // Eager load only THIS evaluator's score for the project
                $query->where('evaluator_id', $user->id);
            }])
            ->get();

        // 2. Map and format data with conditional status parameters
        $mappedProjects = $assignedProjects->map(function ($project) {
            $evaluation = $project->evaluations->first();

            return [
                'id' => $project->id,
                'title' => $project->title,
                'unit_center' => $project->unit_center ?? 'Unassigned Unit',
                'status' => $evaluation ? $evaluation->status: 'pending',
                'score' => $evaluation ? $evaluation->final_calculated_score : null,
                'evaluated_at' => $evaluation ? $evaluation->created_at->format('M d, Y') : null,
            ];
        });

        // 3. Compute metric aggregates for dashboard counter badges
        $total = $mappedProjects->count();
        $completed = $mappedProjects->where('status', 'completed')->count();
        $pending = $mappedProjects->where('status', 'pending')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'metrics' => [
                    'total_assigned' => $total,
                    'completed_evaluations' => $completed,
                    'pending_evaluations' => $pending,
                ],
                'projects' => $mappedProjects
            ]
        ], 200);
    }
}
