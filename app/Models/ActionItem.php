<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class ActionItem extends Model
{
    use Auditable;
    protected $fillable = [
        'finding_id', 'action_description', 'assigned_to',
        'due_date', 'status', 'reminder_sent', 'completed_at', 'comments',
    ];

    protected $casts = [
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'reminder_sent' => 'boolean',
    ];

    public function finding() { return $this->belongsTo(Finding::class); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }

    public function isOverdue()
    {
        return $this->status !== 'completed' && $this->due_date && $this->due_date->isPast();
    }
}
