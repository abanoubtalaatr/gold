<?php

namespace App\Http\Controllers\Auth;

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
    /**
     * Display the password reset link request view.
     */

    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Send a password reset code to the given user.
     *
     * @bodyParam email email required the email of the user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request): RedirectResponse
    {

        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetCode(
            $this->credentials($request),
            null
        );
        return $response == 'passwords.sent_code'
            ? $this->sendResetCodeResponse($request, $response)
            : $this->sendResetCodeFailedResponse($request, $response);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetCodeResponse(Request $request, $response)
    {
        $message = trans($response);

        if ($response === 'passwords.sent_code') {
            $message = trans('messages.otp_sent');
        }

        return redirect()->route('auth.password.otp.form', ['email' => $request->email])->with('status', $message);
    }
    /**
     * Get the response for a failed password reset link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetCodeFailedResponse(Request $request, $response)
    {
        Log::error('Failed to send OTP: ' . trans($response));

        return back()->withErrors(['email' => $request->only('email'), 'success' => false, 'message' => __('errors.otp_failed')]);
    }

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(['email']);
    }


    public function otpForm(Request $request): Response
    {
        return Inertia::render('Auth/OtpVerification', [
            'email' => $request->query('email'),
            'status' => session('status'),
        ]);
    }


    public function resendOtp(Request $request)
    {

        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
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

        $user = User::where('email', $request->email)->firstOrFail();

        $exists = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->otp)->exists();

        if (!$exists) {

            return back()->withErrors(['otp' => __('messages.otp_invalid')]);
        }
        $token = Password::createToken($user);

        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ])
            ->with('status', __('messages.otp_verified'));
    }
}
