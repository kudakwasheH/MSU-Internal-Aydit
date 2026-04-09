<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskTreatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'risk_id',
        'strategy',
        'description',
        'owner_id',
        'status',
        'due_date',
        'completion_percentage',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function risk()
    {
        return $this->belongsTo(RiskRegister::class, 'risk_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
