<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAction($model, 'CREATE');
        });

        static::updated(function ($model) {
            $oldData = array_intersect_key($model->getOriginal(), $model->getChanges());
            $newData = $model->getChanges();
            
            // Don't log if only timestamps changed
            unset($oldData['updated_at'], $newData['updated_at']);
            
            if (empty($newData)) return;

            self::logAction($model, 'UPDATE', $oldData, $newData);
        });

        static::deleted(function ($model) {
            self::logAction($model, 'DELETE', $model->getOriginal());
        });
    }

    protected static function logAction($model, $action, $oldData = null, $newData = null)
    {
        if (!Auth::check()) return;

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => class_basename($model),
            'record_id' => $model->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
