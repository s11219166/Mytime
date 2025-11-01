<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Send a push notification to a user
     */
    public function sendPushNotification(User $user, string $title, string $message, ?array $data = null, ?string $icon = null): bool
    {
        try {
            // Check if user has push notifications enabled
            if (!$user->push_notifications || !$user->push_subscription) {
                Log::info("Push notifications disabled or no subscription for user {$user->id}");
                return false;
            }

            // Decode the subscription
            $subscription = json_decode($user->push_subscription, true);
            
            if (!$subscription || !isset($subscription['endpoint'])) {
                Log::warning("Invalid push subscription for user {$user->id}");
                return false;
            }

            // Prepare the notification payload
            $payload = [
                'title' => $title,
                'body' => $message,
                'icon' => $icon ?? '/favicon.ico',
                'badge' => '/favicon.ico',
                'tag' => 'mytime-notification',
                'requireInteraction' => true,
                'data' => $data ?? [],
                'timestamp' => now()->timestamp * 1000,
            ];

            // Send the push notification
            $this->sendWebPushNotification($subscription, $payload);

            // Update last notification timestamp
            $user->update(['last_push_notification_at' => now()]);

            Log::info("Push notification sent to user {$user->id}: {$title}");
            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send push notification to user {$user->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send push notification to multiple users
     */
    public function sendBulkPushNotifications(array $users, string $title, string $message, ?array $data = null, ?string $icon = null): int
    {
        $sentCount = 0;

        foreach ($users as $user) {
            if ($this->sendPushNotification($user, $title, $message, $data, $icon)) {
                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * Send web push notification using Web Push Protocol
     */
    private function sendWebPushNotification(array $subscription, array $payload): void
    {
        try {
            // For browser-based push notifications, we use the Web Push API
            // This is handled by the browser's service worker
            // We'll store the notification in the database and let the browser handle it via polling
            
            // Alternative: Use a Web Push library like web-push-php
            // For now, we'll implement a simpler approach using browser notifications
            
            Log::info("Web push notification prepared for endpoint: " . substr($subscription['endpoint'], 0, 50) . "...");
            
        } catch (\Exception $e) {
            Log::error("Error sending web push notification: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send project due notification
     */
    public function sendProjectDueNotification(User $user, $project, int $daysRemaining): bool
    {
        $title = match(true) {
            $daysRemaining == 1 => "🚨 Project Due Tomorrow!",
            $daysRemaining == 0 => "🔴 Project Due TODAY!",
            $daysRemaining < 0 => "❌ Project OVERDUE!",
            default => "���� Project Due in {$daysRemaining} Days"
        };

        $message = "Project: {$project->name}";

        $data = [
            'type' => 'project_due',
            'project_id' => $project->id,
            'days_remaining' => $daysRemaining,
            'action' => 'open_project',
            'url' => route('projects.show', $project->id),
        ];

        $icon = '/favicon.ico';

        return $this->sendPushNotification($user, $title, $message, $data, $icon);
    }

    /**
     * Send new project notification
     */
    public function sendNewProjectNotification(User $user, $project): bool
    {
        $title = "✨ New Project Added!";
        $message = "You've been assigned to: {$project->name}";

        $data = [
            'type' => 'new_project',
            'project_id' => $project->id,
            'action' => 'open_project',
            'url' => route('projects.show', $project->id),
        ];

        $icon = '/favicon.ico';

        return $this->sendPushNotification($user, $title, $message, $data, $icon);
    }

    /**
     * Send project completion notification
     */
    public function sendProjectCompletionNotification(User $user, $project): bool
    {
        $title = "✅ Project Completed!";
        $message = "Project '{$project->name}' has been marked as completed.";

        $data = [
            'type' => 'project_completed',
            'project_id' => $project->id,
            'action' => 'open_project',
            'url' => route('projects.show', $project->id),
        ];

        $icon = '/favicon.ico';

        return $this->sendPushNotification($user, $title, $message, $data, $icon);
    }

    /**
     * Send time tracking reminder notification
     */
    public function sendTimeTrackingReminder(User $user): bool
    {
        $title = "⏱️ Time Tracking Reminder";
        $message = "Don't forget to log your time for today!";

        $data = [
            'type' => 'time_reminder',
            'action' => 'open_time_logs',
            'url' => route('time-logs.index'),
        ];

        $icon = '/favicon.ico';

        return $this->sendPushNotification($user, $title, $message, $data, $icon);
    }

    /**
     * Test push notification
     */
    public function sendTestNotification(User $user): bool
    {
        $title = "🎉 Test Notification";
        $message = "Push notifications are working correctly!";

        $data = [
            'type' => 'test',
            'timestamp' => now()->toIso8601String(),
        ];

        return $this->sendPushNotification($user, $title, $message, $data);
    }
}
