<?php

namespace App\Models;

use Database\Factories\AnalyticFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    /** @use HasFactory<AnalyticFactory> */
    use HasFactory;
    protected $fillable = ['service', 'tracking_id', 'active'];
}
