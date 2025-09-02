<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Subcategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Users
        //User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@workfit.medsite.dev',
            'password' => bcrypt('password'), // Ensure to set a password securely
            'is_admin' => true,
        ]);

        // Coupon for new user
        Coupon::factory()->create([
            'code' => 'NEW_USER10',
            'type' => 'percentage',
            'value' => 10,
            'starts_at' => now(),
            'expires_at' => now()->addDays(365),
        ]);

        // Categories + Subcategories
        // $categories = Category::factory(5)
        //     ->has(Subcategory::factory()->count(3))
        //     ->create();

        // $categories->load('subcategories');

        // // Products + Variants
        // $products = Product::factory(50)
        //     ->hasVariants(3)
        //     ->create([
        //         'category_id' => fn() => $categories->random()->id,
        //         'subcategory_id' => fn($attributes) =>
        //             optional($categories->find($attributes['category_id']))
        //                 ?->subcategories
        //                 ->random()
        //                 ->id,
        //     ]);

        // // Collections + Attach Products
        // $collections = Collection::factory(5)->create();

        // $collections->each(function ($collection) use ($products) {
        //     $collection->products()->syncWithoutDetaching(
        //         $products->random(3)->pluck('id')
        //     );
        // });

        // Other seeders
        $this->call(CountriesTableSeeder::class);
        // $this->call(OrderSeeder::class);
        // $this->call(OrderWithPaymentsSeeder::class);
    }
}
