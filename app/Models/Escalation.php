<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Escalation extends Model
{
    protected $fillable = [
        'finding_id', 'escalated_from_level', 'escalated_to_level',
        'escalated_to', 'reason', 'status', 'escalated_by', 'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function finding() { return $this->belongsTo(Finding::class); }
    public function escalatedToUser() { return $this->belongsTo(User::class, 'escalated_to'); }
    public function escalatedByUser() { return $this->belongsTo(User::class, 'escalated_by'); }
}
