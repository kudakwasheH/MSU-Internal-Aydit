<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'audit_id', 'title', 'executive_summary', 'scope', 'status', 'prepared_by',
        'senior_reviewer_id', 'chief_approver_id', 'comments', 'review_notes',
        'draft_issue_date', 'final_issue_date'
    ];

    protected $casts = [
        'draft_issue_date' => 'datetime',
        'final_issue_date' => 'datetime',
        'review_notes' => 'array',
    ];

    public function audit() { return $this->belongsTo(Audit::class); }
    public function preparer() { return $this->belongsTo(User::class, 'prepared_by'); }
    public function seniorReviewer() { return $this->belongsTo(User::class, 'senior_reviewer_id'); }
    public function chiefApprover() { return $this->belongsTo(User::class, 'chief_approver_id'); }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft' => 'Draft Report',
            'pending_senior_review' => 'Pending Senior Auditor Review',
            'pending_chief_approval' => 'Pending Chief Auditor Review',
            'chief_approved' => 'Chief Approved (Ready for Issuance)',
            'returned_for_revision' => 'Returned for Corrections',
            'final_issued' => 'Final Report Issued',
            default => ucwords(str_replace('_', ' ', $this->status)),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft' => 'gray',
            'pending_senior_review' => 'blue',
            'pending_chief_approval' => 'purple',
            'chief_approved' => 'indigo',
            'returned_for_revision' => 'amber',
            'final_issued' => 'emerald',
            default => 'gray',
        };
    }

    public function getWorkflowStepAttribute(): int
    {
        return match($this->status) {
            'draft' => 1,
            'returned_for_revision' => 1,
            'pending_senior_review' => 2,
            'pending_chief_approval' => 3,
            'chief_approved' => 3,
            'final_issued' => 4,
            default => 1,
        };
    }
}
