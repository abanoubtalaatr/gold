<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Account;
use Inertia\Inertia;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $notifications = $user->notifications()->paginate(10);
        
        // Don't automatically mark all as read - this was the issue!
        // $user->unreadNotifications->markAsRead();

        // $formattedNotifications = $notifications->map(function ($notification) {
        //     return [
        //         'id' => $notification->id,
        //         'type' => class_basename($notification->type),
        //         'data' => $notification->data,
        //         'read_at' => $notification->read_at,
        //         'created_at' => $notification->created_at->diffForHumans(),
        //     ];
        // });

        return inertia(
            'Notification/index',
            [
                'notifications' => $notifications,
                //     'pagination' => [
                //     'current_page' => $notifications->currentPage(),
                //     'last_page' => $notifications->lastPage(),
                //     'per_page' => $notifications->perPage(),
                //     'total' => $notifications->total(),
                // ],
            ]
        );
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead(Request $request, $notificationId): JsonResponse
    {
        try {
            $user = $request->user();
            $notification = $user->notifications()->find($notificationId);

            if (!$notification) {
                return response()->json([
                    'error' => 'Notification not found',
                    'success' => false
                ], 404);
            }

            if ($notification->read_at) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Notification already read',
                    'was_already_read' => true
                ]);
            }

            $notification->markAsRead();

            return response()->json([
                'success' => true, 
                'message' => __('mobile.Notification marked as read'),
                'notification_id' => $notificationId,
                'read_at' => $notification->read_at
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read', [
                'notification_id' => $notificationId,
                'user_id' => $request->user()->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Failed to mark notification as read',
                'success' => false
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get vendor notification count (for real-time updates)
     */
    public function getVendorNotificationCount(Request $request): JsonResponse
    {
        $count = $request->user()->unreadNotifications()->count();

        return response()->json(['count' => min($count, 99)]); // Cap at 99 like in frontend
    }
}