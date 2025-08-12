<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showWallet()
    {
        $user = Auth::user();
        return view('wallet', compact('user'));
    }

    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000'
        ]);

        $amount = $request->amount;
        $amountInCents = (int)($amount * 100); // Convert to cents for Stripe

        \Stripe\Stripe::setApiKey(config('stripe.sk'));

        $session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'myr',
                        'product_data' => [
                            'name' => 'TravelFree Wallet Topup'
                        ],
                        'unit_amount' => $amountInCents,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('wallet.success') . '?session_id={CHECKOUT_SESSION_ID}&amount=' . $amount,
            'cancel_url' => route('profile'),
            'metadata' => [
                'user_id' => Auth::id(),
                'amount' => $amount
            ]
        ]);

        return redirect()->away($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        $amount = $request->get('amount');

        if (!$sessionId || !$amount) {
            return redirect()->route('profile')->with('error', 'Invalid payment session.');
        }

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
            
            if ($session->payment_status === 'paid') {
                // Update user's wallet balance
                $user = Auth::user();
                $user->wallet_balance += (float)$amount;
                $user->save();

                return redirect()->route('profile')->with('success', 'Topup successful! RM' . number_format($amount, 2) . ' has been added to your wallet.');
            } else {
                return redirect()->route('profile')->with('error', 'Payment was not completed.');
            }
        } catch (\Exception $e) {
            return redirect()->route('profile')->with('error', 'Payment verification failed.');
        }
    }
}
