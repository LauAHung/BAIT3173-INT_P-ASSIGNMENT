<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    // ----------- STRIPE TOP-UP ----------
    public function showPaymentForm()
    {
        return view('payment'); // this is payment.blade.php (Stripe)
    }
public function processPayment(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:0.5',
        'stripeToken' => 'required'
    ]);

    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

    try {
        $amount = floatval($request->amount);

        \Stripe\Charge::create([
            'amount' => (int)($amount * 100), // safe integer for Stripe
            'currency' => 'myr',
            'source' => $request->stripeToken,
            'description' => 'Wallet Top-up',
        ]);

        return back()->with('success', 'Top-up successful!');
    } catch (\Exception $e) {
        return back()->with('error', $e->getMessage());
    }
}


    // ----------- BOOKING PAYMENT ----------
   public function showPaymentPage($bookingId)
    {
        $user = Auth::user(); // logged-in user
        $booking = Booking::find($bookingId);

        if (!$booking) {
            return redirect()->route('booking')->with('error', 'Booking not found.');
        }

        // ✅ Pass BOTH variables to view
        return view('PaymentPage', compact('user', 'booking'));
    }

    public function completePayment(Request $request, $bookingId)
{
    $user = Auth::user();

    // Fetch the booking
    $booking = Booking::find($bookingId);
    if (!$booking) {
        return redirect()->route('booking')->with('error', 'Booking not found.');
    }

    // Ensure numeric comparison
    $bookingPrice = floatval($booking->Price);
    $userBalance = floatval($user->wallet_balance);

    if ($userBalance < $bookingPrice) {
        return redirect()->route('booking')->with('error', 'Insufficient wallet balance.');
    }

    DB::beginTransaction();
    try {
        // Deduct wallet balance
        $user->wallet_balance = $userBalance - $bookingPrice;
        $user->save();

        // Update booking: mark as paid via Wallet
        $booking->PaymentType = 'Wallet';
        $booking->Status = 'Booked';  // <- THIS LINE is important
        $booking->save();

        DB::commit();

        return redirect()->route('booking')->with('success', 'Payment completed via Wallet!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('booking')->with('error', 'Payment failed: ' . $e->getMessage());
    }
}

}
