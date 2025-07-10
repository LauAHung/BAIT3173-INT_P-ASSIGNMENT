<?php

namespace App\Strategies;

class FacebookAuthStrategy implements AuthStrategy
{
    public function authenticate()
    {
        // Facebook authentication logic here
        return 'Facebook authentication successful';
    }
}
