<?php

namespace App\Models;

use App\Enums\LoyaltyRuleType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoyaltyRule extends Model
{
    /** @use HasFactory<\Database\Factories\LoyaltyRuleFactory> */
    use HasFactory;
    protected $fillable = ['name', 'type', 'config', 'is_active', 'valid_from', 'valid_to'];


    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'type' => LoyaltyRuleType::class,
    ];

    // Scope for active rules
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('valid_to')
                  ->orWhere('valid_to', '>=', now());
            });
    }

    // Scope for earning rules
    public function scopeRedemptionRules($query)
    {
        return $query->where('type', LoyaltyRuleType::REDEMPTION->value);
    }

    public function scopeEarningRules($query)
    {
        return $query->where('type', LoyaltyRuleType::EARNING->value);
    }
}
