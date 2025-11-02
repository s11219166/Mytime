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
        // Clear any cached queries
        \Illuminate\Support\Facades\Cache::flush();
        
        $user = Auth::user();
        
        // Use raw query to bypass any caching issues
        $baseQuery = \Illuminate\Support\Facades\DB::table('projects')
            ->select('projects.*');
        
        // Role-based filtering
        if ($user->isUser()) {
            // Regular users can only see projects they're assigned to or created
            $baseQuery->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereIn('id', function($subQuery) use ($user) {
                      $subQuery->select('project_id')
                               ->from('project_user')
                               ->where('user_id', $user->id);
                  });
            });
        }
        
        // Filter by status if provided
        if ($request->filled('status') && $request->status !== 'all') {
            $baseQuery->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $baseQuery->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort by due dates (upcoming first), completed projects at the end
        $baseQuery->orderByRaw("CASE 
            WHEN status = 'completed' THEN 2
            WHEN status = 'cancelled' THEN 2
            ELSE 1
        END ASC")
        ->orderByRaw("CASE 
            WHEN end_date IS NULL THEN 1
            ELSE 0
        END ASC")
        ->orderBy('end_date', 'ASC')
        ->latest('updated_at');

        // Validate per_page parameter
        $perPageOptions = [10, 25, 50, 100];
        $perPage = $request->input('per_page', 25);
        if (!in_array((int)$perPage, $perPageOptions)) {
            $perPage = 25;
        }

        // Get raw results and convert to models
        $projectIds = $baseQuery->pluck('id')->toArray();
        
        // Now use Eloquent to load with relationships
        $projects = Project::with(['creator', 'teamMembers'])
            ->whereIn('id', $projectIds)
            ->orderByRaw("CASE 
                WHEN status = 'completed' THEN 2
                WHEN status = 'cancelled' THEN 2
                ELSE 1
            END ASC")
            ->orderByRaw("CASE 
                WHEN end_date IS NULL THEN 1
                ELSE 0
            END ASC")
            ->orderBy('end_date', 'ASC')
            ->latest('updated_at')
            ->paginate($perPage)
            ->appends([
                'status' => $request->status,
                'search' => $request->search,
                'per_page' => $perPage,
            ]);

        // Get project statistics based on user role
        if ($user->isAdmin()) {
            $stats = [
                'total' => \Illuminate\Support\Facades\DB::table('projects')->count(),
                'active' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('status', ['active', 'inprogress'])->count(),
                'pending' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('status', ['review_pending', 'awaiting_input', 'paused'])->count(),
                'completed' => \Illuminate\Support\Facades\DB::table('projects')->where('status', 'completed')->count(),
                'overdue' => \Illuminate\Support\Facades\DB::table('projects')->where('end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->count(),
            ];
        } else {
            // Stats for regular users (only their projects)
            $userProjectIds = \Illuminate\Support\Facades\DB::table('projects')
                ->where('created_by', $user->id)
                ->orWhereIn('id', function($q) use ($user) {
                    $q->select('project_id')
                      ->from('project_user')
                      ->where('user_id', $user->id);
                })
                ->pluck('id');

            $stats = [
                'total' => $userProjectIds->count(),
                'active' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('id', $userProjectIds)->whereIn('status', ['active', 'inprogress'])->count(),
                'pending' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('id', $userProjectIds)->whereIn('status', ['review_pending', 'awaiting_input', 'paused'])->count(),
                'completed' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('id', $userProjectIds)->where('status', 'completed')->count(),
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
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action. Only administrators can delete projects.'
            ], 403);
        }

        try {
            $projectName = $project->name;
            $projectId = $project->id;
            
            // Log the deletion for debugging
            \Illuminate\Support\Facades\Log::info('Deleting project: ' . $projectId . ' - ' . $projectName);
            
            // Delete related records first to avoid foreign key constraints
            try {
                $project->teamMembers()->detach();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Error detaching team members: ' . $e->getMessage());
            }
            
            // Delete the project using raw query to ensure deletion
            $deleted = \Illuminate\Support\Facades\DB::table('projects')
                ->where('id', $projectId)
                ->delete();
            
            if (!$deleted) {
                throw new \Exception('Failed to delete project from database');
            }
            
            // Verify deletion with raw query
            $stillExists = \Illuminate\Support\Facades\DB::table('projects')
                ->where('id', $projectId)
                ->first();
            
            if ($stillExists) {
                throw new \Exception('Project still exists after deletion attempt');
            }
            
            // Clear query cache
            \Illuminate\Support\Facades\Cache::flush();
            
            \Illuminate\Support\Facades\Log::info('Successfully deleted project: ' . $projectId);
            
            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully!'
            ], 200);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error deleting project: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting project: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Get updated projects for real-time updates
     */
    public function getUpdates(Request $request)
    {
        $user = Auth::user();
        $lastUpdate = $request->query('last_update', 0);
        
        // Build base query
        $baseQuery = \Illuminate\Support\Facades\DB::table('projects')
            ->select('projects.*');
        
        // Role-based filtering
        if ($user->isUser()) {
            $baseQuery->where(function($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereIn('id', function($subQuery) use ($user) {
                      $subQuery->select('project_id')
                               ->from('project_user')
                               ->where('user_id', $user->id);
                  });
            });
        }
        
        // Filter by status if provided
        if ($request->filled('status') && $request->status !== 'all') {
            $baseQuery->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $baseQuery->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Get project IDs
        $projectIds = $baseQuery->pluck('id')->toArray();
        
        // Load projects with relationships
        $projects = Project::with(['creator', 'teamMembers'])
            ->whereIn('id', $projectIds)
            ->orderByRaw("CASE 
                WHEN status = 'completed' THEN 2
                WHEN status = 'cancelled' THEN 2
                ELSE 1
            END ASC")
            ->orderByRaw("CASE 
                WHEN end_date IS NULL THEN 1
                ELSE 0
            END ASC")
            ->orderBy('end_date', 'ASC')
            ->latest('updated_at')
            ->get();

        // Format projects for response
        $formattedProjects = $projects->map(function ($project) {
            return [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
                'priority' => $project->priority,
                'progress' => $project->progress,
                'budget' => $project->budget,
                'start_date' => $project->start_date->format('M d, Y'),
                'end_date' => $project->end_date ? $project->end_date->format('M d, Y') : null,
                'creator' => $project->creator ? [
                    'id' => $project->creator->id,
                    'name' => $project->creator->name,
                ] : null,
                'team_members' => $project->teamMembers->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name,
                    ];
                })->toArray(),
                'updated_at' => $project->updated_at->timestamp,
            ];
        });

        return response()->json([
            'success' => true,
            'projects' => $formattedProjects,
            'timestamp' => now()->timestamp,
        ]);
    }

    /**
     * Get project statistics for real-time updates
     */
    public function getStats(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $stats = [
                'total' => \Illuminate\Support\Facades\DB::table('projects')->count(),
                'active' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('status', ['active', 'inprogress'])->count(),
                'pending' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('status', ['review_pending', 'awaiting_input', 'paused'])->count(),
                'completed' => \Illuminate\Support\Facades\DB::table('projects')->where('status', 'completed')->count(),
                'overdue' => \Illuminate\Support\Facades\DB::table('projects')->where('end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->count(),
            ];
        } else {
            // Stats for regular users (only their projects)
            $userProjectIds = \Illuminate\Support\Facades\DB::table('projects')
                ->where('created_by', $user->id)
                ->orWhereIn('id', function($q) use ($user) {
                    $q->select('project_id')
                      ->from('project_user')
                      ->where('user_id', $user->id);
                })
                ->pluck('id');

            $stats = [
                'total' => $userProjectIds->count(),
                'active' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('id', $userProjectIds)->whereIn('status', ['active', 'inprogress'])->count(),
                'pending' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('id', $userProjectIds)->whereIn('status', ['review_pending', 'awaiting_input', 'paused'])->count(),
                'completed' => \Illuminate\Support\Facades\DB::table('projects')->whereIn('id', $userProjectIds)->where('status', 'completed')->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'timestamp' => now()->timestamp,
        ]);
    }
}
