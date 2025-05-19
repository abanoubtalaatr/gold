@component('mail::message')
# Vendor Registration OTP

Your OTP code is: **{{ $otp }}**

This code will expire in 10 minutes.


Thanks,<br>
{{ config('app.name') }}
@endcomponent