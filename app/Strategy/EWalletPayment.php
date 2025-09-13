<?php

namespace App\Strategy;

use App\Models\User;
use App\Models\Booking;

class EWalletPayment implements PaymentStrategy
{
    public function pay(User $user, Booking $booking): void
    {
        // Assume user confirmation via "Confirm" button means payment is successful
        $booking->PaymentType = 'TouchNGo';
        $booking->Status = 'Booked';
        $booking->save();
    }
}