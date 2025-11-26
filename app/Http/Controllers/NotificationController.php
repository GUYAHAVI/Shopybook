<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get notifications for the current business
     */
    public function index(Request $request)
    {
        try {
            $business = Auth::user()->business;
            if (!$business) {
                return response()->json(['error' => 'No business found'], 404);
            }

            $limit = $request->get('limit', 10);
            $notifications = $this->notificationService->getRecentNotifications($business->id, $limit);
            $unreadCount = $this->notificationService->getUnreadCount($business->id);

            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Failed to load notifications',
                'notifications' => [],
                'unread_count' => 0
            ], 500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        try {
            $business = Auth::user()->business;
            if (!$business) {
                return response()->json(['count' => 0]);
            }

            $count = $this->notificationService->getUnreadCount($business->id);
            
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            \Log::error('Error getting unread notification count', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return 0 instead of error to prevent breaking the UI
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        try {
            $business = Auth::user()->business;
            if (!$business) {
                return response()->json(['error' => 'No business found'], 404);
            }

            $notification = $this->notificationService->markAsRead($id, $business->id);
            return response()->json(['success' => true, 'notification' => $notification]);
        } catch (\Exception $e) {
            \Log::error('Error marking notification as read', [
                'notification_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Notification not found'], 404);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $business = Auth::user()->business;
        if (!$business) {
            return response()->json(['error' => 'No business found'], 404);
        }

        $updatedCount = $this->notificationService->markAllAsRead($business->id);
        
        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount
        ]);
    }
}