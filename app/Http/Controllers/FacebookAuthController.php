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
        // Request email scope and keep stateful flow
        return Socialite::driver('facebook')
            ->scopes(['email'])
            ->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();

            // Normalize incoming data
            $fullName = $facebookUser->getName() ?? '';
            $firstName = $facebookUser->user['first_name'] ?? (string) Str::of($fullName)->beforeLast(' ');
            $lastName = $facebookUser->user['last_name'] ?? (string) Str::of($fullName)->afterLast(' ');
            if (trim($lastName) === '' && trim($firstName) === '' && $fullName !== '') {
                $firstName = $fullName;
                $lastName = '';
            }

            $email = $facebookUser->getEmail();

            // If Facebook doesn't return email, try to find by social id first
            $user = User::where('social_provider', 'facebook')
                ->where('social_provider_id', (string) $facebookUser->getId())
                ->first();

            if (!$user) {
                $user = $email ? User::where('email', $email)->first() : null;
            }

            if (!$user) {
                // Create new user using the Factory pattern
                // If email is missing, synthesize a placeholder that's unique and internal-only
                if (!$email) {
                    $email = 'fb_' . (string) $facebookUser->getId() . '@facebook.local';
                }

                $socialData = [
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                    'provider_id' => (string) $facebookUser->getId(),
                    'provider_data' => $facebookUser->user ?? [],
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

            Auth::login($user, true);

            return redirect()->route('HomePage')->with('success', 'Facebook login successful');
        } catch (\Exception $e) {
            return redirect()->route('signin')->with('error', 'Facebook authentication failed: ' . $e->getMessage());
        }
    }
}
