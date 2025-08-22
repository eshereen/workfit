<?php

namespace App\Models;

use Database\Factories\CartItemFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CartItem extends Model
{
    /** @use HasFactory<CartItemFactory> */
    use HasFactory;
    protected $fillable = ['cart_id', 'product_id', 'quantity', 'price'];


     public function cart():BelongsTo
 {
     return $this->belongsTo(Cart::class);
 }
}
