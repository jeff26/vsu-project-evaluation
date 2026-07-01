<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectMemberController extends Controller
{
    /**
     * Display a listing of project members.
     */
    public function index(Request $request): JsonResponse
    {
        $members = ProjectMember::query()
            ->with(['project']) // Eager load the parent project details

            // Filter by name or email search query
            ->when($request->input('search'), function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })

            // Filter by a specific project selection if provided
            ->when($request->input('project_id'), function ($query, $projectId) {
                $query->where('project_id', $projectId);
            })

            ->latest()
            ->get();

        return response()->json($members, 201);
    }

    /**
     * Store a newly created project member.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255', // Changed to nullable
            'role'       => 'required|in:Program leader,Project member,Project leader,Component leader,Study leader,Project staff', // Enforced enum limits
        ]);

        $member = ProjectMember::create($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Project member assigned successfully.',
            'data'    => $member->load('project')
        ], 201);
    }

    /**
     * Update the specified project member.
     */
    public function update(Request $request, ProjectMember $member): JsonResponse
    {
//        $member = ProjectMember::find($id);
//
//        if (!$member) {
//            return response()->json(['status' => 'error', 'message' => 'Member not found.'], 404);
//        }

        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255', // Changed to nullable
            'role'       => 'required|in:Program leader,Project member,Project leader,Component leader,Study leader,Project staff', // Enforced enum limits
        ]);

        $member->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Member assignment records updated.',
            'data'    => $member->load('project')
        ], 200);
    }

    /**
     * Remove the specified project member from storage.
     */
    public function destroy(ProjectMember $member): JsonResponse
    {
//        $member = ProjectMember::find($id);
//
//        if (!$member) {
//            return response()->json(['status' => 'error', 'message' => 'Member not found.'], 404);
//        }

        $member->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Member removed from project roster successfully.'
        ], 200);
    }
}
