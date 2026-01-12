<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = [
        'name',
        'country_id',
        'country_code',
    ];

    /**
     * Get the country that owns the state
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
