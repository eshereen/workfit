<x-mail::message>
# Welcome to WorkFit!

Thank you for your interest in WorkFit Premium Activewear. We're excited to offer you an exclusive **10% OFF** on your first purchase!

## Your Exclusive Coupon Code:

<x-mail::panel>
<div style="text-align: center; font-size: 24px; font-weight: bold; color: #DC2626; letter-spacing: 2px;">
{{ $coupon->code }}
</div>
</x-mail::panel>

**Discount:** {{ $coupon->value }}% OFF
**Valid Until:** {{ $coupon->expires_at->format('F j, Y') }}
**Usage:** One-time use only

<x-mail::button :url="route('products.index')">
Start Shopping Now
</x-mail::button>

Simply apply this code at checkout to enjoy your discount. Don't miss out on this limited-time offer!

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
