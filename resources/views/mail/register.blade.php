<x-mail::message>
    <x-logo email width="200" height="60" />
Welcome to {{ config('app.name') }}
we want to welcome you to our platform and we hope you enjoy your stay.

You have received a coupon code from {{ config('app.name') }}
Coupon: {{ $coupon->code }}
as a new user.
 use it to get a discount on your first purchase.

<x-mail::button :url="config('app.url')">
Visit our website
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
