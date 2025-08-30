<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    // ----------------- STRIPE PAYMENT -----------------
    public function showPaymentForm()
    {
        return view('payment');
    }

    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.5',
            'stripeToken' => 'required'
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $charge = Charge::create([
                'amount' => $request->amount * 100,
                'currency' => 'myr',
                'source' => $request->stripeToken,
                'description' => 'Test Payment',
            ]);

            return back()->with('success', 'Payment successful!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ----------------- BOOKING PAYMENT -----------------
    public function showPaymentPage($bookingId)
    {
        $booking = Booking::find($bookingId);

        if (!$booking) {
            return redirect()->route('booking')->with('error', 'Booking not found.');
        }

        return view('PaymentPage', compact('booking'));
    }

    public function completePayment(Request $request, $bookingId)
    {
        $booking = Booking::find($bookingId);

        if (!$booking) {
            return redirect()->route('booking')->with('error', 'Booking not found.');
        }

        // you can validate form input if you have fields
        $booking->Status = 'Paid';
        $booking->save();

        return redirect()->route('bookingdetail', ['bookingId' => $bookingId])
            ->with('success', 'Payment completed successfully!');
    }
}
