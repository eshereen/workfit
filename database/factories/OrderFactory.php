<?php
// database/factories/OrderFactory.php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition()
    {
        $subtotal = $this->faker->randomFloat(2, 10, 500);
        $taxAmount = $subtotal * 0.1; // 10% tax
        $shippingAmount = $this->faker->randomFloat(2, 5, 20);
        $discountAmount = $this->faker->randomFloat(2, 0, 50);
        $totalAmount = $subtotal + $taxAmount + $shippingAmount - $discountAmount;

        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . $this->faker->unique()->randomNumber(8),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $totalAmount,
            'currency' => 'USD',
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'cash_on_delivery']),
            'payment_status' => 'paid', // Default to paid
            'status' => 'paid', // Default to paid
            'billing_address' => json_encode([
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email' => $this->faker->email,
                'phone' => $this->faker->phoneNumber,
                'address' => $this->faker->address,
            ]),
            'shipping_address' => json_encode([
                'first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email' => $this->faker->email,
                'phone' => $this->faker->phoneNumber,
                'address' => $this->faker->address,
            ]),
        ];
    }
}
