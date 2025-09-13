<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if (!$user || $user->account_status !== 'admin') {
            return response()->view('errors.403', [], 403);
        }

        // If 2FA not enabled, force setup
        if (!$user->two_factor_enabled || empty($user->two_factor_secret)) {
            return redirect()->route('admin.2fa.setup');
        }

        // If 2FA enabled but not verified in this session, require challenge
        if (!$request->session()->get('admin.2fa.verified')) {
            return redirect()->route('admin.2fa.challenge');
        }

        return $next($request);
    }
}







