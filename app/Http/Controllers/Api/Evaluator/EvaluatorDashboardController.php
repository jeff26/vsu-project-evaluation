<?php

namespace App\Http\Controllers\Api\Evaluator;

use App\Http\Controllers\Controller;
use App\Models\Evaluations;
use App\Models\Project;
use App\Models\ProjectEvaluator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EvaluatorDashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $thrust_id = request()->thrust_id;

        // check if the user is a chairperson
        $evaluatorRole = ProjectEvaluator::query()
            ->where('email', '=', $user->email)
            ->where('project_thrusts_id', '=', $thrust_id)
            ->where('role', '=', 'Chairperson')->first();
        $isChairperson = (bool)$evaluatorRole;

        // Fetch all institutional projects where this user is assigned as an evaluator
        $assignedProjects = Project::where('project_thrusts_id', '=', $thrust_id)->
        whereHas('evaluators', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with(['evaluations' => function ($query) use ($user) {
                // Eager load only THIS evaluator's score for the project
                $query->where('evaluator_id', $user->id);
            }])
            ->get();

        // Map and format data with conditional status parameters
        $mappedProjects = $assignedProjects->map(function ($project) {
            $evaluation = $project->evaluations->first();

            return [
                'id' => $project->id,
                'title' => $project->title,
                'unit_center' => $project->unit_center ?? 'Unassigned Unit',
                'status' => $evaluation ? $evaluation->status: 'pending',
                'score' => $evaluation ? $evaluation->final_calculated_score : null,
                'evaluated_at' => $evaluation ? $evaluation->created_at->format('M d, Y') : null,
                'attachment' => $project->attachment_path,
            ];
        });

        // Compute metric aggregates for dashboard counter badges
        $total = $mappedProjects->count();
        $completed = $mappedProjects->where('status', 'completed')->count();
        $pending = $mappedProjects->where('status', 'pending')->count();
        $draft = $mappedProjects->where('status', 'draft')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'metrics' => [
                    'total_assigned' => $total,
                    'completed_evaluations' => $completed,
                    'pending_evaluations' => $pending,
                    'draft_evaluations' => $draft,
                ],
                'projects' => $mappedProjects,
                'isChairperson' => $isChairperson,
                'chairpersonData' => $evaluatorRole
            ]
        ], 200);
    }

    public function getMembersRating(Request $request): JsonResponse
    {
        // 1. Validate
        $request->validate([
            'thrust_id' => 'required|exists:project_thrusts,id'
        ]);

        $thrust_id = $request->input('thrust_id');
        $project_id = $request->input('project_id') ?? null;

        //$projects = Project::where('project_thrusts_id', '=', $thrust_id)->withAvg('evaluationScores', 'rating')->get();
        $projects = Evaluations::query()->leftJoin('projects', 'evaluations.project_id', '=', 'projects.id')
            ->leftJoin('users', 'evaluations.evaluator_id', '=', 'users.id')
            ->select('projects.title', 'users.name', 'evaluations.*')
            ->when($project_id, function ($query) use ($project_id) {
                $query->where('projects.id', $project_id);
            })
            ->where('projects.project_thrusts_id', '=', $thrust_id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $projects
        ], 200);
    }

    public function getEvaluatorsThrust(Request $request): JsonResponse
    {
        $email = $request->input('email');
        $data = ProjectEvaluator::query()->where('email', '=', $email)->with('thrust')->get()->map(function ($q) {
            $q->thrust_name = $q->thrust->name;
            $q->thrust_id = $q->thrust->id;

            return $q;
        });
        return response()->json([
            'status' => 'success',
            'data' => $data
        ], 200);
    }
}
