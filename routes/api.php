<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ProjectController;
use App\Http\Controllers\Api\Admin\ProjectEvaluatorController;
use App\Http\Controllers\Api\Admin\ProjectMemberController;
use App\Http\Controllers\Api\Admin\ProjectThrustController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use App\Http\Controllers\Api\Evaluator\EvaluationController;
use App\Http\Controllers\Api\Evaluator\EvaluatorDashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

// Public Route
Route::post('/login', [AuthController::class, 'authenticate']);

// Protected Routes (Must be logged in)

// Inside your authenticated admin route group middleware...
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {

    Route::get('/evaluators', [ProjectEvaluatorController::class, 'index']);
    Route::post('/evaluators', [ProjectEvaluatorController::class, 'store']);
    Route::put('/evaluators/{id}', [ProjectEvaluatorController::class, 'update']);
    Route::delete('/evaluators/{id}', [ProjectEvaluatorController::class, 'destroy']);

    Route::post('projects/{id}/assign-evaluators', [ProjectController::class, 'assignEvaluators']);
    Route::get('projects/evaluators', [ProjectController::class, 'getProjectEvaluators']);

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::apiResource('members', ProjectMemberController::class);

    // Core CRUD Resource Route for Thrusts management
    Route::apiResource('thrusts', ProjectThrustController::class);

    Route::get('projects/thrusts', [ProjectController::class, 'thrusts']);
    // Core CRUD API Endpoints for Projects
    Route::apiResource('projects', ProjectController::class);

    Route::apiResource('users', UserManagementController::class)->only([
        'index', 'store', 'update', 'destroy'
    ]);

});

Route::middleware(['auth:sanctum'])->prefix('evaluator')->group(function () {

    Route::get('/over-all-rating', [EvaluatorDashboardController::class, 'getOverAllRating']);
    Route::get('/members-rating', [EvaluatorDashboardController::class, 'getMembersRating']);
    Route::get('/thrusts', [EvaluatorDashboardController::class, 'getEvaluatorsThrust']);

    Route::get('/dashboard-metrics', [EvaluatorDashboardController::class, 'index']);
    Route::get('/projects', [EvaluationController::class, 'getAssignedProjects']);
    Route::get('/projects/{id}/evaluation-form', [EvaluationController::class, 'getFormStructure']);
    // 3. Post scorecard adjustments (Processes both Draft updates and Final locked states)
    Route::post('projects/{id}/submit-evaluation', [EvaluationController::class, 'submitEvaluation']);

});
