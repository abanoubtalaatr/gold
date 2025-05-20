<?php

namespace App\Http\Controllers\Api\V1\Auth;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Models\EmailVerification;
use App\Mail\EmailVerificationMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EmailVerificationController extends Controller
{
    use ApiResponseTrait;

    public function initiateChange(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Generate verification token
            $token = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $token = 1234;
            $expiresAt = Carbon::now()->addMinutes(15); // 1 hour expiration

            // Delete any existing verification attempts
            EmailVerification::where('user_id', $user->id)->delete();

            // Create new verification record
            $verification = EmailVerification::create([
                'user_id' => $user->id,
                'email' => $request->email,
                'token' => $token,
                'expires_at' => $expiresAt
            ]);

            // Send verification email
            Mail::to($request->email)->send(new EmailVerificationMail($token));

            return $this->successResponse(
                null,
                'Verification email sent to your new email address',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to initiate email change',
                ['error' => $e->getMessage()]
            );
        }
    }

    public function verifyAndUpdate(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'otp' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $verification = EmailVerification::where('token', $request->otp)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$verification) {
                return $this->errorResponse(
                    'mobile.Invalid or expired verification token',
                    null,
                    422
                );
            }

            $user = User::findOrFail($verification->user_id);

            // Update user's email
            $user->update([
                'email' => $verification->email,
                'email_verified_at' => Carbon::now()
            ]);

            // Delete the verification record
            $verification->delete();

            return $this->successResponse(
                [
                    'email' => $user->email,
                    'email_verified' => true
                ],
                'mobile.Email updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to verify and update email',
                ['error' => $e->getMessage()]
            );
        }
    }

    public function resendVerification()
    {
        try {
            $user = Auth::user();
            $verification = EmailVerification::where('user_id', $user->id)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$verification) {
                return $this->errorResponse(
                    'No active email change request found',
                    null,
                    404
                );
            }

            // Generate new token and extend expiration
            $newToken = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $newToken = 1234;

            $verification->update([
                'expires_at' => Carbon::now()->addMinutes(60),
                'token' => $newToken
            ]);

            // Resend verification email
            Mail::to($verification->email)->send(new EmailVerificationMail($newToken));

            return $this->successResponse(
                null,
                'mobile.Verification email resent to your email address'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'mobile.Failed to resend verification email',
                ['error' => $e->getMessage()]
            );
        }
    }
}