<?php

namespace App\Strategy;

use App\Models\User;
use App\Models\Booking;

class CreditCardPayment implements PaymentStrategy
{
    public function pay(User $user, Booking $booking): void
    {
        // Simulate external card processing API
        $booking->PaymentType = 'Credit Card';
        $booking->Status = 'Booked';
        $booking->save();
    }
}
