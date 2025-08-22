<?php

namespace App\Models;

use Database\Factories\CollectionProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionProduct extends Model
{
    /** @use HasFactory<CollectionProductFactory> */
    use HasFactory;
    protected $fillable = ['collection_id', 'product_id'];

    public function collection()
    {
        return $this->belongsTo(Collection::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
