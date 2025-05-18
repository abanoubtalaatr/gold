<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class ApiLocalization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // First try to get locale from Accept-Language header
        $locale = $request->header('Accept-Language');
        
        // If not found, try X-Language header as fallback
        if (!$locale) {
            $locale = $request->header('X-Language');
        }
        
        // If still not found, try query parameter
        if (!$locale) {
            $locale = $request->query('locale');
        }

        // If locale is found and it's supported
        if ($locale && array_key_exists($locale, LaravelLocalization::getSupportedLocales())) {
            app()->setLocale($locale);
        } else {
            // Set default locale
            app()->setLocale(config('app.fallback_locale'));
        }

        return $next($request);
    }
} 