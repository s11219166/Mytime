<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\PushNotificationService;

class PushNotificationController extends Controller
{
    protected $pushNotificationService;

    public function __construct(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Subscribe user to push notifications
     */
    public function subscribe(Request $request)
    {
        try {
            $validated = $request->validate([
                'endpoint' => 'required|string',
                'keys' => 'required|array',
                'keys.p256dh' => 'required|string',
                'keys.auth' => 'required|string',
            ]);

            $user = Auth::user();
            
            // Store the subscription
            $subscription = [
                'endpoint' => $validated['endpoint'],
                'keys' => $validated['keys'],
            ];

            $user->update([
                'push_subscription' => json_encode($subscription),
                'push_notifications' => true,
            ]);

            Log::info("User {$user->id} subscribed to push notifications");

            return response()->json([
                'success' => true,
                'message' => 'Successfully subscribed to push notifications',
            ]);

        } catch (\Exception $e) {
            Log::error("Error subscribing to push notifications: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to subscribe to push notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Unsubscribe user from push notifications
     */
    public function unsubscribe(Request $request)
    {
        try {
            $user = Auth::user();
            
            $user->update([
                'push_subscription' => null,
                'push_notifications' => false,
            ]);

            Log::info("User {$user->id} unsubscribed from push notifications");

            return response()->json([
                'success' => true,
                'message' => 'Successfully unsubscribed from push notifications',
            ]);

        } catch (\Exception $e) {
            Log::error("Error unsubscribing from push notifications: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to unsubscribe from push notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle push notifications
     */
    public function toggle(Request $request)
    {
        try {
            $user = Auth::user();
            $enabled = $request->input('enabled', true);

            $user->update([
                'push_notifications' => $enabled,
            ]);

            Log::info("User {$user->id} toggled push notifications to: " . ($enabled ? 'enabled' : 'disabled'));

            return response()->json([
                'success' => true,
                'message' => 'Push notifications ' . ($enabled ? 'enabled' : 'disabled'),
                'enabled' => $enabled,
            ]);

        } catch (\Exception $e) {
            Log::error("Error toggling push notifications: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle push notifications',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send test push notification
     */
    public function test(Request $request)
    {
        try {
            $user = Auth::user();

            $result = $this->pushNotificationService->sendTestNotification($user);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Test push notification sent successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send test push notification. Make sure push notifications are enabled.',
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error("Error sending test push notification: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test push notification',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get push notification status
     */
    public function status(Request $request)
    {
        try {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'enabled' => $user->push_notifications,
                'subscribed' => !is_null($user->push_subscription),
                'last_notification' => $user->last_push_notification_at,
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting push notification status: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get push notification status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
