<?php


namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FacebookAuthController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        $facebookUser = Socialite::driver('facebook')->stateless()->user();

        $user = User::where('email', $facebookUser->getEmail())->first();

        if (!$user) {
            $user = User::create([
                'first_name' => $facebookUser->user['first_name'] ?? '',
                'last_name'  => $facebookUser->user['last_name'] ?? '',
                'email'      => $facebookUser->getEmail(),
                'password'   => bcrypt(Str::random(16)),
            ]);
        }

        Auth::login($user);

        return redirect()->route('profile');
    }
}
