<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loyaltyTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\LoyaltyTransactionFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'points', 'action', 'description', 'source_type', 'source_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->morphTo();
    }



}
