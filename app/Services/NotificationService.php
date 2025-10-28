<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use App\Mail\ProjectDueReminderMail;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public function createNotification(User $user, string $type, string $title, string $message, ?Project $project = null, ?array $data = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'project_id' => $project?->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Send project due date reminder
     */
    public function sendProjectDueReminder(Project $project, User $user, int $daysRemaining, ?string $notificationTime = null): void
    {
        // Determine urgency level based on days remaining
        $urgencyLevel = 'normal';
        if ($daysRemaining == 1) {
            $urgencyLevel = 'high';
        } elseif ($daysRemaining == 0) {
            $urgencyLevel = 'critical';
        } elseif ($daysRemaining < 0) {
            $urgencyLevel = 'overdue';
        }

        // Create in-app notification with enhanced messaging
        if ($daysRemaining == 3 && $notificationTime) {
            $title = $notificationTime === 'morning'
                ? "📅 Morning Reminder: Project Due in 3 Days"
                : "🌙 Evening Reminder: Project Due in 3 Days";
            $message = "The project '{$project->name}' is due in 3 days. " .
                      ($notificationTime === 'morning'
                          ? "Start your day by reviewing the project progress."
                          : "End your day by ensuring tasks are on track.");
            $type = 'project_reminder';

        } elseif ($daysRemaining == 2) {
            $title = "⚠️ Moderate Alert: Project Due in 2 Days";
            $message = "The project '{$project->name}' is due in 2 days. Please prioritize remaining tasks.";
            $type = 'project_reminder';

        } elseif ($daysRemaining == 1) {
            $title = "🚨 HIGH ALERT: Project Due Tomorrow!";
            $message = "URGENT: The project '{$project->name}' is due tomorrow. Immediate attention required!";
            $type = 'project_due_soon';

        } elseif ($daysRemaining == 0) {
            $title = "🔴 CRITICAL: Project Due TODAY!";
            $message = "CRITICAL ALERT: The project '{$project->name}' is due TODAY! Complete all remaining tasks immediately.";
            $type = 'project_due';

        } elseif ($daysRemaining < 0) {
            $title = "❌ OVERDUE: Project Deadline Passed!";
            $message = "The project '{$project->name}' is overdue by " . abs($daysRemaining) . " " . \Illuminate\Support\Str::plural('day', abs($daysRemaining)) . "! Immediate action required.";
            $type = 'project_overdue';

        } else {
            // Fallback for other days
            $title = "Project Due in {$daysRemaining} " . \Illuminate\Support\Str::plural('day', $daysRemaining);
            $message = "The project '{$project->name}' is due in {$daysRemaining} " . \Illuminate\Support\Str::plural('day', $daysRemaining) . ".";
            $type = 'project_reminder';
        }

        $this->createNotification(
            $user,
            $type,
            $title,
            $message,
            $project,
            [
                'days_remaining' => $daysRemaining,
                'due_date' => $project->end_date->format('Y-m-d'),
                'urgency_level' => $urgencyLevel,
                'notification_time' => $notificationTime,
            ]
        );

        // Send email if user has email notifications enabled
        if ($user->email_notifications) {
            try {
                Mail::to($user->email)->send(new ProjectDueReminderMail($project, $user, $daysRemaining, $urgencyLevel, $notificationTime));
            } catch (\Exception $e) {
                \Log::error('Failed to send project due reminder email: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check and send reminders for upcoming project due dates
     * Sends notifications based on days remaining:
     * - 3 days before: 2 reminders per day (morning 9 AM & evening 6 PM)
     * - 2 days before: 1 moderate alert per day
     * - 1 day before: 1 high alert per day
     * - On due date: Critical alert
     * - Overdue: Daily alerts
     */
    public function checkProjectDueDates(): void
    {
        $projects = Project::whereNotNull('end_date')
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->get();

        \Log::info("Checking {$projects->count()} projects for due date notifications");

        $currentHour = now()->hour;
        $isMorningRun = $currentHour >= 8 && $currentHour < 12; // 8 AM - 12 PM
        $isEveningRun = $currentHour >= 17 && $currentHour < 21; // 5 PM - 9 PM

        foreach ($projects as $project) {
            // Calculate days remaining
            $daysRemainingFloat = now()->diffInDays($project->end_date, false);
            $daysRemaining = (int) floor($daysRemainingFloat);

            $shouldNotify = false;
            $notificationTime = null;

            // Determine if notification should be sent based on days remaining
            if ($daysRemaining == 3) {
                // 3 days before: Send 2 reminders (morning & evening)
                $shouldNotify = $isMorningRun || $isEveningRun;
                $notificationTime = $isMorningRun ? 'morning' : 'evening';
                \Log::info("Project '{$project->name}': 3 days remaining - {$notificationTime} notification");

            } elseif ($daysRemaining == 2) {
                // 2 days before: Send 1 moderate alert (morning only)
                $shouldNotify = $isMorningRun;
                $notificationTime = 'morning';
                \Log::info("Project '{$project->name}': 2 days remaining - moderate alert");

            } elseif ($daysRemaining == 1) {
                // 1 day before: Send 1 high alert (morning only)
                $shouldNotify = $isMorningRun;
                $notificationTime = 'morning';
                \Log::info("Project '{$project->name}': 1 day remaining - HIGH ALERT");

            } elseif ($daysRemaining == 0) {
                // Due today: Send critical alert (morning only)
                $shouldNotify = $isMorningRun;
                $notificationTime = 'morning';
                \Log::info("Project '{$project->name}': DUE TODAY - CRITICAL ALERT");

            } elseif ($daysRemaining < 0) {
                // Overdue: Send daily alert (morning only)
                $shouldNotify = $isMorningRun;
                $notificationTime = 'morning';
                \Log::info("Project '{$project->name}': OVERDUE by " . abs($daysRemaining) . " days");
            }

            if ($shouldNotify) {
                // Notify project creator
                if ($project->creator) {
                    $this->sendProjectDueReminder($project, $project->creator, $daysRemaining, $notificationTime);
                }

                // Notify all team members
                foreach ($project->teamMembers as $member) {
                    if ($member->project_updates) {
                        $this->sendProjectDueReminder($project, $member, $daysRemaining, $notificationTime);
                    }
                }
            }
        }
    }

    /**
     * Notify user about project assignment
     */
    public function notifyProjectAssignment(Project $project, User $user): void
    {
        $this->createNotification(
            $user,
            'project_assigned',
            'New Project Assignment',
            "You have been assigned to the project '{$project->name}'.",
            $project
        );
    }

    /**
     * Notify about project completion
     */
    public function notifyProjectCompletion(Project $project): void
    {
        // Notify creator
        if ($project->creator) {
            $this->createNotification(
                $project->creator,
                'project_completed',
                'Project Completed',
                "The project '{$project->name}' has been marked as completed.",
                $project
            );
        }

        // Notify team members
        foreach ($project->teamMembers as $member) {
            if ($member->project_updates) {
                $this->createNotification(
                    $member,
                    'project_completed',
                    'Project Completed',
                    "The project '{$project->name}' has been marked as completed.",
                    $project
                );
            }
        }
    }

    /**
     * Send time tracking reminder
     */
    public function sendTimeTrackingReminder(User $user): void
    {
        if ($user->time_reminders) {
            $this->createNotification(
                $user,
                'time_reminder',
                'Time Tracking Reminder',
                'Don\'t forget to log your time for today!',
                null
            );
        }
    }
}
