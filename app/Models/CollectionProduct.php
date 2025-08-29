<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionProduct extends Model
{
    use HasFactory;

    protected $table = 'collection_products';

    protected $fillable = ['collection_id', 'product_id'];

    public function collection()
    {
        // belongs to ONE collection via collection_id
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    public function product()
    {
        // belongs to ONE product via product_id
        return $this->belongsTo(Product::class, 'product_id');
    }
}

