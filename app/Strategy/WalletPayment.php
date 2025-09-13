<?php

namespace App\Strategy;

use App\Models\User;
use App\Models\Booking;
use Exception;

class WalletPayment implements PaymentStrategy
{
    public function pay(User $user, Booking $booking): void
    {
        if ($user->wallet_balance < $booking->Price) {
            throw new Exception("Insufficient wallet balance.");
        }

        $user->wallet_balance -= $booking->Price;
        $user->save();

        $booking->PaymentType = 'Wallet';
        $booking->Status = 'Booked';
        $booking->save();
    }
}
