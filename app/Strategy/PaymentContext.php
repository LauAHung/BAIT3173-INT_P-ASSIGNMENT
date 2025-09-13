<?php

namespace App\Strategy;

class PaymentContext
{
    private PaymentStrategy $strategy;

    public function __construct(PaymentStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function execute($user, $booking): void
    {
        $this->strategy->pay($user, $booking);
    }
}
