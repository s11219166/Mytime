<?php

namespace App\Listeners;

use App\Events\ProjectCompleted;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

// Temporarily disable queuing for Render deployment
// class SendProjectCompletedNotification implements ShouldQueue
class SendProjectCompletedNotification
{
    // use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ProjectCompleted $event): void
    {
        try {
            $project = $event->project;

            // Notify project creator
            if ($project->creator) {
                $this->notificationService->createNotification(
                    $project->creator,
                    'project_completed',
                    '✅ Project Completed',
                    "The project '{$project->name}' has been marked as completed.",
                    $project,
                    [
                        'action' => 'project_completed',
                        'project_id' => $project->id,
                    ]
                );
                Log::info("Completion notification sent to creator for project: {$project->name}");
            }

            // Notify all team members
            foreach ($project->teamMembers as $member) {
                $this->notificationService->createNotification(
                    $member,
                    'project_completed',
                    '✅ Project Completed',
                    "The project '{$project->name}' has been marked as completed.",
                    $project,
                    [
                        'action' => 'project_completed',
                        'project_id' => $project->id,
                    ]
                );
                Log::info("Completion notification sent to team member {$member->id} for project: {$project->name}");
            }
        } catch (\Exception $e) {
            Log::error('Error in SendProjectCompletedNotification: ' . $e->getMessage());
        }
    }
}
