<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    use HasFactory;
    protected $fillable = [
        'audit_code', 'title', 'description', 'audit_type', 'priority',
        'status', 'planned_start_date', 'planned_end_date',
        'actual_start_date', 'actual_end_date', 'created_by', 'approved_by',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
        'actual_start_date' => 'date',
        'actual_end_date' => 'date',
    ];

    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function workingPapers() { return $this->hasMany(WorkingPaper::class); }
    public function findings() { return $this->hasMany(Finding::class); }
    public function qualityAssessments() { return $this->hasMany(QualityAssessment::class); }

    public function risks()
    {
        return $this->belongsToMany(RiskRegister::class, 'audit_risks', 'audit_id', 'risk_id')->withTimestamps();
    }

    public function getCycleTimeAttribute()
    {
        if ($this->actual_start_date && $this->actual_end_date) {
            return $this->actual_start_date->diffInDays($this->actual_end_date);
        }
        return null;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'gray',
            'planned' => 'blue',
            'in_progress' => 'yellow',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'high' => 'red',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }
}
