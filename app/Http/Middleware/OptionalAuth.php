<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class OptionalAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guard = 'api'): Response
    {
        // Try to authenticate if token is present
        if ($request->bearerToken() || $request->header('Authorization')) {
            try {
                // Try to authenticate the user using JWT
                $user = JWTAuth::parseToken()->authenticate();
                
                if ($user) {
                    // Set the authenticated user
                    Auth::guard($guard)->setUser($user);
                }
            } catch (JWTException $e) {
                // Token is invalid, expired, or not provided
                // Continue as guest user - don't throw exception
            } catch (\Exception $e) {
                // Any other authentication error
                // Continue as guest user - don't throw exception
            }
        }

        return $next($request);
    }
}
