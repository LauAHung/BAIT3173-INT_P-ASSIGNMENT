<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Services\UserRegistrationService;
use App\Factories\UserFactoryManager;
use App\Factories\AuthFactoryManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FacebookAuthController extends Controller
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

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();

            $user = User::where('email', $facebookUser->getEmail())->first();

            if (!$user) {
                // Create new user using the Factory pattern
                $socialData = [
                    'first_name' => $facebookUser->user['first_name'] ?? '',
                    'last_name'  => $facebookUser->user['last_name'] ?? '',
                    'email'      => $facebookUser->getEmail(),
                    'provider_id' => $facebookUser->getId(),
                    'provider_data' => $facebookUser->user,
                    'profile_picture' => $facebookUser->getAvatar() ?? ''
                ];

                $user = $this->userFactoryManager->createUser('facebook', $socialData);
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

            return redirect()->route('HomePage')->with('success', 'Facebook login successful');
        } catch (\Exception $e) {
            return redirect()->route('signin')->with('error', 'Facebook authentication failed: ' . $e->getMessage());
        }
    }
}
