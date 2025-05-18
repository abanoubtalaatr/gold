<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class OTP
{
    public static function generateOtp($length = null)
    {
        $otp = 1234;
        return $otp;

        if ($length)
            return Str::random($length);

        return str_pad(mt_rand(1000, 9999), 4, 0, STR_PAD_LEFT);
    }
}
