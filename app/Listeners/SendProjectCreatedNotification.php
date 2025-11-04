<?php

namespace App\Listeners;

use App\Events\ProjectCreated;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            $projectName = $project->name;
            $projectId = $project->id;

            // In-app notification to creator
            if ($project->creator) {
                $this->notificationService->createNotification(
                    $project->creator,
                    'new_project',
                    '✨ New Project Created',
                    "You have created the project '{$projectName}'.",
                    $project,
                    [
                        'action' => 'project_created',
                        'project_id' => $projectId,
                    ]
                );
                Log::info("In-app notification sent to creator for project: {$projectName}");
            }

            // In-app notification to all team members
            foreach ($project->teamMembers as $member) {
                $this->notificationService->createNotification(
                    $member,
                    'project_assigned',
                    '👥 New Project Assignment',
                    "You have been assigned to the project '{$projectName}'.",
                    $project,
                    [
                        'action' => 'project_assigned',
                        'project_id' => $projectId,
                    ]
                );
                Log::info("In-app notification sent to team member {$member->id} for project: {$projectName}");
            }

            // Email all admins about the new project
            $this->emailAllAdmins($project);

        } catch (\Exception $e) {
            Log::error('Error in SendProjectCreatedNotification: ' . $e->getMessage());
        }
    }

    /**
     * Send email to all admin users about the new project.
     */
    protected function emailAllAdmins($project): void
    {
        try {
            // Fetch all admin users
            $admins = User::where('role', 'admin')->get(['id', 'name', 'email']);

            if ($admins->isEmpty()) {
                Log::warning('No admin users found to email for project: ' . $project->name);
                return;
            }

            // Send email to each admin
            foreach ($admins as $admin) {
                $this->sendAdminEmail($admin, $project);
            }

            Log::info('Project created emails sent to ' . $admins->count() . ' admin(s) for project: ' . $project->name);
        } catch (\Exception $e) {
            Log::error('Error emailing admins for project created: ' . $e->getMessage());
        }
    }

    /**
     * Send email to a single admin about the new project.
     */
    protected function sendAdminEmail($admin, $project): void
    {
        try {
            $projectName = $project->name;
            $body = "A new project has been created: {$projectName}\n\n";
            $body .= "Project Details:\n";
            $body .= "================\n";
            
            if ($project) {
                $body .= "Name: {$project->name}\n";
                if (!empty($project->description)) {
                    $body .= "Description: " . substr($project->description, 0, 100) . (strlen($project->description) > 100 ? '...' : '') . "\n";
                }
                if (!empty($project->priority)) {
                    $body .= "Priority: " . ucfirst($project->priority) . "\n";
                }
                if (!empty($project->status)) {
                    $body .= "Status: " . ucfirst(str_replace('_', ' ', $project->status)) . "\n";
                }
                if (!empty($project->start_date)) {
                    $body .= "Start Date: " . optional($project->start_date)->format('M d, Y') . "\n";
                }
                if (!empty($project->end_date)) {
                    $body .= "Due Date: " . optional($project->end_date)->format('M d, Y') . "\n";
                }
                if (!empty($project->budget)) {
                    $body .= "Budget: $" . number_format($project->budget, 2) . "\n";
                }
            }

            $body .= "\n---\n";
            $body .= "This is an automated notification from MyTime.\n";

            Mail::raw(
                $body,
                function ($message) use ($admin, $projectName) {
                    $message->to($admin->email)
                        ->subject('New Project Created: ' . $projectName);
                }
            );
            Log::info("Project created email sent to admin {$admin->email} ({$admin->name}) for project: {$projectName}");
        } catch (\Exception $e) {
            Log::error('Failed to send project created email to admin ' . $admin->email . ': ' . $e->getMessage());
        }
    }
}
