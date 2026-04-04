<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $request = request();

        ActivityLog::create([
            'user_id' => $event->user?->id,
            'action' => 'failed_login',
            'description' => 'Failed login attempt for "'.e($event->credentials['email'] ?? 'unknown').'"',
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ]);
    }
}
