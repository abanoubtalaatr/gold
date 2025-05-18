
@component('mail::message')
# Email Verification

You are receiving this email because we received a request to change your email address.

Your verification code is: **{{ $token }}**

Or click the button below to verify your email address:

@component('mail::button', ['url' => url('/api/verify-email?token='.$token)])
Verify Email Address
@endcomponent

If you did not request this change, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent