<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingPaper extends Model
{
    protected $fillable = [
        'audit_id', 'title', 'description', 'content', 'file_path', 'version', 'status', 'created_by',
    ];

    public function audit() { return $this->belongsTo(Audit::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
