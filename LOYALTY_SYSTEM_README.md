# Loyalty System Implementation

## Overview
This loyalty system allows customers to earn points for purchases and redeem them for discounts. The system follows a simple rule: **$1 spent = 1 point earned** and **100 points = $1 discount**.

## Features

### Point Earning
- **Purchase Points**: Customers earn 1 point for every $1 spent on orders
- **Signup Bonus**: 100 points awarded for new account registration
- **Review Points**: 20 points for product reviews
- **Automatic Awarding**: Points are automatically awarded when orders are completed

### Point Redemption
- **Minimum Redemption**: 100 points required to start redeeming
- **Redemption Rate**: 100 points = $1 discount
- **Flexible Amounts**: Customers can redeem any amount in 100-point increments
- **Transaction Tracking**: All redemptions are logged with detailed descriptions

## System Components

### 1. Models
- **LoyaltyTransaction**: Stores all point transactions (earned/redeemed)
- **User**: Has loyalty balance calculation and transaction relationships

### 2. Services
- **LoyaltyService**: Core service for managing points
  - `addPoints()`: Award points to users
  - `redeemPoints()`: Deduct points from users
  - `calculateRedemptionValue()`: Convert points to dollar value
  - `canRedeemPoints()`: Check if redemption is possible
  - `redeemPointsForDiscount()`: Redeem points for discount

### 3. Event Listeners
- **AwardLoyaltyPoints**: Automatically awards points when orders are completed

### 4. Livewire Components
- **LoyaltyPoints**: Customer-facing component for viewing balance and redeeming points

### 5. Configuration
- **config/loyalty.php**: Configurable rules for point earning and redemption

## Usage Examples

### For Developers

#### Awarding Points
```php
$loyaltyService = app(LoyaltyService::class);
$loyaltyService->addPoints($user, 50, 'purchase', $order);
```

#### Redeeming Points
```php
$loyaltyService = app(LoyaltyService::class);
$loyaltyService->redeemPointsForDiscount($user, 200, 'Order discount');
```

#### Checking Balance
```php
$balance = $user->loyaltyBalance();
```

### For Customers

1. **View Points**: Visit the dashboard to see current loyalty balance
2. **Redeem Points**: Click "Redeem Points" button (requires minimum 100 points)
3. **Enter Amount**: Specify how many points to redeem (in 100-point increments)
4. **See Value**: System shows dollar value of redemption
5. **Confirm**: Complete redemption to receive discount

## Testing

Use the console command to test the loyalty system:

```bash
php artisan loyalty:test --user-id=1
```

This will:
- Show current balance
- Add test points
- Test redemption calculations
- Perform test redemption
- Display transaction history

## Database Structure

### loyalty_transactions Table
- `user_id`: Foreign key to users table
- `points`: Integer (positive for earned, negative for redeemed)
- `action`: String (purchase, signup, redeem, etc.)
- `description`: Text description of transaction
- `source_type` & `source_id`: Polymorphic relationship to source object
- `created_at` & `updated_at`: Timestamps

## Configuration

### config/loyalty.php
```php
return [
    'rules' => [
        'purchase' => [
            'points_per_dollar' => 1, // 1 point per $1
            'description' => 'Earned from purchase',
        ],
        'signup' => [
            'points' => 100,
            'description' => 'Signup bonus',
        ],
        'review' => [
            'points' => 20,
            'description' => 'Product review',
        ],
    ],
    'redemption' => [
        'ratio' => 100, // 100 points = $1
        'minimum_redemption' => 100, // Minimum points to redeem
    ],
];
```

## Integration Points

### Checkout Process
- Points are automatically awarded when order payment status changes to 'paid'
- Order total amount is converted from cents to dollars for point calculation

### Admin Panel
- Customer resource shows loyalty balance
- Loyalty transactions are tracked and viewable

### Customer Dashboard
- Real-time loyalty balance display
- Point redemption interface
- Transaction history

## Security Features

- Points can only be redeemed if user has sufficient balance
- Minimum redemption requirement prevents abuse
- All transactions are logged and auditable
- Points cannot be negative

## Future Enhancements

- Point expiration system
- Tier-based earning rates
- Special promotion multipliers
- Referral bonus system
- Point transfer between users
- Integration with external loyalty programs
