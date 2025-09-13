<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TwoFactorService;

class TwoFactorController extends Controller
{
    public function showSetup(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || $user->account_status !== 'admin') {
            return response()->view('errors.403', [], 403);
        }

        $service = app(TwoFactorService::class);
        $secret = $user->two_factor_secret ?: $service->generateSecret();
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $secret;
            $user->save();
        }

        $issuer = config('app.name', 'TravelFree');
        $account = $user->email;
        $otpauth = $service->getOtpAuthUrl($issuer, $account, $secret);

        return view('admin.2fa.setup', compact('secret', 'otpauth'));
    }

    public function enable(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || $user->account_status !== 'admin') {
            return response()->view('errors.403', [], 403);
        }
        $service = app(TwoFactorService::class);
        if (!$service->verifyCode($user->two_factor_secret ?? '', $request->code)) {
            return back()->withErrors(['code' => 'Invalid code. Please try again.']);
        }
        $user->two_factor_enabled = true;
        $user->two_factor_confirmed_at = now();
        $user->save();
        $request->session()->put('admin.2fa.verified', true);
        // Immediately redirect to dashboard with flash message (no intermediate page)
        return redirect()->route('dashboard')->with('success', 'Two-factor authentication enabled.');
    }

    public function showChallenge()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || $user->account_status !== 'admin') {
            return response()->view('errors.403', [], 403);
        }
        return view('admin.2fa.challenge');
    }

    public function verify(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user || $user->account_status !== 'admin') {
            return response()->view('errors.403', [], 403);
        }
        $service = app(TwoFactorService::class);
        if (!$service->verifyCode($user->two_factor_secret ?? '', $request->code)) {
            return back()->withErrors(['code' => 'Invalid or expired code.']);
        }
        $request->session()->put('admin.2fa.verified', true);
        return redirect()->route('dashboard')->with('success', '2FA verified.');
    }
}


