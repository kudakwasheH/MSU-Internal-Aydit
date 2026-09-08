<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_id', 'status', 'prepared_by', 'senior_reviewer_id', 'chief_approver_id',
        'comments', 'draft_issue_date', 'final_issue_date'
    ];

    protected $casts = [
        'draft_issue_date' => 'datetime',
        'final_issue_date' => 'datetime',
    ];

    public function audit() { return $this->belongsTo(Audit::class); }
    public function preparer() { return $this->belongsTo(User::class, 'prepared_by'); }
    public function seniorReviewer() { return $this->belongsTo(User::class, 'senior_reviewer_id'); }
    public function chiefApprover() { return $this->belongsTo(User::class, 'chief_approver_id'); }
}
