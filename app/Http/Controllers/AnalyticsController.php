<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TimeEntry;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', '30'); // days
        $startDate = now()->subDays($period);

        // ===== PROJECT ANALYTICS =====
        $totalProjects = $user->createdProjects()->count() + $user->assignedProjects()->count();
        $activeProjects = $user->getAllProjects()->whereNotIn('status', ['completed', 'cancelled'])->count();
        $completedProjects = $user->getAllProjects()->where('status', 'completed')->count();
        $overdueProjects = $user->getAllProjects()
            ->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();

        // Project status distribution
        $projectsByStatus = $user->getAllProjects()
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Project priority distribution
        $projectsByPriority = $user->getAllProjects()
            ->select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->get()
            ->pluck('count', 'priority')
            ->toArray();

        // Projects completion over time (last 6 months)
        $projectCompletionTrend = $user->getAllProjects()
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as month, COUNT(*) as count')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Daily project completions (last 30 days)
        $dailyCompletions = $user->getAllProjects()
            ->where('status', 'completed')
            ->where('updated_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(updated_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top projects by progress
        $topProjects = $user->getAllProjects()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->orderByDesc('progress')
            ->limit(5)
            ->get();

        // ===== TIME TRACKING ANALYTICS =====
        $timeEntries = TimeEntry::where('user_id', $user->id)
            ->where('start_time', '>=', $startDate)
            ->get();

        $totalHours = $timeEntries->sum('duration_minutes') / 60;
        $avgDailyHours = $totalHours / max($period, 1);

        // Time by project
        $timeByProject = TimeEntry::where('user_id', $user->id)
            ->where('start_time', '>=', $startDate)
            ->join('projects', 'time_entries.project_id', '=', 'projects.id')
            ->select('projects.name', DB::raw('SUM(time_entries.duration_minutes) as total_minutes'))
            ->groupBy('projects.id', 'projects.name')
            ->orderByDesc('total_minutes')
            ->limit(10)
            ->get();

        // Daily time tracking (last 30 days)
        $dailyTime = TimeEntry::where('user_id', $user->id)
            ->where('start_time', '>=', now()->subDays(30))
            ->selectRaw('DATE(start_time) as date, SUM(duration_minutes) as total_minutes')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Weekly breakdown
        $weeklyTime = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->format('l');
            $minutes = TimeEntry::where('user_id', $user->id)
                ->whereDate('start_time', $date)
                ->sum('duration_minutes');
            $weeklyTime[] = [
                'day' => $dayName,
                'hours' => round($minutes / 60, 1)
            ];
        }

        // Hourly distribution (what time of day user works most)
        $hourlyDistribution = TimeEntry::where('user_id', $user->id)
            ->where('start_time', '>=', $startDate)
            ->selectRaw('HOUR(start_time) as hour, SUM(duration_minutes) as total_minutes')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // ===== NOTIFICATION ANALYTICS =====
        $totalNotifications = Notification::where('user_id', $user->id)->count();
        $unreadNotifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        $readRate = $totalNotifications > 0 ? round(($totalNotifications - $unreadNotifications) / $totalNotifications * 100, 1) : 0;

        // Notifications by type
        $notificationsByType = Notification::where('user_id', $user->id)
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();

        // ===== PRODUCTIVITY METRICS =====
        // Calculate productivity score (0-100)
        $productivityScore = $this->calculateProductivityScore($user, $period);

        // Streak calculation (consecutive days with time entries)
        $currentStreak = $this->calculateStreak($user);

        // Average completion rate
        $projectsWithProgress = $user->getAllProjects()->whereNotIn('status', ['cancelled'])->get();
        $avgProgress = $projectsWithProgress->avg('progress') ?? 0;

        // ===== BUDGET ANALYTICS =====
        $totalBudget = $user->getAllProjects()->sum('budget');
        $activeBudget = $user->getAllProjects()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->sum('budget');

        return view('analytics', compact(
            // Project metrics
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'overdueProjects',
            'projectsByStatus',
            'projectsByPriority',
            'projectCompletionTrend',
            'dailyCompletions',
            'topProjects',
            // Time tracking
            'totalHours',
            'avgDailyHours',
            'timeByProject',
            'dailyTime',
            'weeklyTime',
            'hourlyDistribution',
            // Notifications
            'totalNotifications',
            'unreadNotifications',
            'readRate',
            'notificationsByType',
            // Productivity
            'productivityScore',
            'currentStreak',
            'avgProgress',
            // Budget
            'totalBudget',
            'activeBudget',
            'period'
        ));
    }

    private function calculateProductivityScore($user, $period)
    {
        $score = 0;

        // Time logged (30 points)
        $timeScore = min(30, ($user->timeEntries()->where('start_time', '>=', now()->subDays($period))->sum('duration_minutes') / 60) / 8 * 30);

        // Project completion rate (30 points)
        $projects = $user->getAllProjects()->get();
        $completionScore = $projects->count() > 0 ? ($projects->where('status', 'completed')->count() / $projects->count()) * 30 : 0;

        // Average progress (25 points)
        $progressScore = ($projects->avg('progress') ?? 0) / 100 * 25;

        // On-time delivery (15 points)
        $onTimeScore = 15;
        $overdue = $user->getAllProjects()->whereNotNull('end_date')
            ->where('end_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->count();
        if ($overdue > 0) {
            $onTimeScore = max(0, 15 - ($overdue * 3));
        }

        $score = $timeScore + $completionScore + $progressScore + $onTimeScore;

        return round(min(100, $score), 1);
    }

    private function calculateStreak($user)
    {
        $streak = 0;
        $currentDate = now()->startOfDay();

        while (true) {
            $hasEntry = TimeEntry::where('user_id', $user->id)
                ->whereDate('start_time', $currentDate)
                ->exists();

            if (!$hasEntry) {
                break;
            }

            $streak++;
            $currentDate = $currentDate->subDay();

            // Limit to prevent infinite loop
            if ($streak > 365) {
                break;
            }
        }

        return $streak;
    }
}
