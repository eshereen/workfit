<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'name' => $this->faker->randomElement([
                'Man',
                'Women',
                'Kids',
               
                 ]),
            'description' => $this->faker->paragraph(3),
            'parent_id' => null, // Will be set in seeder for subcategories
        ];
    }
}
