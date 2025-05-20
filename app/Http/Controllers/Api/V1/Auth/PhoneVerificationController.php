<?php

// app/Http/Controllers/Api/V1/Auth/PhoneVerificationController.php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PhoneVerificationController extends Controller
{
    use ApiResponseTrait;

    public function initiateChange(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|max:20|unique:users,mobile,' . $user->id,
                'dialling_code' => 'required|string|max:10',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            // Generate OTP (6 digits)
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $otp = 1234;
            $expiresAt = Carbon::now()->addMinutes(15);

            // Delete any existing verification attempts
            PhoneVerification::where('user_id', $user->id)->delete();

            // Create new verification record
            PhoneVerification::create([
                'user_id' => $user->id,
                'phone_number' => $request->phone_number,
                'dialling_code' => $request->dialling_code,
                'otp' => $otp,
                'expires_at' => $expiresAt
            ]);

            // TODO: Send OTP to the phone number (SMS service integration)

            return $this->successResponse(
                null,
                'OTP sent to your phone number',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to initiate phone change',
                ['error' => $e->getMessage()]
            );
        }
    }

    public function verifyAndUpdate(Request $request)
    {
        try {
            $user = Auth::user();

            $validator = Validator::make($request->all(), [
                'otp' => 'required|string|size:4',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            $verification = PhoneVerification::where('user_id', $user->id)
                ->where('otp', $request->otp)
                ->where('expires_at', '>', Carbon::now())
                ->first();

            if (!$verification) {
                return $this->errorResponse(
                    'Invalid or expired OTP',
                    null,
                    422
                );
            }

            // Update user's phone number
            $user->update([
                'mobile' => $verification->phone_number,
                'dialling_code' => $verification->dialling_code
            ]);

            // Delete the verification record
            $verification->delete();

            return $this->successResponse(
                [
                    'phone_number' => $user->mobile,
                    'dialling_code' => $user->dialling_code
                ],
                'Phone number updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to verify and update phone number',
                ['error' => $e->getMessage()]
            );
        }
    }

    public function resendOtp(Request $request)
    {
        try {
            $user = Auth::user();
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $otp = 1234;

            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|string|max:20|unique:users,mobile,' . $user->id,
                'dialling_code' => 'required|string|max:10',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorResponse($validator->errors());
            }

            
            PhoneVerification::where('user_id', $user->id)->delete();

            $expiresAt = Carbon::now()->addMinutes(15);

            // Create new verification record
            PhoneVerification::create([
                'user_id' => $user->id,
                'phone_number' => $request->phone_number,
                'dialling_code' => $request->dialling_code,
                'otp' => $otp,
                'expires_at' => $expiresAt
            ]);
            
            // TODO: Resend OTP to the phone number

            return $this->successResponse(
                null,
                'OTP resent to your phone number'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to resend OTP',
                ['error' => $e->getMessage()]
            );
        }
    }
}
