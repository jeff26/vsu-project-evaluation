<?php

namespace App\Http\Controllers\Api\Evaluator;

use App\Http\Controllers\Controller;
use App\Models\EvaluationCriteria;
use App\Models\Project;
use App\Models\Evaluations;
use App\Models\EvaluationCategories;
use App\Models\EvaluationScores;
use App\Models\ProjectMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluationController extends Controller
{
    /**
     * Fetch all projects assigned to the authenticated evaluator panelist.
     * Route: GET /api/evaluator/projects
     *
     * @return JsonResponse
     */
    public function getAssignedProjects(): JsonResponse
    {
        $evaluatorId = auth()->id();

        // Fetches projects linked to this evaluator via an 'evaluators' relationship or pivot table
        $assignedProjects = Project::whereHas('evaluators', function ($query) use ($evaluatorId) {
            $query->where('user_id', $evaluatorId);
        })
            ->with(['thrust']) // Eager load thrust areas if configured
            ->get();

        return response()->json($assignedProjects, 200);
    }

    /**
     * Fetch the structural rubric (Categories + Criteria) alongside existing scores.
     * Route: GET /api/evaluator/projects/{id}/evaluation-form
     *
     * @param int|string $projectId
     * @return JsonResponse
     */
    public function getFormStructure($projectId): JsonResponse
    {
        $evaluatorId = auth()->id();
        $User = User::query()->where('id', '=', $evaluatorId)->first();

        // 1. Guard check: Ensure target project existence
        $projectExists = Project::where('id', $projectId)->exists();
        if (!$projectExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'The requested project workspace record does not exist.'
            ], 404);
        }

        // 2. Fetch the entire grading template framework sorted cleanly
        $categories = EvaluationCategories::where('label', $User->label)->with(['criteria' => function ($query) {
            $query->orderBy('id', 'asc');
        }])->orderBy('id', 'asc')->get();

        // 3. Look up if a grading record already exists for this specific reviewer assignment
        $existingEvaluation = Evaluations::where('project_id', $projectId)
            ->where('evaluator_id', $evaluatorId)
            ->first();

        // 4. Flatten score matrices into a rapid [criterion_id => rating] look-up array
        $existingScores = [];
        if ($existingEvaluation) {
            $existingScores = EvaluationScores::where('evaluation_id', $existingEvaluation->id)
                ->pluck('rating', 'criterion_id')
                ->toArray();
        }

        $projectLeader = ProjectMember::where('project_id', $projectId)
            ->first();

        return response()->json([
            'status' => 'success',
            'projectLeader' => $projectLeader ? $projectLeader->name : 'Project Leader not exist',
            'categories' => $categories,
            'existing_evaluation' => $existingEvaluation,
            // Cast empty PHP arrays to a JSON object {} so the frontend v-model binds properly
            'existing_scores' => empty($existingScores) ? (object)[] : $existingScores
        ], 200);
    }

    /**
     * Submit or save structural scorecard evaluation changes.
     * Route: POST /api/evaluator/projects/{id}/submit-evaluation
     *
     * @param Request $request
     * @param int|string $projectId
     * @return JsonResponse
     */
    public function submitEvaluation(Request $request, $projectId): JsonResponse
    {
        $evaluatorId = auth()->id();

        // 1. Validate submission payload architecture
        $request->validate([
            'scores' => 'required|array',
            'scores.*.criterion_id' => 'required|exists:evaluation_criteria,id',
            'scores.*.rating' => 'required|numeric|min:0',
            'comments' => 'nullable|string',
            'is_draft' => 'required|boolean'
        ]);

        // 2. Wrap operations inside a DB transaction to guarantee data integrity
        try {
            $evaluation = DB::transaction(function () use ($request, $projectId, $evaluatorId) {

                // Pull or instantiate the master evaluation parent record
                $evaluation = Evaluations::updateOrCreate(
                    [
                        'project_id' => $projectId,
                        'evaluator_id' => $evaluatorId
                    ],
                    [
                        'comments' => $request->input('comments'),
                        // Marks it as 'pending' for drafts, or 'completed' to permanently lock edits
                        'status' => $request->input('is_draft') ? 'draft' : 'completed',
                        'final_calculated_score' => 0 // Calculated dynamically below
                    ]
                );

                $totalCalculatedScore = 0;

                // Loop through individual line criteria entries
                foreach ($request->input('scores') as $scoreItem) {

                    // Pull specific criterion profile rules to check maximum allowed weights
                    $criterion = EvaluationCriteria::query()
                        ->where('id', $scoreItem['criterion_id'])
                        ->first();

                    $rating = floatval($scoreItem['rating']);

                    // Server-Side Guard: Enforce weight ceilings in case the UI is bypassed
                    if ($rating > $criterion->weight_percentage) {
                        $rating = $criterion->weight_percentage;
                    }

                    // Save or update row item scores
                    EvaluationScores::updateOrCreate(
                        [
                            'evaluation_id' => $evaluation->id,
                            'criterion_id' => $scoreItem['criterion_id']
                        ],
                        ['rating' => $rating]
                    );

                    $totalCalculatedScore += $rating;
                }

                // Apply computed master tally back into parent anchor record
                $evaluation->update([
                    'final_calculated_score' => $totalCalculatedScore
                ]);

                return $evaluation;
            });

            return response()->json([
                'status' => 'success',
                'message' => $request->input('is_draft')
                    ? 'Draft checkpoint changes cached securely.'
                    : 'Formal evaluation evaluation submitted and locked successfully.',
                'data' => $evaluation
            ], 200);

        } catch (\Exception $e) {
            Log::error('Evaluation processing transactional failure: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An internal database error occurred while calculating your scorecard. Changes were safely rolled back.'
            ], 500);
        }
    }
}
