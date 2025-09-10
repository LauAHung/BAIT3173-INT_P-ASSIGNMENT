<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Services\UserRegistrationService;
use App\Factories\UserFactoryManager;
use App\Factories\AuthFactoryManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

    public function handleGoogleCallback()
    {
        try {
            // Get user info from Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if the user already exists in your database
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Create new user using the Factory pattern
                $socialData = [
                    'first_name' => $googleUser->user['given_name'] ?? '',
                    'last_name'  => $googleUser->user['family_name'] ?? '',
                    'email'      => $googleUser->getEmail(),
                    'provider_id' => $googleUser->getId(),
                    'provider_data' => $googleUser->user,
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

            return redirect()->route('HomePage')->with('success', 'Google login successful');
        } catch (\Exception $e) {
            return redirect()->route('signin')->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
}

