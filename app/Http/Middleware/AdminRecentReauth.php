<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminRecentReauth
{
    /**
     * Require that the admin has recently reauthenticated (e.g., within 10 minutes).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || $user->account_status !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $ts = (int) $request->session()->get('admin.recently_authenticated_at', 0);
        $maxAgeSeconds = 10 * 60; // 10 minutes
        if ($ts <= 0 || (time() - $ts) > $maxAgeSeconds) {
            return response()->json([
                'success' => false,
                'message' => 'Re-authentication required for this action.'
            ], 401);
        }

        return $next($request);
    }
}








