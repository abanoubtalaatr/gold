<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Response;
use App\Models\Account;
use Inertia\Inertia;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {

        $user = $request->user();
        $notifications = $user->notifications()->paginate(10);
        
        $user->unreadNotifications->markAsRead();

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
}