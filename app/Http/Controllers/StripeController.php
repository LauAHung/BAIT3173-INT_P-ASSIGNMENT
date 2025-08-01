<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeController extends Controller {
    public function index(){
        return view('test');
    }

    public function checkout(){
        \Stripe\Stripe::setApiKey(config('stripe.sk'));

        $session = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'myr',
                        'product_data' => [
                            'name' => 'Send me money!!!'
                        ],
                        'unit_amount' => 5,
                    ],
                    'quantity' => 1,
                ],

            ],
            'mode' => 'payment',
            'success_url' => route('success'),
            'cancel_url' => route('stripe.index')
        ]);

        return redirect() -> away($session-> url);
    }

    public function success(){
        return view('test');
    }

}