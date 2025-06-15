<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\NotificationResource;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all notifications for the authenticated user.
     */
    public function index()
    {
        // Get paginated notifications
        $notifications = Auth::user()->notifications()->paginate(10);

        // Group notifications by day
        $groupedNotifications = $notifications->getCollection()->groupBy(function ($notification) {
            return $notification->created_at->translatedFormat('d-F-Y'); // Group by date (Y-m-d format)
        });

        // Replace the original collection with the grouped data
        $groupedData = $groupedNotifications->map(function ($group, $date) {
            return [
                'date' => $date,
                'notifications' => NotificationResource::collection($group),
            ];
        })->values();

        // Add the grouped data back to the paginated instance
        $notifications->setCollection($groupedData);


        return $this->successResponse( $notifications, __('mobile.Notifications fetched successfully'));
    }

    /**
     * Get all unread notifications for the authenticated user, grouped by day.
     */
    public function unread()
    {
        // Get paginated unread notifications
        $unreadNotifications = Auth::user()->unreadNotifications()->paginate(10);

        // Group unread notifications by day
        $groupedNotifications = $unreadNotifications->getCollection()->groupBy(function ($notification) {
            return $notification->created_at->translatedFormat('d-F-Y'); // Group by date (Y-m-d format)
        });

        // Replace the original collection with the grouped data
        $groupedData = $groupedNotifications->map(function ($group, $date) {
            return [
                'date' => $date,
                'notifications' => NotificationResource::collection($group),
            ];
        })->values();

        // Add the grouped data back to the paginated instance
        $unreadNotifications->setCollection($groupedData);

        return $this->successResponse( $unreadNotifications, __('mobile.Unread notifications fetched successfully'));
    
    }



    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        $notification =  Auth::user()->notifications()->find($id);

        if (!$notification) {
            return $this->returnError('404', __('mobile.Notification not found.'));
        }

        $notification->markAsRead();

        return $this->successResponse(null, __('mobile.Notification marked as read.'));
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return $this->successResponse(null, __('mobile.All notifications marked as read.'));
    }

    /**
     * Delete a specific notification.
     */
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->find($id);

        if (!$notification) {
            return $this->returnError('404', __('mobile.Notification not found.'));
        }

        $notification->delete();

        return $this->successResponse(null,__('mobile.Notification deleted.'));
    }

    /**
     * Delete all notifications.
     */
    public function deleteAllNotifications()
    {
        Auth::user()->notifications()->delete();

        return $this->successResponse(null, __('mobile.All notifications deleted.'));
    }

    public function notificationCounts()
    {
        return $this->successResponse(Auth::user()->unreadNotifications()->count(), __('mobile.Notification counts fetched successfully'));
    }

    /**
     * Poll for new notifications (for web interface real-time updates)
     */
    public function poll()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $lastCheck = request()->get('last_check');

        // If no last_check provided, use 5 minutes ago to catch recent notifications
        if (!$lastCheck) {
            $since = now()->subMinutes(5);
        } else {
            try {
                $since = \Carbon\Carbon::parse($lastCheck);
                // Ensure we don't go too far back to avoid performance issues
                $maxLookback = now()->subHours(1);
                if ($since->lt($maxLookback)) {
                    $since = $maxLookback;
                }
            } catch (\Exception $e) {
                // If invalid date, use 5 minutes ago as fallback
                $since = now()->subMinutes(5);
            }
        }

        // Get only unread notifications created AFTER the last check time
        $newNotifications = $user->notifications()
            ->where('created_at', '>', $since)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limit to prevent overwhelming the frontend
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at->toISOString(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            });

        // Get total unread count
        $totalUnread = $user->unreadNotifications()->count();

        return response()->json([
            'notifications' => $newNotifications,
            'count' => $newNotifications->count(),
            'total_unread' => $totalUnread,
            'timestamp' => now()->toISOString(),
            'since' => $since->toISOString(),
            'next_check_from' => $newNotifications->count() > 0 ?
                $newNotifications->first()['created_at'] :
                now()->toISOString(),
            'debug' => [
                'user_id' => $user->id,
                'last_check_provided' => $lastCheck,
                'since_parsed' => $since->toISOString(),
                'notifications_found' => $newNotifications->count(),
                'server_time' => now()->toISOString(),
            ]
        ]);
    }
}
