<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendProjectNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:send-notification {project_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send project creation notifications asynchronously';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $projectId = $this->argument('project_id');
        
        $project = Project::find($projectId);
        if (!$project) {
            $this->error("Project with ID {$projectId} not found!");
            return Command::FAILURE;
        }

        try {
            // Send notification when project is created
            $notificationService = App::make(NotificationService::class);
            $pushNotificationService = App::make(PushNotificationService::class);

            // Notify project creator
            if ($project->creator) {
                $notificationService->notifyProjectAssignment($project, $project->creator);

                // Send push notification
                if (isset($project->creator->push_notifications) && $project->creator->push_notifications) {
                    try {
                        $pushNotificationService->sendNewProjectNotification($project->creator, $project);
                        Log::info('Push notification sent for new project to creator: ' . $project->creator->id);
                    } catch (\Exception $e) {
                        Log::error('Failed to send new project push notification: ' . $e->getMessage());
                    }
                }

                // Send email notification
                if (isset($project->creator->email_notifications) && $project->creator->email_notifications) {
                    try {
                        Mail::to($project->creator->email)
                            ->send(new ProjectDueReminderMail(
                                $project,
                                $project->creator,
                                0, // days remaining (0 for new project)
                                'new_project', // urgency level
                                'creation' // notification time
                            ));
                    } catch (\Exception $e) {
                        Log::error('Failed to send new project email: ' . $e->getMessage());
                    }
                }
            }

            // Notify all team members
            foreach ($project->teamMembers as $member) {
                if (!isset($member->project_updates) || $member->project_updates) {
                    $notificationService->notifyProjectAssignment($project, $member);

                    // Send push notification
                    if (isset($member->push_notifications) && $member->push_notifications) {
                        try {
                            $pushNotificationService->sendNewProjectNotification($member, $project);
                            Log::info('Push notification sent for new project to team member: ' . $member->id);
                        } catch (\Exception $e) {
                            Log::error('Failed to send new project push notification to team member: ' . $e->getMessage());
                        }
                    }

                    // Send email notification
                    if (isset($member->email_notifications) && $member->email_notifications) {
                        try {
                            Mail::to($member->email)
                                ->send(new ProjectDueReminderMail(
                                    $project,
                                    $member,
                                    0, // days remaining (0 for new project)
                                    'new_project', // urgency level
                                    'creation' // notification time
                                ));
                        } catch (\Exception $e) {
                            Log::error('Failed to send new project email to team member: ' . $e->getMessage());
                        }
                    }
                }
            }

            $this->info("Project notifications sent successfully for project ID: {$projectId}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to send project notifications: " . $e->getMessage());
            Log::error("Failed to send project notifications for project ID {$projectId}: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}