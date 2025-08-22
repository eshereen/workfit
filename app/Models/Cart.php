<?php

namespace App\Models;

use Database\Factories\CartFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    /** @use HasFactory<CartFactory> */
    use HasFactory;
    protected $fillable = ['user_id', 'session_id'];

   public function items():HasMany
    {
        return $this->hasMany(CartItem::class);
    }




}
