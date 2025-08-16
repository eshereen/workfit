<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Support\Str;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $name = $this->faker->unique()->words(3, true);
        $gender = $this->faker->randomElement(['men', 'women', 'unisex']);

        return [
            'name' => ucfirst($name),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $this->faker->randomFloat(2, 10, 200),
            'compare_price' => $this->faker->optional()->randomFloat(2, 200, 300),
            'featured' => $this->faker->boolean(20),
            'category_id' => Category::factory(),
             'subcategory_id' => Subcategory::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            // Array of placeholder image URLs (you can add more URLs here)
            $placeholderImages = [
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=2960&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?q=80&w=3064&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?q=80&w=2367&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1576566588028-4147f3842f27?q=80&w=2400&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1740711152088-88a009e877bb?q=80&w=2960&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'https://images.unsplash.com/photo-1522706604291-210a56c3b376?q=80&w=2400&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
            ];

            // Randomly select main image
            $mainImage = $this->faker->randomElement($placeholderImages);
            $product->addMediaFromUrl($mainImage)
                ->toMediaCollection('main_image');

            // Randomly select 3-5 gallery images (avoiding duplicates)
            $galleryImages = $this->faker->randomElements($placeholderImages, $this->faker->numberBetween(3, 5));
            foreach ($galleryImages as $imageUrl) {
                $product->addMediaFromUrl($imageUrl)
                    ->toMediaCollection('product_images');
            }
        });
    }

    public function hasVariants($count = 1)
{
    return $this->has(ProductVariant::factory()->count($count), 'variants');
}
}
