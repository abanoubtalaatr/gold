@component('mail::message')

# {{__('Message received')}}

@if($subject)
{{__('Subject')}}: {{$subject}}
@endif

{{__('Name')}}: {{ $name }}

{{__('Email')}}: {{ $email }}

@if($phone)
{{__('Mobile')}}: {{ $phone }}
@endif


{{__('Message')}}: {{ $message }}

{{__('Thanks')}},
    {{ config('app.name') }}
@endcomponent