<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CollectionProduct extends  Pivot
{
    use HasFactory;

    protected $table = 'collection_products';
    protected $fillable = ['collection_id', 'product_id'];

    public function collection()
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

