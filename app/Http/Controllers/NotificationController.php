<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications
     */
    public function index()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->paginate(10);
        $unreadCount = $user->notifications()->where('is_read', false)->count();

        return view('notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark all notifications as read
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        $user->notifications()->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark multiple notifications as read
     */
    public function markMultipleAsRead(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer'
        ]);

        $user = Auth::user();
        $ids = $request->input('ids');

        $notifications = $user->notifications()->whereIn('id', $ids)->get();

        if ($notifications->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No valid notifications found'
            ], 404);
        }

        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifications marked as read',
            'count' => $notifications->count()
        ]);
    }

    /**
     * Remove the specified notification
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully'
        ]);
    }

    /**
     * Clear all read notifications
     */
    public function clearRead(Request $request)
    {
        $user = Auth::user();
        $user->notifications()->where('is_read', true)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Read notifications cleared'
        ]);
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        $count = $user->notifications()->where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Get latest notifications for real-time updates
     */
    public function getLatest()
    {
        $user = Auth::user();
        $notifications = $user->notifications()
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'is_read' => $notification->is_read,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'project_id' => $notification->project_id
                ];
            });

        $unreadCount = $user->notifications()->where('is_read', false)->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
}
