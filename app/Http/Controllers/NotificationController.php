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
        $notifications = Auth::user()
            ->notifications()
            ->with('project')
            ->latest()
            ->paginate(20);

        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();

        return view('notifications', compact('notifications', 'unreadCount'));
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->notifications()->where('is_read', false)->count();
        
        return response()->json([
            'count' => $count
        ]);
    }

    /**
     * Get recent notifications for dropdown
     */
    public function getRecent()
    {
        $notifications = Auth::user()
            ->notifications()
            ->with('project')
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()->where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Delete all read notifications
     */
    public function clearRead()
    {
        Auth::user()->notifications()->where('is_read', true)->delete();

        return redirect()->back()->with('success', 'All read notifications cleared');
    }
}
