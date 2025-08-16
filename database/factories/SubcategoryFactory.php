<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subcategory>
 */
class SubcategoryFactory extends Factory
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
                'Spring',
                'Winter',
                'Casual',
                'Sleep Wear',
                 ]),
            'description' => $this->faker->paragraph(3),

        ];
    }
}
