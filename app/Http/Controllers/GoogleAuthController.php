<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Services\UserRegistrationService;
use App\Factories\UserFactoryManager;
use App\Factories\AuthFactoryManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
    private UserRegistrationService $userRegistrationService;
    private UserFactoryManager $userFactoryManager;
    private AuthFactoryManager $authFactoryManager;

    public function __construct(
        UserRegistrationService $userRegistrationService,
        UserFactoryManager $userFactoryManager,
        AuthFactoryManager $authFactoryManager
    ) {
        $this->userRegistrationService = $userRegistrationService;
        $this->userFactoryManager = $userFactoryManager;
        $this->authFactoryManager = $authFactoryManager;
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            // Get user info from Google
            $googleUser = Socialite::driver('google')->user();

            // Normalize incoming data
            $fullName = $googleUser->getName() ?? '';
            $firstName = $googleUser->user['given_name'] ?? (string) Str::of($fullName)->beforeLast(' ');
            $lastName = $googleUser->user['family_name'] ?? (string) Str::of($fullName)->afterLast(' ');
            if (trim($lastName) === '' && trim($firstName) === '' && $fullName !== '') {
                $firstName = $fullName;
                $lastName = '';
            }

            $email = $googleUser->getEmail();

            // Try to find by social id first
            $user = User::where('social_provider', 'google')
                ->where('social_provider_id', (string) $googleUser->getId())
                ->first();

            // Fallback: find by email if available
            if (!$user) {
                $user = $email ? User::where('email', $email)->first() : null;
            }

            if (!$user) {
                // Create new user using the Factory pattern
                if (!$email) {
                    // If Google doesn't provide email, synthesize an internal-only placeholder
                    $email = 'google_' . (string) $googleUser->getId() . '@google.local';
                }

                $socialData = [
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                    'provider_id' => (string) $googleUser->getId(),
                    'provider_data' => $googleUser->user ?? [],
                    'profile_picture' => $googleUser->getAvatar() ?? ''
                ];

                $user = $this->userFactoryManager->createUser('google', $socialData);
            } else {
                // Check if user is allowed (active or admin) before logging in
                if (!in_array($user->account_status, ['active', 'admin'])) {
                    if ($user->account_status === 'suspended') {
                        return redirect()->route('signin')->withErrors([
                            'email' => 'Your account is currently suspended. It may be caused by breaking the rules. Please contact administrator for assistance.',
                        ]);
                    }
                    
                    return redirect()->route('signin')->withErrors([
                        'email' => 'Your account is not active. Please check your email for verification.',
                    ]);
                }
                
                // Update last login for existing user
                $user = $this->userRegistrationService->handleUserLogin($user);
            }

            Auth::login($user);
            // Regenerate session; mark recent re-auth; enforce 2FA for admin
            $request->session()->regenerate();
            if ($user->account_status === 'admin') {
                $request->session()->put('admin.recently_authenticated_at', time());
                $request->session()->forget('admin.2fa.verified');
                if ($user->two_factor_enabled && !empty($user->two_factor_secret)) {
                    return redirect()->route('admin.2fa.challenge');
                }
                return redirect()->route('admin.2fa.setup');
            }
            return redirect()->route('HomePage')->with('success', 'Google login successful');
        } catch (\Exception $e) {
            return redirect()->route('signin')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}

