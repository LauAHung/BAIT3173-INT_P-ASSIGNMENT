<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\User;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Show wallet balance page
    public function showWallet()
    {
        $user = Auth::user();
        return view('wallet', compact('user'));
    }

    // Initiate Stripe top-up
    public function topup(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000'
        ]);

        $amount = floatval($request->amount); // ensure numeric
        $amountInCents = (int)($amount * 100); // Stripe expects cents as int

        Stripe::setApiKey(config('stripe.sk'));

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

    // Handle successful Stripe payment
    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return redirect()->route('profile')->with('error', 'Invalid payment session.');
        }

        try {
            Stripe::setApiKey(config('stripe.sk'));
            $session = Session::retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $userId = $session->metadata['user_id']; // access as array
                $amount = floatval($session->metadata['amount']); // cast to float

                $user = User::find($userId);
                if ($user) {
                    $user->wallet_balance = floatval($user->wallet_balance) + $amount; // ensure float
                    $user->save();

                    return redirect()->route('profile')->with('success', 'Topup successful! RM' . number_format($amount, 2) . ' added to your wallet.');
                } else {
                    return redirect()->route('profile')->with('error', 'User not found.');
                }
            } else {
                return redirect()->route('profile')->with('error', 'Payment was not completed.');
            }
        } catch (\Exception $e) {
            return redirect()->route('profile')->with('error', 'Payment verification failed: ' . $e->getMessage());
        }
    }
}
