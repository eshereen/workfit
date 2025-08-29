<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
      
              return [
            'name' => $this->faker->unique()->country(),
            'code' => $this->faker->unique()->regexify('[A-Z]{2}'),
            'phone_code' => $this->faker->numberBetween(1, 999),
            'currency_code' => $this->faker->randomElement(['USD', 'EUR', 'GBP', 'CAD', 'AUD']),
            'currency_sympol' => $this->faker->randomElement(['$', '€', '£', 'C$', 'A$']),
        ];
       
    }
}
