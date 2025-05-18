<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Vendor/Auth/ResetPassword', [
            'email' => $request->email,
            'token' => $request->query('token'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'token.required' => trans('validation.required', ['attribute' => trans('auth.token')]),
            'email.required' => trans('validation.required', ['attribute' => trans('auth.email')]),
            'email.email' => trans('validation.email', ['attribute' => trans('auth.email')]),
            'password.required' => trans('validation.required', ['attribute' => trans('auth.password')]),
            'password.confirmed' => trans('validation.confirmed', ['attribute' => trans('auth.password')]),
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasRole('vendor')) {
            throw ValidationException::withMessages([
                'email' => __('auth.invalid_user_type'),
            ]);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('vendor.login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
} 