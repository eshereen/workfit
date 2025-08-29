<?php

namespace Database\Factories;
use App\Models\Subcategory; 

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
      public function configure()
    {
        return $this->afterCreating(function (Subcategory $subcategory) {
            // Array of placeholder image URLs (you can add more URLs here)
           $mainImage = public_path('imgs/' . $this->faker->numberBetween(1, 6) . '.jpg');

$subcategory->addMedia($mainImage)
    ->preservingOriginal()
    ->toMediaCollection('main_image');


         
        });
    }
}
