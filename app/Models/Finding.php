<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finding extends Model
{
    protected $fillable = [
        'audit_id', 'title', 'description', 'root_cause', 'impact',
        'recommendation', 'severity', 'status', 'escalation_level',
        'created_by', 'assigned_to',
    ];

    public function audit() { return $this->belongsTo(Audit::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function actionItems() { return $this->hasMany(ActionItem::class); }
    public function escalations() { return $this->hasMany(Escalation::class); }

    public function requiresEscalation()
    {
        return in_array($this->severity, ['high', 'critical'])
            && !in_array($this->status, ['resolved', 'closed'])
            && $this->created_at->diffInDays(now()) > 7;
    }

    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'critical' => 'red',
            'high' => 'orange',
            'medium' => 'yellow',
            'low' => 'green',
            default => 'gray',
        };
    }
}
