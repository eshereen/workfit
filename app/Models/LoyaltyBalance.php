<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyBalance extends Model
{
    /** @use HasFactory<\Database\Factories\LoyaltyBalanceFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'balance', 'total_earned', 'total_redeemed'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Update balance based on points change
    public function updateBalance(int $points)
    {
        $this->balance += $points;

        if ($points > 0) {
            $this->total_earned += $points;
        } else {
            $this->total_redeemed += abs($points);
        }

        $this->save();
    }
}
