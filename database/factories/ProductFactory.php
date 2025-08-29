<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->randomElement([
            'PRO-FIT SHORT',
            'SUMMER TANK',
            'ZIP UP HIGH NECK',
            'RUSH WF PANT',
            'BORN TO WIN',
            'Pro-FIT D'
        ]);

        $price = $this->faker->randomFloat(2, 10, 200);
        $comparePrice = $this->faker->optional()->randomFloat(2, $price, $price + 100);

        return [
            'name' => ucfirst($name),
            'description' => $this->faker->paragraphs(3, true),
            'price' => $price,
            'compare_price' => $comparePrice,
            'featured' => $this->faker->boolean(20),
            'category_id' => Category::factory(),
            'subcategory_id' => Subcategory::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            $faker = \Faker\Factory::create();

            // Pick one random image for main image
            $mainImage = public_path('imgs/' . $faker->numberBetween(1, 6) . '.jpg');

            $product->addMedia($mainImage)
                ->preservingOriginal()
                ->toMediaCollection('main_image');

            // Pick 3–5 random images for gallery (excluding duplicates)
            $galleryImages = collect(range(1, 6))
                ->random($faker->numberBetween(3, 5))
                ->map(fn ($num) => public_path("imgs/{$num}.jpg"));

            foreach ($galleryImages as $imagePath) {
                $product->addMedia($imagePath)
                    ->preservingOriginal()
                    ->toMediaCollection('product_images');
            }
        });
    }

    public function hasVariants($count = 1)
    {
        return $this->has(ProductVariant::factory()->count($count), 'variants');
    }
}
