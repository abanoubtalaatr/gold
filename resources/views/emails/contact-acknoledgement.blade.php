@component('mail::message')


@component('mail::panel')
# Contact Message Received

You received this email as an acknoledgement because you send an inquiry thorough {{ config('app.name') }} platform.
We are happy to tell you that we have received your email and we will be in touch soon.
@endcomponent

    Thanks,
    {{ config('app.name') }}
@endcomponent