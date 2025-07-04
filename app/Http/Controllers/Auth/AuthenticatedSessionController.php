<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();
        // Check if the user is inactive
        if ($user && !$user->is_active) {
            return back()->withErrors([
                'is_active' => 'Your account is inactive. Please contact support.',
            ]);
        }

        
        $request->authenticate();

        $request->session()->regenerate();

        
        if (auth()->user()->hasRole('vendor')) {
            
            return redirect()->intended(route("vendor.dashboard"));
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isVendor = $user && $user->hasRole('vendor');
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // Redirect based on user role
        if ($isVendor) {
            return redirect()->route('login')->with('success', __('You have been logged out successfully.'));
        }
        
        return redirect()->route('login')->with('success', __('You have been logged out successfully.'));
    }
}
