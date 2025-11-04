<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationTriggerController extends Controller
{
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * Manually trigger due date check (for Render deployment)
     * This replaces the cron job since Render doesn't support cron
     */
    public function checkDueDates(Request $request)
    {
        // Basic auth check - only allow admin or authenticated users
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            Log::info('Manual due date check triggered by user: ' . auth()->id());

            $this->notificationService->checkProjectDueDates();

            $notificationCount = \App\Models\Notification::where('created_at', '>=', now()->subMinutes(5))->count();

            return response()->json([
                'success' => true,
                'message' => 'Due date check completed',
                'notifications_created' => $notificationCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error in manual due date check: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create test notifications (for debugging)
     */
    public function createTestNotifications(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        try {
            // Create a test project if none exists
            $project = Project::first();
            if (!$project) {
                $project = Project::create([
                    'name' => 'Test Project',
                    'description' => 'Test project for notifications',
                    'status' => 'active',
                    'priority' => 'medium',
                    'start_date' => now(),
                    'end_date' => now()->addDays(7),
                    'created_by' => $user->id
                ]);
            }

            // Create test notifications
            $this->notificationService->createNotification(
                $user,
                'test',
                '🧪 Test Notification',
                'This is a test notification to verify the system is working.',
                $project
            );

            $this->notificationService->createNotification(
                $user,
                'project_reminder',
                '📅 Test Reminder',
                'This is a test reminder notification.',
                $project
            );

            return response()->json([
                'success' => true,
                'message' => 'Test notifications created',
                'project_id' => $project->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating test notifications: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notification statistics
     */
    public function getStats(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $user = auth()->user();

            $stats = [
                'total_notifications' => $user->notifications()->count(),
                'unread_notifications' => $user->notifications()->where('is_read', false)->count(),
                'today_notifications' => $user->notifications()->whereDate('created_at', today())->count(),
                'projects_count' => Project::count(),
                'upcoming_projects' => Project::whereNotNull('end_date')
                    ->where('end_date', '>', now())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->count(),
                'overdue_projects' => Project::whereNotNull('end_date')
                    ->where('end_date', '<', now())
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trigger notifications for a specific project
     */
    public function triggerProjectNotifications(Request $request, Project $project)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $user = auth()->user();

            // Check if user has access to this project
            $hasAccess = $project->created_by === $user->id ||
                        $project->teamMembers->contains($user->id);

            if (!$hasAccess && !$user->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            // Create test notifications for this project
            $this->notificationService->createNotification(
                $user,
                'project_reminder',
                '📅 Manual Reminder',
                "Manual reminder for project: {$project->name}",
                $project
            );

            if ($project->end_date) {
                $daysRemaining = now()->diffInDays($project->end_date, false);
                $this->notificationService->sendProjectDueReminder($project, $user, $daysRemaining);
            }

            return response()->json([
                'success' => true,
                'message' => 'Notifications triggered for project',
                'project' => $project->name
            ]);

        } catch (\Exception $e) {
            Log::error('Error triggering project notifications: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
