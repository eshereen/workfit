<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $basePrice = $this->faker->numberBetween(1000, 25000); // $10-$250 in cents
        $price = $basePrice * $quantity;

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'product_variant_id' => $this->faker->optional(0.7)->randomElement([ProductVariant::factory()]),
            'quantity' => $quantity,
            'price' => $price,
        ];
    }

    /**
     * Create order item with specific order.
     */
    public function forOrder(Order $order)
    {
        return $this->state(function (array $attributes) use ($order) {
            return [
                'order_id' => $order->id,
            ];
        });
    }

    /**
     * Create order item with specific product.
     */
    public function forProduct(Product $product)
    {
        return $this->state(function (array $attributes) use ($product) {
            return [
                'product_id' => $product->id,
            ];
        });
    }

    /**
     * Create order item with specific variant.
     */
    public function withVariant(ProductVariant $variant)
    {
        return $this->state(function (array $attributes) use ($variant) {
            return [
                'product_id' => $variant->product_id,
                'product_variant_id' => $variant->id,
            ];
        });
    }

    /**
     * Create order item with high quantity.
     */
    public function highQuantity()
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity' => $this->faker->numberBetween(5, 10),
            ];
        });
    }

    /**
     * Create order item with low quantity.
     */
    public function lowQuantity()
    {
        return $this->state(function (array $attributes) {
            return [
                'quantity' => $this->faker->numberBetween(1, 2),
            ];
        });
    }
}
