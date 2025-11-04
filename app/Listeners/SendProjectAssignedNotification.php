<?php

namespace App\Listeners;

use App\Events\ProjectAssigned;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendProjectAssignedNotification implements ShouldQueue
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
    public function handle(ProjectAssigned $event): void
    {
        try {
            $project = $event->project;
            $user = $event->user;

            $this->notificationService->createNotification(
                $user,
                'project_assigned',
                '👥 Project Assignment',
                "You have been assigned to the project '{$project->name}'.",
                $project,
                [
                    'action' => 'project_assigned',
                    'project_id' => $project->id,
                    'assigned_by' => $project->creator_id,
                ]
            );

            Log::info("Notification sent to user {$user->id} for project assignment: {$project->name}");
        } catch (\Exception $e) {
            Log::error('Error in SendProjectAssignedNotification: ' . $e->getMessage());
        }
    }
}
