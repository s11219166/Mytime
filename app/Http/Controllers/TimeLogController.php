<?php

namespace App\Http\Controllers;

use App\Models\TimeEntry;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TimeLogController extends Controller
{
    /**
     * Display time logs page
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Get filter parameters
        $period = $request->get('period', '30'); // days
        $projectFilter = $request->get('project');
        $statusFilter = $request->get('status');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Build query
        $query = TimeEntry::where('user_id', $user->id)
            ->with('project');

        // Apply filters
        if ($startDate && $endDate) {
            $query->whereBetween('start_time', [$startDate, $endDate]);
        } elseif ($period) {
            $query->where('start_time', '>=', now()->subDays($period));
        }

        if ($projectFilter) {
            $query->where('project_id', $projectFilter);
        }

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        // Get paginated results
        $timeLogs = $query->orderByDesc('start_time')->paginate(20);

        // Get summary statistics
        $totalMinutes = TimeEntry::where('user_id', $user->id)
            ->where('start_time', '>=', now()->subDays($period))
            ->sum('duration_minutes');
        $totalHours = round($totalMinutes / 60, 1);

        $todayMinutes = TimeEntry::where('user_id', $user->id)
            ->whereDate('start_time', today())
            ->sum('duration_minutes');
        $todayHours = round($todayMinutes / 60, 1);

        $weekMinutes = TimeEntry::where('user_id', $user->id)
            ->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum('duration_minutes');
        $weekHours = round($weekMinutes / 60, 1);

        $activeEntries = TimeEntry::where('user_id', $user->id)
            ->where('status', 'running')
            ->count();

        // Get user's projects for filter dropdown
        $projects = $user->getAllProjects()->get();

        // Get time logs by project
        $timeByProject = TimeEntry::where('user_id', $user->id)
            ->where('start_time', '>=', now()->subDays($period))
            ->join('projects', 'time_entries.project_id', '=', 'projects.id')
            ->select('projects.name', 'projects.id', DB::raw('SUM(time_entries.duration_minutes) as total_minutes'))
            ->groupBy('projects.id', 'projects.name')
            ->orderByDesc('total_minutes')
            ->limit(10)
            ->get();

        // Recent activity (last 7 days)
        $recentActivity = TimeEntry::where('user_id', $user->id)
            ->where('start_time', '>=', now()->subDays(7))
            ->selectRaw('DATE(start_time) as date, SUM(duration_minutes) as total_minutes, COUNT(*) as entry_count')
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();

        return view('time-logs.index', compact(
            'timeLogs',
            'totalHours',
            'todayHours',
            'weekHours',
            'activeEntries',
            'projects',
            'timeByProject',
            'recentActivity',
            'period',
            'projectFilter',
            'statusFilter'
        ));
    }

    /**
     * Start a new time entry
     */
    public function start(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_description' => 'required|string|max:255',
        ]);

        // Stop any running entries first
        TimeEntry::where('user_id', auth()->id())
            ->where('status', 'running')
            ->update([
                'status' => 'paused',
                'end_time' => now()
            ]);

        $timeEntry = TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['project_id'],
            'task_description' => $validated['task_description'],
            'start_time' => now(),
            'status' => 'running',
        ]);

        return redirect()->back()->with('success', 'Time tracking started successfully!');
    }

    /**
     * Stop a running time entry
     */
    public function stop($id)
    {
        $timeEntry = TimeEntry::where('user_id', auth()->id())->findOrFail($id);

        $timeEntry->update([
            'end_time' => now(),
            'duration_minutes' => now()->diffInMinutes($timeEntry->start_time),
            'status' => 'completed',
        ]);

        return redirect()->back()->with('success', 'Time tracking stopped successfully!');
    }

    /**
     * Update time entry
     */
    public function update(Request $request, $id)
    {
        $timeEntry = TimeEntry::where('user_id', auth()->id())->findOrFail($id);

        $validated = $request->validate([
            'task_description' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $timeEntry->update($validated);

        return redirect()->back()->with('success', 'Time log updated successfully!');
    }

    /**
     * Delete time entry
     */
    public function destroy($id)
    {
        $timeEntry = TimeEntry::where('user_id', auth()->id())->findOrFail($id);
        $timeEntry->delete();

        return redirect()->back()->with('success', 'Time log deleted successfully!');
    }

    /**
     * Manually add time entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'task_description' => 'required|string|max:255',
            'start_time' => 'required|date',
            'duration_minutes' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $startTime = Carbon::parse($validated['start_time']);
        $endTime = $startTime->copy()->addMinutes($validated['duration_minutes']);

        TimeEntry::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['project_id'],
            'task_description' => $validated['task_description'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration_minutes' => $validated['duration_minutes'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'completed',
        ]);

        return redirect()->back()->with('success', 'Time log added successfully!');
    }
}
