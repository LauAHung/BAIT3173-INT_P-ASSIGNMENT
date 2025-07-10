<?php

namespace App\Strategies;

class GoogleAuthStrategy implements AuthStrategy
{
    public function authenticate()
    {
        // Google authentication logic here
        return 'Google authentication successful';
    }
}
