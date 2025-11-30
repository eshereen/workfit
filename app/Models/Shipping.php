<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'price',
        'is_free_for_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_free_for_order' => 'boolean',
    ];

    /**
     * Get the country that owns this shipping method
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}

