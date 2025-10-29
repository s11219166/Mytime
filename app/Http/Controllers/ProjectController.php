<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Project::with(['creator', 'teamMembers']);

        // Role-based filtering
        if ($user->isUser()) {
            // Regular users can only see projects they're assigned to or created
            $query->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('teamMembers', function($q) use ($user) {
                      $q->where('users.id', $user->id);
                  });
            });
        }
        // Admins can see all projects (no filtering needed)

        // Filter by status if provided
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Validate per_page parameter
        $perPageOptions = [10, 25, 50, 100];
        $perPage = $request->input('per_page', 25);
        if (!in_array((int)$perPage, $perPageOptions)) {
            $perPage = 25;
        }

        $projects = $query->latest()->paginate($perPage)->appends([
            'status' => $request->status,
            'search' => $request->search,
            'per_page' => $perPage,
        ]);

        // Get project statistics based on user role
        if ($user->isAdmin()) {
            $stats = [
                'total' => Project::count(),
                'active' => Project::whereIn('status', ['active', 'inprogress'])->count(),
                'pending' => Project::whereIn('status', ['review_pending', 'awaiting_input', 'paused'])->count(),
                'completed' => Project::where('status', 'completed')->count(),
                'overdue' => Project::where('end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->count(),
            ];
        } else {
            // Stats for regular users (only their projects)
            $userProjectIds = Project::where('created_by', $user->id)
                ->orWhereHas('teamMembers', function($q) use ($user) {
                    $q->where('users.id', $user->id);
                })->pluck('id');

            $stats = [
                'total' => $userProjectIds->count(),
                'active' => Project::whereIn('id', $userProjectIds)->whereIn('status', ['active', 'inprogress'])->count(),
                'pending' => Project::whereIn('id', $userProjectIds)->whereIn('status', ['review_pending', 'awaiting_input', 'paused'])->count(),
                'completed' => Project::whereIn('id', $userProjectIds)->where('status', 'completed')->count(),
            ];
        }

        return view('projects.index', compact('projects', 'stats', 'perPage'));
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        // Only admins can create projects
        if (Auth::user()->isUser()) {
            abort(403, 'Unauthorized action. Only administrators can create projects.');
        }

        $users = User::where('id', '!=', Auth::id())->get();
        return view('projects.create', compact('users'));
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,completed,inprogress,awaiting_input,cancelled,paused,revision_needed,review_pending,overdue',
            'priority' => 'required|in:low,medium,high,urgent',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'progress' => 'nullable|integer|min:0|max:100',
            'tags' => 'nullable|string',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'team_roles' => 'nullable|array',
            'team_roles.*' => 'in:member,lead,manager',
        ]);

        // Process tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Create project
        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? 'active',
            'priority' => $validated['priority'] ?? 'medium',
            'budget' => $validated['budget'] ?? null,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'progress' => $validated['progress'] ?? 0,
            'tags' => $validated['tags'] ?? null,
            'created_by' => Auth::id(),
        ]);

        // Attach team members
        if (!empty($validated['team_members'])) {
            $teamData = [];
            foreach ($validated['team_members'] as $index => $userId) {
                $teamData[$userId] = [
                    'role' => $validated['team_roles'][$index] ?? 'member'
                ];
            }
            $project->teamMembers()->attach($teamData);
        }

        return redirect()->route('projects.index')->with('success', 'Project created successfully!');
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if ($user->isUser()) {
            $hasAccess = $project->created_by === $user->id ||
                        $project->teamMembers->contains($user->id);

            if (!$hasAccess) {
                abort(403, 'Unauthorized action. You do not have access to this project.');
            }
        }

        $project->load(['creator', 'teamMembers']);
        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project
     */
    public function edit(Project $project)
    {
        $user = Auth::user();

        // Only admins can edit projects
        if ($user->isUser()) {
            abort(403, 'Unauthorized action. Only administrators can edit projects.');
        }

        $users = User::where('id', '!=', Auth::id())->get();
        $project->load('teamMembers');
        return view('projects.edit', compact('project', 'users'));
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,completed,inprogress,awaiting_input,cancelled,paused,revision_needed,review_pending,overdue,planning',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'nullable|integer|min:0|max:100',
            'tags' => 'nullable|string',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
            'team_roles' => 'nullable|array',
            'team_roles.*' => 'in:member,lead,manager',
        ]);

        // Process tags
        if ($validated['tags']) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Update project
        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'budget' => $validated['budget'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'progress' => $validated['progress'] ?? $project->progress,
            'tags' => $validated['tags'],
        ]);

        // Update team members
        $project->teamMembers()->detach();
        if (!empty($validated['team_members'])) {
            $teamData = [];
            foreach ($validated['team_members'] as $index => $userId) {
                $teamData[$userId] = [
                    'role' => $validated['team_roles'][$index] ?? 'member'
                ];
            }
            $project->teamMembers()->attach($teamData);
        }

        return redirect()->route('projects.index')->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the specified project
     */
    public function destroy(Project $project)
    {
        $user = Auth::user();

        // Only admins can delete projects
        if ($user->isUser()) {
            abort(403, 'Unauthorized action. Only administrators can delete projects.');
        }

        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully!');
    }

    /**
     * Mark project as complete (for regular users)
     */
    public function markComplete(Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if ($user->isUser()) {
            $hasAccess = $project->created_by === $user->id ||
                        $project->teamMembers->contains($user->id);

            if (!$hasAccess) {
                abort(403, 'Unauthorized action. You do not have access to this project.');
            }
        }

        $project->update([
            'status' => 'completed',
            'progress' => 100
        ]);

        return redirect()->back()->with('success', 'Project marked as completed!');
    }

    /**
     * Update project progress
     */
    public function updateProgress(Request $request, Project $project)
    {
        $user = Auth::user();

        // Check if user has access to this project
        if ($user->isUser()) {
            $hasAccess = $project->created_by === $user->id ||
                        $project->teamMembers->contains($user->id);

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
        }

        $validated = $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $project->update([
            'progress' => $validated['progress']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project progress updated successfully!',
            'progress' => $project->progress
        ]);
    }
}
