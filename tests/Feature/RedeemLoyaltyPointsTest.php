<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\LoyaltyTransaction;
use App\Services\LoyaltyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RedeemLoyaltyPointsTest extends TestCase
{
    use RefreshDatabase;

    protected LoyaltyService $loyaltyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loyaltyService = app(LoyaltyService::class);
    }

    #[Test]
    public function user_can_redeem_points_successfully()
    {
        // Arrange: Create a user with 200 points
        $user = User::factory()->create();
        LoyaltyTransaction::factory()->create([
            'user_id' => $user->id,
            'points' => 200,
            'action' => 'purchase',
            'source_type' => null,
            'source_id' => null,
        ]);

        // Act: Redeem 100 points
        $transaction = $this->loyaltyService->redeemPoints($user, 100);

        // Assert
        $this->assertEquals(100, $user->fresh()->loyaltyBalance());
        $this->assertDatabaseHas('loyalty_transactions', [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'points' => -100,
            'action' => 'redeem',
            'description' => 'Points redeemed',
            'source_type' => null,
            'source_id' => null,
        ]);
    }

    #[Test]
    public function user_cannot_redeem_more_points_than_balance()
    {
        // Arrange: Create a user with 50 points
        $user = User::factory()->create();
        LoyaltyTransaction::factory()->create([
            'user_id' => $user->id,
            'points' => 50,
            'action' => 'purchase',
            'source_type' => null,
            'source_id' => null,
        ]);

        // Assert: Expect exception when trying to redeem 100 points
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient points');

        // Act: Attempt to redeem 100 points
        $this->loyaltyService->redeemPoints($user, 100);
    }

    #[Test]
    public function redemption_creates_negative_transaction()
    {
        // Arrange: Create a user with 300 points
        $user = User::factory()->create();
        LoyaltyTransaction::factory()->create([
            'user_id' => $user->id,
            'points' => 300,
            'action' => 'purchase',
            'source_type' => null,
            'source_id' => null,
        ]);

        // Act: Redeem 150 points
        $this->loyaltyService->redeemPoints($user, 150);

        // Assert: Check that a negative transaction was created
        $this->assertDatabaseHas('loyalty_transactions', [
            'user_id' => $user->id,
            'points' => -150,
            'action' => 'redeem',
            'source_type' => null,
            'source_id' => null,
        ]);

        // Assert: Check balance is correct
        $this->assertEquals(150, $user->fresh()->loyaltyBalance());
    }

    #[Test]
    public function multiple_redemptions_work_correctly()
    {
        // Arrange: Create a user with 500 points
        $user = User::factory()->create();
        LoyaltyTransaction::factory()->create([
            'user_id' => $user->id,
            'points' => 500,
            'action' => 'purchase',
            'source_type' => null,
            'source_id' => null,
        ]);

        // Act: Redeem points in multiple transactions
        $this->loyaltyService->redeemPoints($user, 100);
        $this->loyaltyService->redeemPoints($user, 50);
        $this->loyaltyService->redeemPoints($user, 200);

        // Assert: Check final balance
        $this->assertEquals(150, $user->fresh()->loyaltyBalance());

        // Assert: Check all redemption transactions exist
        $this->assertDatabaseCount('loyalty_transactions', 4); // 1 initial + 3 redemptions
        $this->assertDatabaseHas('loyalty_transactions', [
            'user_id' => $user->id,
            'points' => -100,
            'action' => 'redeem',
            'source_type' => null,
            'source_id' => null,
        ]);
        $this->assertDatabaseHas('loyalty_transactions', [
            'user_id' => $user->id,
            'points' => -50,
            'action' => 'redeem',
            'source_type' => null,
            'source_id' => null,
        ]);
        $this->assertDatabaseHas('loyalty_transactions', [
            'user_id' => $user->id,
            'points' => -200,
            'action' => 'redeem',
            'source_type' => null,
            'source_id' => null,
        ]);
    }
    

    #[Test]
    public function redemption_fails_with_negative_points()
    {
        // Arrange: Create a user with points
        $user = User::factory()->create();
        LoyaltyTransaction::factory()->create([
            'user_id' => $user->id,
            'points' => 100,
            'action' => 'purchase',
            'source_type' => null,
            'source_id' => null,
        ]);

        // Assert: Expect exception when trying to redeem negative points
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Points to redeem must be positive');

        // Act: Attempt to redeem -50 points (invalid)
        $this->loyaltyService->redeemPoints($user, -50);
    }

    #[Test]
    public function redemption_can_include_source()
    {
        // Arrange: Create a user with 200 points
        $user = User::factory()->create();
        LoyaltyTransaction::factory()->create([
            'user_id' => $user->id,
            'points' => 200,
            'action' => 'purchase',
            'source_type' => null,
            'source_id' => null,
        ]);

        // Create an order to use as source
        $order = Order::factory()->create();

        // Act: Redeem 100 points with a source
        $transaction = $this->loyaltyService->redeemPoints(
            $user,
            100,
            'Points redeemed for order discount',
            $order
        );

        // Assert
        $this->assertEquals(100, $user->fresh()->loyaltyBalance());
        $this->assertDatabaseHas('loyalty_transactions', [
            'id' => $transaction->id,
            'user_id' => $user->id,
            'points' => -100,
            'action' => 'redeem',
            'description' => 'Points redeemed for order discount',
            'source_type' => Order::class,
            'source_id' => $order->id,
        ]);
    }
    #[Test]
public function redemption_fails_with_zero_points()
{
    // Arrange: Create a user with points
    $user = User::factory()->create();
    LoyaltyTransaction::factory()->create([
        'user_id' => $user->id,
        'points' => 100,
        'action' => 'purchase',
        'source_type' => null,
        'source_id' => null,
    ]);

    // Assert: Expect exception when trying to redeem zero points
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Points to redeem must be positive');

    // Act: Attempt to redeem 0 points
    $this->loyaltyService->redeemPoints($user, 0);
}

#[Test]
public function redemption_fails_with_insufficient_points()
{
    // Arrange: Create a user with only 50 points
    $user = User::factory()->create();
    LoyaltyTransaction::factory()->create([
        'user_id' => $user->id,
        'points' => 50,
        'action' => 'purchase',
        'source_type' => null,
        'source_id' => null,
    ]);

    // Assert: Expect exception when trying to redeem more than available
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Insufficient points');

    // Act: Attempt to redeem 100 points
    $this->loyaltyService->redeemPoints($user, 100);
}
}
