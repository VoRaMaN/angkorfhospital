<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        ActivityLog::log(
            'login',
            "User \"{$event->user->name}\" logged in",
        );
    }
}
