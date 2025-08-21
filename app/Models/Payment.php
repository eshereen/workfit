<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;

class Payment extends Model
{
    protected $fillable = [
        'order_id','provider','provider_reference','status','currency','amount_minor',
        'return_url','cancel_url','meta'
    ];
    protected $casts = ['meta' => 'array'];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function transactions() { return $this->hasMany(PaymentTransaction::class); }
}
