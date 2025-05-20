<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\NotificationResource;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $query = Auth::user()->notifications();
        
        // Filter by read/unread if specified
        if ($request->has('read')) {
            $isRead = filter_var($request->read, FILTER_VALIDATE_BOOLEAN);
            $query = $isRead ? $query->whereNotNull('read_at') : $query->whereNull('read_at');
        }

        // Sort by date
        $query = $query->orderBy('created_at', 'desc');

        // Paginate results
        $notifications = $query->paginate($request->per_page ?? 15);

        return $this->successResponse(
            NotificationResource::collection($notifications),
            __('mobile.fetched_successfully')
        );
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();

        return $this->successResponse([
            'count' => $count
        ], __('mobile.count_fetched_successfully'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return $this->successResponse(
            new NotificationResource($notification),
            __('mobile.marked_as_read')
        );
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return $this->successResponse(
            null,
            __('mobile.all_marked_as_read')
        );
    }

    /**
     * Delete a notification
     */
    public function destroy(string $id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return $this->successResponse(
            null,
            __('mobile.deleted_successfully')
        );
    }

    /**
     * Delete all notifications
     */
    public function destroyAll()
    {
        Auth::user()->notifications()->delete();

        return $this->successResponse(
            null,
            __('mobile.all_deleted_successfully')
        );
    }
} 