<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QualityAssessment extends Model
{
    protected $fillable = [
        'audit_id', 'assessment_type', 'reviewer_id', 'review_date',
        'score', 'strengths', 'improvements', 'status',
    ];

    protected $casts = ['review_date' => 'date', 'score' => 'integer'];

    public function audit() { return $this->belongsTo(Audit::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }
}
