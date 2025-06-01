<?php

namespace App\Http\Middleware;

use App\Services\RealTimeNotificationService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    protected $realTimeNotificationService;

    public function __construct(RealTimeNotificationService $realTimeNotificationService)
    {
        $this->realTimeNotificationService = $realTimeNotificationService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Track user activity if authenticated
        if (Auth::check()) {
            $this->realTimeNotificationService->markUserOnline(Auth::id());
        }

        return $response;
    }
} 