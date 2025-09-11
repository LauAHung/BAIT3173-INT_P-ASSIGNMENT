<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $this->middleware('auth'); // Require authentication
    }

    // ---------------- STRIPE TOP-UP ----------------
    public function showPaymentForm()
    {
        return view('payment'); // Stripe top-up page
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
                'amount' => (int)($amount * 100), // cents
                'currency' => 'myr',
                'source' => $request->stripeToken,
                'description' => 'Wallet Top-up',
            ]);

            return back()->with('success', 'Top-up successful!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ---------------- BOOKING PAYMENT ----------------
    public function showPaymentPage($bookingId)
    {
        $user = Auth::user();
        if (!$user) {
            Log::warning('Unauthenticated access to showPaymentPage', ['bookingId' => $bookingId]);
            return redirect()->route('signin')->with('error', 'Please log in to proceed with payment.');
        }

        $booking = Booking::findOrFail($bookingId);
        $tickets = $booking->tickets; // eager load tickets

        return view('PaymentPage', compact('user', 'booking', 'tickets'));
    }

    public function completePayment(Request $request, $bookingId)
    {
        $user = Auth::user();
        if (!$user) {
            Log::warning('Unauthenticated access to completePayment', ['bookingId' => $bookingId]);
            return redirect()->route('signin')->with('error', 'Please log in to complete payment.');
        }

        $booking = Booking::findOrFail($bookingId);
        $bookingPrice = floatval($booking->Price);

        DB::beginTransaction();
        try {
            switch ($request->method) {
                case 'Wallet':
                    if ($user->wallet_balance < $bookingPrice) {
                        return view('PaymentPage', [
                            'user' => $user,
                            'booking' => $booking,
                            'insufficientBalance' => true
                        ]);
                    }
                    $user->wallet_balance -= $bookingPrice;
                    $user->save();
                    $booking->PaymentType = 'Wallet';
                    break;

                case 'Credit Card':
                    $booking->PaymentType = 'Credit Card';
                    break;

                case 'EWallet':
                    $booking->PaymentType = 'TouchNGo';
                    break;

                default:
                    throw new \Exception('Invalid payment method.');
            }

            $booking->Status = 'Booked';
            $booking->save();

            DB::commit();

            $displayType = $booking->PaymentType === 'TouchNGo' ? "Touch 'n Go eWallet" : $booking->PaymentType;

            return back()->with('success', "Payment completed via {$displayType}!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment failed', [
                'bookingId' => $bookingId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('booking')->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }

    // ---------------- REFUND ----------------
    public function showRefundPage($bookingId)
    {
        $user = Auth::user();
        $booking = Booking::findOrFail($bookingId);

        return view('RefundPage', compact('user', 'booking'));
    }

    public function processRefund(Request $request, $bookingId)
    {
        $user = Auth::user();
        $booking = Booking::findOrFail($bookingId);

        $journey = Journey::findOrFail($booking->JourneyID);
        $departureDate = Carbon::parse($journey->DepartureTime);
        $now = Carbon::now();

        if ($now->diffInHours($departureDate, false) <= 72) {
            return redirect()->route('refund.page', $bookingId)
                ->with('error', 'Refund is not allowed within 3 days of departure.');
        }

        $refundAmount = $booking->Price * 0.8; // Deduct 20%

        DB::beginTransaction();
        try {
            // Refund to wallet
            $user->wallet_balance += $refundAmount;
            $user->save();

            // Update booking status
            $booking->Status = 'Refunded';
            $booking->save();

            // Update tickets & free seats
            $tickets = Ticket::where('BookingID', $bookingId)->get();
            foreach ($tickets as $ticket) {
                $ticket->update(['Status' => 'Refunded']);
                Seat::where('SeatID', $ticket->SeatID)
                    ->update(['is_available' => 'Y', 'status' => 'Refunded']);
            }

            // Update Journey seat availability
            $journey->increment('SeatAvailable', $booking->TicketNo);

            DB::commit();

            return redirect()->route('booking')->with([
                'success' => "Refund successful! RM " . number_format($refundAmount, 2) . " returned to your wallet.",
                'showRefundedTab' => true
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund failed', [
                'bookingId' => $bookingId,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('booking')->with('error', 'Refund failed: ' . $e->getMessage());
        }
    }
}
