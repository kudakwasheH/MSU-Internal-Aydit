<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Traits\Auditable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, Auditable;

    protected $fillable = [
        'name', 'email', 'password', 'staff_id', 'department', 'position', 'approval_level', 'google_id', 'avatar',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function audits() { return $this->hasMany(Audit::class, 'created_by'); }
    public function approvedAudits() { return $this->hasMany(Audit::class, 'approved_by'); }
    public function ownedRisks() { return $this->hasMany(RiskRegister::class, 'owner_id'); }
    public function assignedFindings() { return $this->hasMany(Finding::class, 'assigned_to'); }
    public function assignedActions() { return $this->hasMany(ActionItem::class, 'assigned_to'); }
    public function reviews() { return $this->hasMany(QualityAssessment::class, 'reviewer_id'); }
}
