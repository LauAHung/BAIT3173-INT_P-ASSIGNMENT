<?php

namespace App\Strategy;

use App\Models\User;
use App\Models\Booking;

interface PaymentStrategy
{
    public function pay(User $user, Booking $booking): void;
}
