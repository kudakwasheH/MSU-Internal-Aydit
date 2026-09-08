<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Request;

class LogAuthenticationEvents
{
    public function handle($event)
    {
        $action = '';
        $userId = null;
        $module = 'Auth';
        $newData = null;

        if ($event instanceof Login) {
            $action = 'LOGIN_SUCCESS';
            $userId = $event->user->id;
        } elseif ($event instanceof Logout) {
            $action = 'LOGOUT';
            $userId = $event->user->id;
        } elseif ($event instanceof Failed) {
            $action = 'LOGIN_FAILED';
            $newData = [
                'email' => $event->credentials['email'] ?? 'unknown',
            ];
            $userId = $event->user ? $event->user->id : null;
        } elseif ($event instanceof Lockout) {
            $action = 'ACCOUNT_LOCKOUT';
            $newData = [
                'email' => $event->request->email ?? 'unknown',
            ];
        }

        if ($action) {
            AuditLog::create([
                'user_id' => $userId,
                'action' => $action,
                'module' => $module,
                'record_id' => 0, // No specific record for auth events
                'new_data' => $newData,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }
}
