<?php

namespace Database\Factories;

use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Collection>
 */
class CollectionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'New Arrivals', 
                'Best Sellers', 
                'Trending', 
                'Sale', 
                'Exclusive'
            ]),
            'description' => $this->faker->paragraph(3),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Collection $collection) {
            // Create a new faker instance here
            $faker = \Faker\Factory::create();

            // Pick one random image for main image
            $mainImage = public_path('imgs/' . $faker->numberBetween(1, 6) . '.jpg');

            if (file_exists($mainImage)) {
                $collection->addMedia($mainImage)
                    ->preservingOriginal()
                    ->toMediaCollection('main_image');
            }
        });
    }
}
