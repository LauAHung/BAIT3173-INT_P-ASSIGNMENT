<?php

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminActivityLogger
{
    public function log(string $action, array $details = []): void
    {
        $user = Auth::user();
        $email = $user ? ($user->email ?? 'unknown') : 'guest';

        try {
            AdminActivityLog::create([
                'admin_email' => $email,
                'action' => $action,
                'details' => $details,
            ]);
        } catch (\Throwable $e) {
            // Never block the main action due to logging failures
            Log::warning('AdminActivityLogger failed', [
                'error' => $e->getMessage(),
                'action' => $action,
                'admin_email' => $email,
            ]);
        }
    }
}


