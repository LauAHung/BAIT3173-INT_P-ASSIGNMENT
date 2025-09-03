<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Seat;
use App\Models\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Require authentication for all methods
    }

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
        $user = Auth::user();
        if (!$user) {
            Log::warning('Unauthenticated access attempted to showPaymentPage', ['bookingId' => $bookingId]);
            return redirect()->route('login')->with('error', 'Please log in to proceed with payment.');
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            Log::error('Booking not found in showPaymentPage', ['bookingId' => $bookingId]);
            return redirect()->route('booking')->with('error', 'Booking not found.');
        }

        return view('PaymentPage', compact('user', 'booking'));
    }

    public function completePayment(Request $request, $bookingId)
    {
        $user = Auth::user();
        if (!$user) {
            Log::warning('Unauthenticated access attempted to completePayment', ['bookingId' => $bookingId]);
            return redirect()->route('login')->with('error', 'Please log in to complete payment.');
        }

        $booking = Booking::find($bookingId);
        if (!$booking) {
            Log::error('Booking not found in completePayment', ['bookingId' => $bookingId]);
            return redirect()->route('booking')->with('error', 'Booking not found.');
        }

        $bookingPrice = floatval($booking->Price);

        DB::beginTransaction();
        try {
            if ($request->method === 'Wallet') {
                if ($user->wallet_balance < $bookingPrice) {
                    Log::info('Insufficient wallet balance', [
                        'user_id' => $user->id,
                        'wallet_balance' => $user->wallet_balance,
                        'booking_price' => $bookingPrice
                    ]);
                    return view('PaymentPage', [
                        'user' => $user,
                        'booking' => $booking,
                        'insufficientBalance' => true
                    ]);
                }

                $user->wallet_balance -= $bookingPrice;
                $user->save();

                $booking->PaymentType = 'Wallet';
            } elseif ($request->method === 'Credit Card') {
                $booking->PaymentType = 'Credit Card';
            } elseif ($request->method === 'FPX') {
                $booking->PaymentType = 'FPX';
            }

            $booking->Status = 'Booked';
            $booking->save();

            DB::commit();
            Log::info('Payment completed successfully', [
                'bookingId' => $bookingId,
                'paymentType' => $booking->PaymentType
            ]);

            return redirect()->route('booking')->with('success', "Payment completed via {$booking->PaymentType}!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment failed', [
                'bookingId' => $bookingId,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('booking')->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    // ----------- REFUND ----------
public function showRefundPage($bookingId)
{
    $user = Auth::user();
    $booking = Booking::find($bookingId);

    if (!$booking) {
        return redirect()->route('booking')->with('error', 'Booking not found.');
    }

    return view('RefundPage', compact('user', 'booking'));
}

public function processRefund(Request $request, $bookingId)
{
    $user = Auth::user();
    Log::info('Starting refund process', ['bookingId' => $bookingId, 'userId' => $user->id]);

    $booking = Booking::find($bookingId);
    
    $refundAmount = $booking->Price * 0.8; // Deduct 20% charge
    Log::info('Calculated refund amount', [
        'bookingId' => $bookingId,
        'originalPrice' => $booking->Price,
        'refundAmount' => $refundAmount
    ]);

    DB::beginTransaction();
    try {
        // 1. Refund money to wallet
        $user->wallet_balance += $refundAmount;
        $user->save();
        Log::info('Wallet balance updated', [
            'userId' => $user->id,
            'newBalance' => $user->wallet_balance
        ]);

        // 2. Update booking status
        $booking->Status = 'Refunded';
        $booking->save();
        Log::info('Booking status updated to Refunded', ['bookingId' => $bookingId]);

        // 3. Update tickets and free seats
        $tickets = Ticket::where('BookingID', $bookingId)->get();
        Log::info('Found tickets', ['bookingId' => $bookingId, 'ticketCount' => $tickets->count()]);
        foreach ($tickets as $ticket) {
            $ticket->update(['Status' => 'Refunded']);
            Seat::where('SeatID', $ticket->SeatID)
                ->update(['is_available' => 'Y', 'status' => 'Refunded']);
            Log::info('Ticket and seat updated', [
                'ticketId' => $ticket->id,
                'seatId' => $ticket->SeatID
            ]);
        }

        // 4. Update Journey seat availability
        $journey = Journey::find($booking->JourneyID);
        if ($journey) {
            $journey->increment('SeatAvailable', $booking->TicketNo);
            Log::info('Journey seat availability updated', [
                'journeyId' => $booking->JourneyID,
                'seatsAdded' => $booking->TicketNo
            ]);
        } else {
            Log::warning('Journey not found', ['journeyId' => $booking->JourneyID]);
        }

        DB::commit();
        Log::info('Refund process completed successfully', ['bookingId' => $bookingId]);

        return redirect()->route('booking')->with([
            'success' => "Refund successful! RM " . number_format($refundAmount, 2) . " returned to your wallet.",
            'showRefundedTab' => true
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Refund failed', [
            'bookingId' => $bookingId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        return redirect()->route('booking')->with('error', 'Refund failed: ' . $e->getMessage());
    }
    }

}
