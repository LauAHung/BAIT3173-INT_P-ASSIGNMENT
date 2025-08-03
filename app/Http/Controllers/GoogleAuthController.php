<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Services\UserRegistrationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    private UserRegistrationService $userRegistrationService;

    public function __construct(UserRegistrationService $userRegistrationService)
    {
        $this->userRegistrationService = $userRegistrationService;
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        // Get user info from Google
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if the user already exists in your database
        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            // Create new user using the Builder pattern
            $socialData = [
                'first_name' => $googleUser->user['given_name'] ?? '',
                'last_name'  => $googleUser->user['family_name'] ?? '',
                'email'      => $googleUser->getEmail(),
                'provider'   => 'google',
                'provider_id' => $googleUser->getId(),
                'provider_data' => $googleUser->user,
                'profile_picture' => $googleUser->getAvatar() ?? ''
            ];

            $user = $this->userRegistrationService->registerSocialMediaUser($socialData);
        } else {
            // Update last login for existing user
            $user = $this->userRegistrationService->handleUserLogin($user);
        }

        Auth::login($user);

        return redirect()->route('profile');
    }
}

