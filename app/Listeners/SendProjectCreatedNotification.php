<?php

namespace App\Listeners;

use App\Events\ProjectCreated;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendProjectCreatedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(private NotificationService $notificationService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ProjectCreated $event): void
    {
        try {
            $project = $event->project;

            // Notify project creator
            if ($project->creator) {
                $this->notificationService->createNotification(
                    $project->creator,
                    'new_project',
                    '✨ New Project Created',
                    "You have created the project '{$project->name}'.",
                    $project,
                    [
                        'action' => 'project_created',
                        'project_id' => $project->id,
                    ]
                );
                Log::info("Notification sent to creator for project: {$project->name}");
            }

            // Notify all team members
            foreach ($project->teamMembers as $member) {
                $this->notificationService->createNotification(
                    $member,
                    'project_assigned',
                    '👥 New Project Assignment',
                    "You have been assigned to the project '{$project->name}'.",
                    $project,
                    [
                        'action' => 'project_assigned',
                        'project_id' => $project->id,
                    ]
                );
                Log::info("Notification sent to team member {$member->id} for project: {$project->name}");
            }
        } catch (\Exception $e) {
            Log::error('Error in SendProjectCreatedNotification: ' . $e->getMessage());
        }
    }
}
