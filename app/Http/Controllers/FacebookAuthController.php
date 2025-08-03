<?php


namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Services\UserRegistrationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FacebookAuthController extends Controller
{
    private UserRegistrationService $userRegistrationService;

    public function __construct(UserRegistrationService $userRegistrationService)
    {
        $this->userRegistrationService = $userRegistrationService;
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();

        $user = User::where('email', $facebookUser->getEmail())->first();

        if (!$user) {
            // Create new user using the Builder pattern
            $socialData = [
                'first_name' => $facebookUser->user['first_name'] ?? '',
                'last_name'  => $facebookUser->user['last_name'] ?? '',
                'email'      => $facebookUser->getEmail(),
                'provider'   => 'facebook',
                'provider_id' => $facebookUser->getId(),
                'provider_data' => $facebookUser->user,
                'profile_picture' => $facebookUser->getAvatar() ?? ''
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
