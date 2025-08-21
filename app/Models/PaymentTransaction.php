<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = ['payment_id','event','provider_status','payload','idempotency_key'];
    protected $casts = ['payload' => 'array'];

    public function payment() { return $this->belongsTo(Payment::class); }
}
