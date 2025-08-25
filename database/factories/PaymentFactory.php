<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition()
    {
        $providers = ['stripe', 'paypal', 'cash_on_delivery', 'bank_transfer'];
        $provider = $this->faker->randomElement($providers);

        $statuses = ['pending', 'completed', 'failed', 'cancelled'];
        $status = $this->faker->randomElement($statuses);

        return [
            'order_id' => Order::factory(),
            'provider' => $provider,
            'provider_reference' => $this->generateProviderReference($provider),
            'status' => $status,
            'currency' => $this->faker->randomElement(['USD', 'EUR', 'GBP']),
            'amount_minor' => $this->faker->numberBetween(1000, 50000), // $10-$500 in cents
            'return_url' => $this->faker->url(),
            'cancel_url' => $this->faker->url(),
            'meta' => $this->generateMetaData($provider, $status),
        ];
    }

    /**
     * Generate provider-specific reference
     */
    private function generateProviderReference(string $provider): string
    {
        return match ($provider) {
            'stripe' => 'pi_' . $this->faker->regexify('[a-zA-Z0-9]{24}'),
            'paypal' => 'PAY-' . $this->faker->regexify('[A-Z0-9]{17}'),
            'cash_on_delivery' => 'COD-' . $this->faker->unique()->randomNumber(6),
            'bank_transfer' => 'BT-' . $this->faker->unique()->randomNumber(8),
            default => $this->faker->unique()->uuid(),
        };
    }

    /**
     * Generate provider-specific metadata
     */
    private function generateMetaData(string $provider, string $status): array
    {
        $baseMeta = [
            'created_at' => $this->faker->dateTimeThisYear(),
            'updated_at' => $this->faker->dateTimeThisYear(),
        ];

        return match ($provider) {
            'stripe' => array_merge($baseMeta, [
                'payment_intent_id' => 'pi_' . $this->faker->regexify('[a-zA-Z0-9]{24}'),
                'client_secret' => 'pi_' . $this->faker->regexify('[a-zA-Z0-9]{24}') . '_secret_' . $this->faker->regexify('[a-zA-Z0-9]{24}'),
                'payment_method_types' => ['card'],
                'capture_method' => 'automatic',
            ]),
            'paypal' => array_merge($baseMeta, [
                'paypal_order_id' => $this->faker->regexify('[A-Z0-9]{17}'),
                'intent' => 'CAPTURE',
                'payment_source' => 'paypal',
            ]),
            'cash_on_delivery' => array_merge($baseMeta, [
                'delivery_instructions' => $this->faker->optional()->sentence(),
                'expected_delivery_date' => $this->faker->dateTimeBetween('now', '+7 days'),
            ]),
            'bank_transfer' => array_merge($baseMeta, [
                'bank_name' => $this->faker->company(),
                'account_number' => $this->faker->bankAccountNumber(),
                'routing_number' => $this->faker->regexify('[0-9]{9}'),
                'reference' => 'ORDER-' . $this->faker->unique()->randomNumber(6),
            ]),
            default => $baseMeta,
        };
    }

    /**
     * Create payment for specific order
     */
    public function forOrder(Order $order)
    {
        return $this->state(function (array $attributes) use ($order) {
            return [
                'order_id' => $order->id,
                'amount_minor' => $order->total_amount,
                'currency' => $order->currency,
            ];
        });
    }

    /**
     * Create successful payment
     */
    public function successful()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }

    /**
     * Create failed payment
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
            ];
        });
    }

    /**
     * Create pending payment
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }
}
