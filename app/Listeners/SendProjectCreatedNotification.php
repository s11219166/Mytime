<?php

namespace App\Listeners;

use App\Events\ProjectCreated;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Mail\Mailable;

class SendProjectCreatedNotification
{
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

            // In-app notification to creator
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

                // Email to creator if enabled
                if (!isset($project->creator->email_notifications) || $project->creator->email_notifications) {
                    $this->sendProjectCreatedEmail($project->creator->email, $project->name);
                }
            }

            // In-app notification to all team members + email if enabled
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

                if (!isset($member->email_notifications) || $member->email_notifications) {
                    $this->sendProjectCreatedEmail($member->email, $project->name);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error in SendProjectCreatedNotification: ' . $e->getMessage());
        }
    }

    /**
     * Lightweight inline email for project created event.
     */
    protected function sendProjectCreatedEmail(string $recipientEmail, string $projectName): void
    {
        try {
            Mail::raw(
                "A new project has been created: {$projectName}",
                function ($message) use ($recipientEmail, $projectName) {
                    $message->to($recipientEmail)
                        ->subject('New Project Created: ' . $projectName);
                }
            );
            Log::info("Project created email sent to {$recipientEmail} for project {$projectName}");
        } catch (\Exception $e) {
            Log::error('Failed to send project created email to ' . $recipientEmail . ': ' . $e->getMessage());
        }
    }
}
