<?php

namespace App\Strategies;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FacebookAuthStrategy implements AuthStrategy
{
    public function authenticate()
    {

        return 'Successfully authenticated with Facebook';
    }
}
