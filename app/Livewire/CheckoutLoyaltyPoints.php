<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Auth;

class CheckoutLoyaltyPoints extends Component
{
    public $pointsToRedeem = 0;
    public $availablePoints = 0;
    public $redemptionValue = 0;
    public $showLoyaltySection = false;
    public $errorMessage = '';
    public $successMessage = '';

    protected $rules = [
        'pointsToRedeem' => 'required|integer|min:100',
    ];

    public function mount()
    {
        if (Auth::check()) {
            $this->availablePoints = Auth::user()->loyaltyBalance();
            $this->showLoyaltySection = $this->availablePoints >= 100;
        }
    }

    public function updatedPointsToRedeem()
    {
        $this->resetValidation();
        $this->errorMessage = '';
        $this->successMessage = '';

        if ($this->pointsToRedeem > 0) {
            $loyaltyService = app(LoyaltyService::class);
            $this->redemptionValue = $loyaltyService->calculateRedemptionValue($this->pointsToRedeem);
        } else {
            $this->redemptionValue = 0;
        }

        // Emit event to update order total
        $this->dispatch('loyaltyPointsUpdated', [
            'points' => $this->pointsToRedeem,
            'value' => $this->redemptionValue
        ]);
    }

    public function applyLoyaltyPoints()
    {
        if (!Auth::check()) {
            $this->errorMessage = 'Please log in to use loyalty points.';
            return;
        }

        $this->validate();

        try {
            $user = Auth::user();
            $loyaltyService = app(LoyaltyService::class);

            if (!$loyaltyService->canRedeemPoints($user, $this->pointsToRedeem)) {
                $this->errorMessage = 'Cannot redeem points. Check minimum requirement and balance.';
                return;
            }

            $this->successMessage = "Successfully applied {$this->pointsToRedeem} points for \${$this->redemptionValue} discount!";
            $this->errorMessage = '';

            // Emit event to update order total
            $this->dispatch('loyaltyPointsApplied', [
                'points' => $this->pointsToRedeem,
                'value' => $this->redemptionValue
            ]);

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->successMessage = '';
        }
    }

    public function removeLoyaltyPoints()
    {
        $this->pointsToRedeem = 0;
        $this->redemptionValue = 0;
        $this->errorMessage = '';
        $this->successMessage = '';

        // Emit event to update order total
        $this->dispatch('loyaltyPointsRemoved');
    }

    public function render()
    {
        return view('livewire.checkout-loyalty-points');
    }
}
