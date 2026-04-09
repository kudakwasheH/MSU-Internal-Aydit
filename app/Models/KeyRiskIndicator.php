<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyRiskIndicator extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'risk_category',
        'threshold_green',
        'threshold_yellow',
        'threshold_red',
        'current_value',
        'status',
    ];
}
