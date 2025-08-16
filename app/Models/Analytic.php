<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    /** @use HasFactory<\Database\Factories\AnalyticFactory> */
    use HasFactory;
    protected $fillable = ['service', 'tracking_id', 'active'];
}
