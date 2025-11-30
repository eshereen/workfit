<?php

namespace App\Models;

use Database\Factories\CountryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /** @use HasFactory<CountryFactory> */
    use HasFactory;
    protected $fillable = ['name', 'code', 'phone_code', 'currency_code', 'tax_rate', 'active'];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function newsletters()
    {
        return $this->hasMany(Newsletter::class);
    }

    public function shippings()
    {
        return $this->hasMany(Shipping::class);
    }
}
