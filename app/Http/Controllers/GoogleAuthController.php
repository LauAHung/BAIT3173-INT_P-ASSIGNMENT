<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
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
        $user = User::create([
            'first_name' => $googleUser->user['given_name'] ?? '',
            'last_name'  => $googleUser->user['family_name'] ?? '',
            'email'      => $googleUser->getEmail(),
            'password'   => bcrypt(Str::random(16)),
        ]);
    }

    Auth::login($user);

    return redirect()->route('profile');
}

}

