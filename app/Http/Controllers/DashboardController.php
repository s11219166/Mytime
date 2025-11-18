<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Format duration in minutes to human readable string
     */
    private function formatDuration(?int $minutes): string
    {
        if (!$minutes) return '0m';

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $remainingMinutes);
        }

        return sprintf('%dm', $remainingMinutes);
    }
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();

        // Get or create current session
        $currentSession = TimeEntry::where('user_id', $user->id)
            ->whereNull('end_time')
            ->first();

        if (!$currentSession) {
            $currentSession = TimeEntry::create([
                'user_id' => $user->id,
                'task_description' => 'Work Session',
                'start_time' => now(),
                'status' => 'running'
            ]);
        }

        // Calculate session time
        $sessionTime = $currentSession->start_time->diffForHumans(null, true);
        $sessionStart = $currentSession->start_time->format('h:i A');

        // Get today's statistics
        $todayStats = [
            'sessions' => TimeEntry::where('user_id', $user->id)
                ->whereDate('start_time', $today)
                ->count(),
            'total_time' => $this->formatDuration(TimeEntry::where('user_id', $user->id)
                ->whereDate('start_time', $today)
                ->sum('duration_minutes')),
            'avg_session' => $this->formatDuration(TimeEntry::where('user_id', $user->id)
                ->whereDate('start_time', $today)
                ->avg('duration_minutes'))
        ];

        // Get recent sessions
        $recentSessions = TimeEntry::where('user_id', $user->id)
            ->where('start_time', '!=', $currentSession->start_time)
            ->orderBy('start_time', 'desc')
            ->take(5)
            ->get()
            ->map(function ($session) {
                $session->duration = $this->formatDuration(
                    $session->end_time
                        ? $session->start_time->diffInMinutes($session->end_time)
                        : $session->start_time->diffInMinutes(now())
                );
                return $session;
            });

        // Prepare data based on user role
        if ($user->isAdmin()) {
            // Admin dashboard data
            $adminStats = [
                'total_users' => \App\Models\User::count(),
                'total_projects' => Project::count(),
                'active_projects' => Project::whereIn('status', ['active', 'inprogress'])->count(),
                'overdue_projects' => Project::where('end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->count(),
            ];

            return view('dashboard', compact(
                'sessionTime',
                'sessionStart',
                'todayStats',
                'currentSession',
                'recentSessions',
                'adminStats'
            ));
        } else {
            // User dashboard data
            $userProjectIds = DB::table('projects')
                ->where('created_by', $user->id)
                ->orWhereIn('id', function($q) use ($user) {
                    $q->select('project_id')
                      ->from('project_user')
                      ->where('user_id', $user->id);
                })
                ->pluck('id');

            $stats = [
                'active_projects' => Project::whereIn('id', $userProjectIds)
                    ->whereIn('status', ['active', 'inprogress'])
                    ->count(),
                'completed_projects' => Project::whereIn('id', $userProjectIds)
                    ->where('status', 'completed')
                    ->count(),
                'completion_rate' => $userProjectIds->count() > 0 
                    ? round((Project::whereIn('id', $userProjectIds)->where('status', 'completed')->count() / $userProjectIds->count()) * 100)
                    : 0,
                'total_hours' => $this->formatDuration(TimeEntry::where('user_id', $user->id)
                    ->whereMonth('start_time', now()->month)
                    ->sum('duration_minutes')),
                'unread_notifications' => $user->notifications()
                    ->where('is_read', false)
                    ->count(),
            ];

            return view('dashboard', compact(
                'sessionTime',
                'sessionStart',
                'todayStats',
                'currentSession',
                'recentSessions',
                'stats'
            ));
        }
    }

    public function addNote(Request $request)
    {
        $request->validate([
            'note' => 'required|string|max:1000'
        ]);

        $session = TimeEntry::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        if ($session) {
            $session->update([
                'notes' => $request->note
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function startBreak()
    {
        $currentSession = TimeEntry::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        if ($currentSession) {
            $currentSession->update([
                'end_time' => now(),
                'duration_minutes' => $currentSession->start_time->diffInMinutes(now())
            ]);
        }

        TimeEntry::create([
            'user_id' => Auth::id(),
            'task_description' => 'Break Time',
            'start_time' => now(),
            'status' => 'break'
        ]);

        return response()->json(['success' => true]);
    }

    public function endBreak()
    {
        $breakSession = TimeEntry::where('user_id', Auth::id())
            ->where('status', 'break')
            ->whereNull('end_time')
            ->first();

        if ($breakSession) {
            $breakSession->update([
                'end_time' => now(),
                'duration_minutes' => $breakSession->start_time->diffInMinutes(now())
            ]);
        }

        TimeEntry::create([
            'user_id' => Auth::id(),
            'task_description' => 'Work Session',
            'start_time' => now(),
            'status' => 'running'
        ]);

        return response()->json(['success' => true]);
    }

    public function endSession()
    {
        $currentSession = TimeEntry::where('user_id', Auth::id())
            ->whereNull('end_time')
            ->first();

        if ($currentSession) {
            $currentSession->update([
                'end_time' => now(),
                'duration_minutes' => $currentSession->start_time->diffInMinutes(now())
            ]);
        }

        return response()->json(['success' => true]);
    }
}
