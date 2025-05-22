@component('mail::message')
 {{ $subject }}

<div style="background-color: #f3f4f6; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
    {!! nl2br(e($content)) !!}
</div>

<p style="margin-top: 20px;">
    Thank you for contacting us.
</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent