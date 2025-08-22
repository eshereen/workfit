<?php

namespace App\Livewire;

use Exception;
use App\Services\LoyaltyService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LoyaltyPoints extends Component
{
    public $pointsToRedeem = 100;
    public $showRedeemForm = false;
    public $redemptionMessage = '';
    public $redemptionError = '';

    protected $rules = [
        'pointsToRedeem' => 'required|integer|min:100',
    ];

    public function render()
    {
        $user = Auth::user();
        $loyaltyBalance = $user ? $user->loyaltyBalance() : 0;
        $redemptionValue = $user ? app(LoyaltyService::class)->calculateRedemptionValue($this->pointsToRedeem) : 0;

        return view('livewire.loyalty-points', [
            'loyaltyBalance' => $loyaltyBalance,
            'redemptionValue' => $redemptionValue,
        ]);
    }

    public function toggleRedeemForm()
    {
        $this->showRedeemForm = !$this->showRedeemForm;
        $this->resetValidation();
        $this->redemptionMessage = '';
        $this->redemptionError = '';
    }

    public function redeemPoints()
    {
        $this->validate();

        try {
            $user = Auth::user();
            $loyaltyService = app(LoyaltyService::class);

            if (!$loyaltyService->canRedeemPoints($user, $this->pointsToRedeem)) {
                $this->redemptionError = 'Cannot redeem points. Check minimum requirement and balance.';
                return;
            }

            $loyaltyService->redeemPointsForDiscount($user, $this->pointsToRedeem, null, 'Manual redemption from dashboard');

            $this->redemptionMessage = "Successfully redeemed {$this->pointsToRedeem} points!";
            $this->showRedeemForm = false;
            $this->pointsToRedeem = 100;

        } catch (Exception $e) {
            $this->redemptionError = $e->getMessage();
        }
    }

    public function updatedPointsToRedeem()
    {
        $this->resetValidation();
        $this->redemptionMessage = '';
        $this->redemptionError = '';
    }
}
