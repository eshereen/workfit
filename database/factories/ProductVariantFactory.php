<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'product_id' => \App\Models\Product::factory(),
            'color' => $this->faker->randomElement(['Red','Blue','Green','Black','White','Gray','Beige','Brown','Gold']),
            'size' => $this->faker->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
            'price' => $this->faker->randomFloat(2, 200, 600),
            'stock' => $this->faker->numberBetween(10, 100),
            'weight' => $this->faker->numberBetween(100, 400),
            'quantity' => $this->faker->numberBetween(1, 10), // Assuming quantity is a field for stock management
        ];
    }
}
