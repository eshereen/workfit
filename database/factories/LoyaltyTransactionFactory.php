<?php

namespace Database\Factories;

use App\Models\LoyaltyTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoyaltyTransactionFactory extends Factory
{
    protected $model = LoyaltyTransaction::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'points' => $this->faker->numberBetween(10, 500),
            'action' => $this->faker->randomElement(['purchase', 'signup', 'review']),
            'description' => $this->faker->sentence,
            'source_type' => null, // Set to null for now
            'source_id' => null,   // Set to null for now
        ];
    }

    // Add a state method for when you need to specify a source
    public function withSource($source)
    {
        return $this->state(function (array $attributes) use ($source) {
            return [
                'source_type' => get_class($source),
                'source_id' => $source->id,
            ];
        });
    }
}
