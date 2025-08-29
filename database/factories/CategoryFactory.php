<?php

namespace Database\Factories;
use App\Models\Category; 
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
                'Men',
                'Women',
                'Kids',
               
                 ]),
            'description' => $this->faker->paragraph(3),
            'parent_id' => null, // Will be set in seeder for subcategories
        ];
    }
      public function configure()
    {
        return $this->afterCreating(function (Category $category) {
            // Array of placeholder image URLs (you can add more URLs here)
         $mainImage = public_path('imgs/' . $this->faker->numberBetween(1, 6) . '.jpg');

$category->addMedia($mainImage)
    ->preservingOriginal()
    ->toMediaCollection('main_image');

        });
    }
}
