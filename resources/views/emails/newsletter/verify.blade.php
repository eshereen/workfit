{{-- resources/views/emails/newsletter/verify.blade.php --}}
<x-mail::message>
# Confirm your subscription

Please confirm your subscription to WorkFit updates.

<x-mail::button :url="$url">
Confirm Subscription
</x-mail::button>

If you did not request this, you can ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
