<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;

class PasswordResetLinkController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Vendor/Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validateEmail($request);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasRole('vendor')) {
            return back()->withErrors(['email' => __('auth.invalid_user_type')]);
        }

        $response = $this->broker()->sendResetCode(
            $this->credentials($request),
            null
        );

        return $response == 'passwords.sent_code'
            ? $this->sendResetCodeResponse($request, $response)
            : $this->sendResetCodeFailedResponse($request, $response);
    }

    protected function sendResetCodeResponse(Request $request, $response)
    {
        $message = trans($response);

        if ($response === 'passwords.sent_code') {
            $message = trans('messages.otp_sent');
        }

        return redirect()->route('vendor.password.otp.form', ['email' => $request->email])->with('status', $message);
    }

    protected function sendResetCodeFailedResponse(Request $request, $response)
    {
        Log::error('Failed to send OTP: ' . trans($response));

        return back()->withErrors(['email' => $request->only('email'), 'success' => false, 'message' => __('errors.otp_failed')]);
    }

    public function broker()
    {
        return app('auth.password.broker');
    }

    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
    }

    protected function credentials(Request $request)
    {
        return $request->only(['email']);
    }

    public function otpForm(Request $request): Response
    {
        return Inertia::render('Vendor/Auth/OtpVerification', [
            'email' => $request->query('email'),
            'status' => session('status'),
        ]);
    }

    public function resendOtp(Request $request)
    {
        $this->validateEmail($request);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasRole('vendor')) {
            return back()->withErrors(['email' => __('auth.invalid_user_type')]);
        }
    

        $response = $this->broker()->sendResetCode(
            $this->credentials($request),
            null
        );

        return $response == 'passwords.sent_code'
            ? $this->sendResetCodeResponse($request, $response)
            : $this->sendResetCodeFailedResponse($request, $response);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $otp = implode('', $request->input('otp'));

        $request->merge(['otp' => $otp]);
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric',
        ], [
            'email.required' => trans('validation.required', ['attribute' => trans('auth.email')]),
            'email.email' => trans('validation.email', ['attribute' => trans('auth.email')]),
            'otp.required' => trans('validation.required', ['attribute' => trans('auth.otp')]),
            'otp.numeric' => trans('validation.numeric', ['attribute' => trans('auth.otp')]),
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->hasRole('vendor')) {
            return back()->withErrors(['email' => __('auth.invalid_user_type')]);
        }

        $exists = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)->exists();

        if (!$exists) {
            return back()->withErrors(['otp' => __('messages.otp_invalid')]);
        }

        $token = Password::createToken($user);

        return redirect()->route('vendor.password.reset', [
            'token' => $token,
            'email' => $user->email,
        ])->with('status', __('messages.otp_verified'));
    }
} 