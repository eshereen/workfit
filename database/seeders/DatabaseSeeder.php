<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Subcategory;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'), // Ensure to set a passwordsecurely
            'is_admin' => true,
        ]);
        //create coupon for new User
        Coupon::factory()->create([
            'code' => 'NEW_USER10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'valid_from' => now(),
            'valid_to' => now()->addDays(30),
        ]);
           // Create categories with subcategories
    // Create categories with subcategories
        $categories = Category::factory(5)
            ->has(Subcategory::factory()->count(3))
            ->create();

        // Preload subcategories to avoid N+1 queries
        $categories->load('subcategories');

        // Create products with variants
        $products = Product::factory(50)
            ->hasVariants(3)
            ->create([
                'category_id' => fn() => $categories->random()->id,
                'subcategory_id' => fn($attributes) =>
                    $categories->find($attributes['category_id'])
                        ->subcategories
                        ->random()
                        ->id,
            ]);

        // Create collections and attach existing products
        $collections = Collection::factory(5)->create();

        // Attach products to collections (3 random products per collection)
        $collections->each(function ($collection) use ($products) {
            $collection->products()->attach(
                $products->random(3)->pluck('id')
            );
        });

        $this->call(CountriesTableSeeder::class);
    }
 
}

