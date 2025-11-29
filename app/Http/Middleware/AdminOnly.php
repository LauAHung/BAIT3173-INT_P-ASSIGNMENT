<?php
/**
 * author: Lau Aik Hung
 * student id: 23WMR14555
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->account_status === 'admin') {
            return $next($request);
        }

        return response()->view('errors.403', [], 403);
    }
}


