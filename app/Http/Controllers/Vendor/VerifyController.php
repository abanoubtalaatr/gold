<?php

namespace App\Http\Controllers\Vendor;

use App\Helpers\OTP;
use App\Models\User;
use App\Mail\VendorOtpMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Events\Admin\AdminNotification;

class VerifyController extends Controller
{
    // app/Http/Controllers/Vendor/Auth/VerifyController.php
public function show(Request $request)
{
    return inertia('Vendor/Auth/Verify', [
        'email' => $request->session()->get('email')
    ]);
}

public function verify(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:6'
    ]);

    $cacheKey = 'vendor_registration_otp:'.$request->email;
    $otpData = Cache::get($cacheKey);

    // if (!$otpData || $otpData['otp'] != $request->otp) {
    //     return back()->withErrors(['otp' => 'Invalid OTP code']);
    // }

    // if (now()->gt($otpData['expires_at'])) {
    //     return back()->withErrors(['otp' => 'OTP has expired']);
    // }

    // Create the user
    // $userData = $otpData['registration_data'];
    // $userData['password'] = bcrypt($userData['password']);
    $userData['email_verified_at'] = now();

    // $user = User::create($userData);
    // $user->syncRoles(['vendor']);

    // Clear OTP cache
    Cache::forget($cacheKey);
    $user = User::where('email', $request->email)->first();
    // Log in the user
    Auth::login($user);
    $title = 'New Vendor Registration';
    $message = 'A new vendor has registered';
    $admin = User::whereHas('roles', function($query){
        $query->where('name', 'superadmin');
    })->first();

    if($admin){
        $title = $admin->prefer_language === 'ar' ? ' جديد عميل '  : __('mobile.New Customer');
        $message = $admin->prefer_language === 'ar' ? 'لديك عميل جديد '  : __('mobile.You have a new customer');
        event(new AdminNotification($title, $message, $admin->id));
    }

    foreach(User::where('vendor_id', $admin->id)->get() as $admin){
        $title = $admin->prefer_language === 'ar' ? ' جديد عميل '  : __('mobile.New Customer');
        $message = $admin->prefer_language === 'ar' ? 'لديك عميل جديد '  : __('mobile.You have a new customer');
        event(new AdminNotification($title, $message, $admin->id));
    }

    
    return redirect()->route('vendor.dashboard');
}

public function resend(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $cacheKey = 'vendor_registration_otp:'.$request->email;
    $otpData = Cache::get($cacheKey);

    if (!$otpData) {
        return back()->withErrors(['email' => 'No pending registration found']);
    }

    // Generate new OTP
    $newOtp = rand(100000, 999999);
    $newOtp=123456;
    $expiresAt = now()->addMinutes(10);

    // Update cache
    Cache::put($cacheKey, [
        ...$otpData,
        'otp' => $newOtp,
        'expires_at' => $expiresAt
    ], $expiresAt);

    // Resend email
    $this->sendOtpEmail($request->email, $newOtp);

    return back()->with('status', 'New OTP has been sent to your email');
}

private function sendOtpEmail($email, $otp)
{
    // Implement your email sending logic here
    Mail::to($email)->send(new VendorOtpMail($otp));
}


}