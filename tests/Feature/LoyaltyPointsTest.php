<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Events\OrderPlaced;
use App\Models\LoyaltyTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoyaltyPointsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_awards_loyalty_points_when_order_is_placed()
    {
        // Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 100.00,
            'payment_status' => 'paid',
            'status' => 'paid'
        ]);

        // Act
        event(new OrderPlaced($order));

        // Assert
        $this->assertDatabaseHas('loyalty_transactions', [
            'user_id' => $user->id,
            'points' => 100, // 100 points for $100 order
            'action' => 'purchase',
            'source_type' => Order::class,
            'source_id' => $order->id,
        ]);

        $this->assertEquals(100, $user->loyaltyBalance());
    }

    #[Test]
    public function it_does_not_award_points_for_unpaid_orders()
    {
        // Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 100.00,
            'payment_status' => 'pending',
            'status' => 'pending'
        ]);

        // Act
        event(new OrderPlaced($order));

        // Assert
        $this->assertDatabaseCount('loyalty_transactions', 0);
        $this->assertEquals(0, $user->loyaltyBalance());
    }
}
