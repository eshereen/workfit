<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\User;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $isRegistered = $this->faker->boolean(70); // 70% chance of being registered user

        return [
            'user_id' => $isRegistered ? User::factory() : null,
            'country_id' => Country::factory(),
            'email' => $this->faker->unique()->safeEmail(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone_number' => $this->faker->phoneNumber(),
            'billing_country_id' => Country::factory(),
            'billing_state' => $this->faker->state(),
            'billing_city' => $this->faker->city(),
            'billing_address' => $this->faker->streetAddress(),
            'billing_building_number' => $this->faker->buildingNumber(),
            'shipping_country_id' => Country::factory(),
            'shipping_state' => $this->faker->state(),
            'shipping_city' => $this->faker->city(),
            'shipping_address' => $this->faker->streetAddress(),
            'shipping_building_number' => $this->faker->buildingNumber(),
            'use_billing_for_shipping' => $this->faker->boolean(70),
        ];
    }

    /**
     * Indicate that the customer is a registered user.
     */
    public function registered()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => User::factory(),
            ];
        });
    }

    /**
     * Indicate that the customer is a guest.
     */
    public function guest()
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => null,
            ];
        });
    }

    /**
     * Use billing address for shipping.
     */
    public function useBillingForShipping()
    {
        return $this->state(function (array $attributes) {
            return [
                'use_billing_for_shipping' => true,
                'shipping_country_id' => $attributes['billing_country_id'],
                'shipping_state' => $attributes['billing_state'],
                'shipping_city' => $attributes['billing_city'],
                'shipping_address' => $attributes['billing_address'],
                'shipping_building_number' => $attributes['billing_building_number'],
            ];
        });
    }
}
