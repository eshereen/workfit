{{-- resources/views/emails/newsletter/verify.blade.php --}}
@component('mail::message')
# Confirm your subscription

Please confirm your subscription to WorkFit updates.

@component('mail::button', ['url' => $url])
Confirm Subscription
@endcomponent

If you did not request this, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
