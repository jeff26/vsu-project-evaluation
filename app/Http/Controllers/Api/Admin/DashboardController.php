<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluations;
use App\Models\EvaluationScores;
use App\Models\Project;
use App\Models\ProjectMember; // Assuming this is your "Evaluator/User" table
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // 1. Completion Rate & Status Breakdown
        $label = request()->label ?? null;
        $query = Evaluations::query()->leftJoin('projects', 'evaluations.project_id', '=', 'projects.id')
            ->leftJoin('users', 'evaluations.evaluator_id', '=', 'users.id')
            ->select('evaluations.*')
            ->when($label && $label != 'admin', function ($query) use ($label) {
                $query->where('projects.label', $label);
            });
        $totalEvaluations = $query->count();
        $completedCount = $query->where('status', 'completed')->count();
        $pendingCount = $query->where('status', 'pending')->count();
        $draftCount = $query->where('status', 'draft')->count();

        $completionRate = $totalEvaluations > 0 ? round(($completedCount / $totalEvaluations) * 100) : 0;

        // 2. Score Distribution (Grouping projects by score brackets)
        $evaluation_scores = DB::table('evaluation_scores')
            ->join('evaluations', 'evaluation_scores.evaluation_id', '=', 'evaluations.id')
            ->join('projects', 'evaluations.project_id', '=', 'projects.id')
            ->select('evaluation_scores.*')
            ->when($label && $label != 'admin', function ($query) use ($label) {
                $query->where('projects.label', $label);
            });

        $scores = $evaluation_scores->pluck('rating');
        $distribution = [
            $scores->whereBetween('rating', [1, 2.99])->count(),
            $scores->whereBetween('rating', [2, 2.99])->count(),
            $scores->whereBetween('rating', [3, 3.99])->count(),
            $scores->whereBetween('rating', [4, 5])->count(),
        ];

        // 3. Category Radar Data (Averages per Rubric Category)
        $radarData = DB::table('evaluation_scores')
            ->join('evaluation_criteria', 'evaluation_scores.criterion_id', '=', 'evaluation_criteria.id')
            ->join('evaluation_categories', 'evaluation_criteria.category_id', '=', 'evaluation_categories.id')
            ->select('evaluation_categories.name as category', DB::raw('AVG(evaluation_scores.rating) as score'))
            ->groupBy('evaluation_categories.id', 'evaluation_categories.name')
            ->get();

        $avg_score = $evaluation_scores->avg('rating');

        $data = [
            'total_users' => User::where('role', 'evaluator')
                ->when($label && $label != 'admin', function ($query) use ($label) {
                    $query->where('label', $label);
                })->count(),
            'total_projects' => Project::when($label && $label != 'admin', function ($query) use ($label) {
                                    $query->where('projects.label', $label);
                                })->count(),
            'completion_rate' => $completionRate,
            'avg_score' => number_format($avg_score ?? 0, 2),

            // Chart Data Objects
            'status_data' => [
                'completed' => $completedCount,
                'pending' => $pendingCount,
                'draft' => $draftCount
            ],
            'score_distribution' => $distribution,
            'radar_data' => $radarData,

            // Tables
            'recent_projects' => Project::when($label && $label != 'admin', function ($query) use ($label) {
                $query->where('projects.label', $label);
            })->orderByDesc('created_at')->take(5)->get(),
            'evaluator_workload' => User::where('role', 'evaluator')
                ->when($label && $label != 'admin', function ($query) use ($label) {
                    $query->where('label', $label);
                })
                ->withCount(['authoredEvaluations as assigned_count'])
                ->withCount(['authoredEvaluations as completed_count' => function ($query) {
                    $query->where('status', 'completed');
                }])
                ->having('assigned_count', '>', 0)
                ->orderByDesc('assigned_count')
                ->get()
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
