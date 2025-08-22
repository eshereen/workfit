<?php

namespace App\Models;

use Database\Factories\ProductVariantFactory;
use App\Traits\HasSku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    /** @use HasFactory<ProductVariantFactory> */
    use HasFactory,HasSku;
    protected $fillable = ['product_id', 'color', 'size', 'sku', 'stock','price', 'weight', 'quantity'];

    public function getColorCodeAttribute()
    {
        return config('colors.'.$this->color, '#CCCCCC'); // Default to gray if color not found
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }



}
