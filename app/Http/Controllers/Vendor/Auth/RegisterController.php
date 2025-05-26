<?php

namespace App\Http\Controllers\Vendor\Auth;

use App\Http\Requests\Vendor\RegiserRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Mail\VendorOtpMail;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Vendor/Auth/Register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'mobile' => 'required|string|max:20|unique:users',
            'store_name_en' => 'required|string|max:255',
            'store_name_ar' => 'required|string|max:255',
            'commercial_registration_number' => 'required|string|max:255|unique:users',
            'commercial_registration_image' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Store registration data in session temporarily
        $registrationData = $validated;
        $registrationData['commercial_registration_image'] = $request->file('commercial_registration_image')
            ->store('commercial_registrations', 'public');
        // Set is_active to false
        $registrationData['is_active'] = false;

        // Generate OTP (6 digits)
        $otp = rand(100000, 999999);
        $otp=123456;
        $expiresAt = now()->addMinutes(10);

        // Store OTP in cache
        Cache::put('vendor_registration_otp:' . $validated['email'], [
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'registration_data' => $registrationData
        ], $expiresAt);

        // Send OTP via email (implement this method)
        $this->sendOtpEmail($validated['email'], $otp);

        return redirect()->route('vendor.verify')->with([
            'email' => $validated['email'],
            'message' => 'OTP sent to your email address'
        ]);
    }


    private function sendOtpEmail($email, $otp)
    {
        // Implement your email sending logic here
        Mail::to($email)->send(new VendorOtpMail($otp));
    }
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('vendor.login');
    }
}