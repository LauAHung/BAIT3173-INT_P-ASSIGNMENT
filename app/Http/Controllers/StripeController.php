<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends Controller {
    public function index(){
        return view('test');
    }

    public function checkout(){
        \Stripe\Stripe::setApiKey(config('stripe.sk'));

        $session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'myr',
                        'product_data' => [
                            'name' => 'Send me money!!!'
                        ],
                        'unit_amount' => 200, // Set to 200 cents (RM2.00)
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.index')
        ]);

        return redirect()->away($session->url); // Fix syntax: remove space, add semicolon
    }

    public function success(Request $request){
        $session = \Stripe\Checkout\Session::retrieve($request->get('session_id'));
        if ($session->payment_status === 'paid') {
            return view('BookingPage')->with('message', 'Payment successful!');
        }
        return redirect()->route('stripe.index')->with('error', 'Payment failed.');
    }
}