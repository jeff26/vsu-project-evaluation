<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember; // Assuming this is your "Evaluator/User" table
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        // 1. Total counts
        $totalUsers = User::query()->where('role', '=', 'evaluator')->count();
        $totalProjects = Project::count();

        // 2. Aggregate projects by their center/unit
        // Uses raw SQL to group and count efficiently
        $projectsByUnit = Project::query()
            ->select('unit_center', DB::raw('count(*) as total'))
            ->groupBy('unit_center')
            ->get();

        // 3. Get recent project activity
        $recentProjects = Project::query()
            ->select('id', 'title', 'unit_center')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_users'      => $totalUsers,
                'total_projects'   => $totalProjects,
                'projects_by_unit' => $projectsByUnit,
                'recent_projects'  => $recentProjects
            ]
        ], 200);
    }
}
