<?php

namespace App\Strategies;

class AuthContext
{
    private $strategy;

    public function __construct(AuthStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function executeStrategy()
    {
        return $this->strategy->authenticate();
    }
}
