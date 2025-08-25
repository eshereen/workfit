<?php
// database/factories/OrderFactory.php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use App\Models\Customer;
use App\Models\Country;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $subtotal = $this->faker->numberBetween(1000, 50000); // Store as cents
        $taxAmount = (int)($subtotal * 0.1); // 10% tax
        $shippingAmount = $this->faker->numberBetween(500, 2000); // $5-$20
        $discountAmount = $this->faker->numberBetween(0, (int)($subtotal * 0.2)); // 0-20% discount
        $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        $isGuest = $this->faker->boolean(30); // 30% chance of being guest order
        $useBillingForShipping = $this->faker->boolean(70); // 70% chance of using billing for shipping

        return [
            'order_number' => 'ORD-' . $this->faker->unique()->randomNumber(8),
            'guest_token' => $isGuest ? Str::random(32) : null,
            'user_id' => $isGuest ? null : User::factory(),
            'customer_id' => Customer::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'country_id' => Country::factory(),
            'state' => $this->faker->state(),
            'city' => $this->faker->city(),
            'email' => $this->faker->email(),
            'phone_number' => $this->faker->phoneNumber(),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'billing_address' => $this->faker->streetAddress(),
            'billing_building_number' => $this->faker->buildingNumber(),
            'shipping_address' => $useBillingForShipping ? $this->faker->streetAddress() : $this->faker->streetAddress(),
            'shipping_building_number' => $this->faker->buildingNumber(),
            'use_billing_for_shipping' => $useBillingForShipping,
            'notes' => $this->faker->optional(0.3)->sentence(),
            'coupon_id' => $this->faker->optional(0.2)->randomElement([Coupon::factory()]),
            'is_guest' => $isGuest,
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'cash_on_delivery', 'bank_transfer']),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'status' => $this->faker->randomElement(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
        ];
    }

    /**
     * Indicate that the order is for a guest user.
     */
    public function guest()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
                'guest_token' => Str::random(32),
                'is_guest' => true,
            ];
        });
    }

    /**
     * Indicate that the order is for a registered user.
     */
    public function registered()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => User::factory(),
                'guest_token' => null,
                'is_guest' => false,
            ];
        });
    }

    /**
     * Indicate that the order is paid.
     */
    public function paid()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'paid',
                'status' => $this->faker->randomElement(['processing', 'shipped', 'delivered']),
            ];
        });
    }

    /**
     * Indicate that the order is pending payment.
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'pending',
                'status' => 'pending',
            ];
        });
    }

    /**
     * Indicate that the order is delivered.
     */
    public function delivered()
    {
        return $this->state(function (array $attributes) {
            return [
                'payment_status' => 'paid',
                'status' => 'delivered',
            ];
        });
    }
}
