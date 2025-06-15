<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Route;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'csrf_token' => csrf_token(),
            'auth' => $request->user() ? [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
                'notificationCount' => $request->user()->unreadNotifications->count(),
                'avatar' => $request->user()->avatar,
                'is_vendor' => $request->user()->isVendor(),
                'roles' => $request->user()->getRoleNames()->toArray(),
                'vendor_id' => $request->user()->vendor_id,
                'store_name_ar' => $request->user()->store_name_ar,
                'store_name_en' => $request->user()->store_name_en,
            ] : null,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error')
            ],
            'locale' => app()->getLocale(),
            'auth_permissions' => $request->user() ? $request->user()->getPermissionsViaRoles()->pluck('name')->toArray() : []
        ];
    }
}
