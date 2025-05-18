<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckActiveAccount
{
    public function handle(Request $request, Closure $next)
    {

        $user = auth('api')->user();
        if (auth('api')->check()  && !$user->is_active ) {

            return response()->json(['message' => __('messages.account_not_active'),'is_active' => false], 403);
        }

        return $next($request);
    }
}