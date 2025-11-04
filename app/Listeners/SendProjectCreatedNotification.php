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
     * Uses fallback to logging if SMTP connection fails (common on Render).
     */
    protected function sendAdminEmail($admin, $project): void
    {
        try {
            $projectName = $project->name;
            $body = $this->buildEmailBody($project);

            // Try to send email via configured mailer
            try {
                Mail::raw(
                    $body,
                    function ($message) use ($admin, $projectName) {
                        $message->to($admin->email)
                            ->subject('New Project Created: ' . $projectName);
                    }
                );
                Log::info("Project created email sent to admin {$admin->email} ({$admin->name}) for project: {$projectName}");
            } catch (\Exception $mailException) {
                // If SMTP fails (common on Render due to network restrictions),
                // log the email content for manual review or alternative delivery
                Log::warning("SMTP connection failed for admin {$admin->email}, logging email content instead: " . $mailException->getMessage());
                
                // Log the email content that would have been sent
                Log::info("PENDING_EMAIL_TO: {$admin->email}");
                Log::info("PENDING_EMAIL_SUBJECT: New Project Created: {$projectName}");
                Log::info("PENDING_EMAIL_BODY: " . $body);
                
                // Store in database for later delivery (optional enhancement)
                $this->logPendingEmail($admin, $projectName, $body);
            }
        } catch (\Exception $e) {
            Log::error('Error in sendAdminEmail for admin ' . $admin->email . ': ' . $e->getMessage());
        }
    }

    /**
     * Build the email body with project details.
     */
    protected function buildEmailBody($project): string
    {
        $body = "A new project has been created: {$project->name}\n\n";
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

        return $body;
    }

    /**
     * Log pending email for manual delivery or alternative service.
     * This is a fallback mechanism for environments with SMTP restrictions.
     */
    protected function logPendingEmail($admin, $projectName, $body): void
    {
        try {
            // Log as structured data for easy parsing
            Log::channel('single')->info('PENDING_EMAIL', [
                'to' => $admin->email,
                'admin_name' => $admin->name,
                'subject' => "New Project Created: {$projectName}",
                'body' => $body,
                'timestamp' => now()->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log pending email: ' . $e->getMessage());
        }
    }
}
