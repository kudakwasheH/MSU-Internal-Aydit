<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class RiskRegister extends Model
{
    use Auditable;
    protected $table = 'risk_registers';

    protected $fillable = [
        'risk_code', 'title', 'description', 'category',
        'inherent_likelihood', 'inherent_impact',
        'residual_likelihood', 'residual_impact',
        'status', 'owner_id',
        'kra_at_risk', 'cause', 'consequence', 'last_reviewed_at',
    ];

    protected $casts = [
        'inherent_likelihood' => 'integer',
        'inherent_impact' => 'integer',
        'residual_likelihood' => 'integer',
        'residual_impact' => 'integer',
        'inherent_risk_score' => 'integer',
        'residual_risk_score' => 'integer',
    ];

    public function owner() { return $this->belongsTo(User::class, 'owner_id'); }

    public function audits()
    {
        return $this->belongsToMany(Audit::class, 'audit_risks', 'risk_id', 'audit_id')->withTimestamps();
    }

    public function getRiskLevelAttribute()
    {
        $score = $this->residual_risk_score;
        if ($score >= 15) return 'Extreme';
        if ($score >= 10) return 'High';
        if ($score >= 5) return 'Medium';
        return 'Low';
    }

    public function getRiskColorAttribute()
    {
        return match($this->risk_level) {
            'Extreme' => '#dc3545', // Danger Red
            'High' => '#fd7e14', // Orange
            'Medium' => '#ffc107', // Warning Yellow
            'Low' => '#28a745', // Success Green
        };
    }

    public function getInherentRiskLevelAttribute()
    {
        $score = $this->inherent_risk_score;
        if ($score >= 15) return 'Extreme';
        if ($score >= 10) return 'High';
        if ($score >= 5) return 'Medium';
        return 'Low';
    }

    public function getInherentRiskColorAttribute()
    {
        return match($this->inherent_risk_level) {
            'Extreme' => '#dc3545',
            'High' => '#fd7e14',
            'Medium' => '#ffc107',
            'Low' => '#28a745',
        };
    }

    public function getRouteKeyName()
    {
        return 'risk_code';
    }
}
