<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Seat;
use App\Models\Journey;
use App\Models\User;
use App\Strategy\PaymentContext;
use App\Strategy\WalletPayment;
use App\Strategy\CreditCardPayment;
use App\Strategy\EWalletPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentApiController extends Controller
{
    // ---------------- STRIPE TOP-UP ----------------
    public function topup(Request $request)
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

            return response()->json(['message' => 'Top-up successful!'], 200);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ---------------- GET BOOKING INFO ----------------
    public function getBookingInfo($bookingId)
    {
        $booking = Booking::where('BookingID', $bookingId)->first();

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $journey = Journey::where('JourneyID', $booking->JourneyID)
            ->with('train')
            ->first();

        return response()->json([
            'booking' => [
                'BookingID' => $booking->BookingID,
                'Price'     => $booking->Price,
            ],
            'journey' => $journey ? [
                'TrainNo'      => $journey->train->TrainNo ?? 'Unknown',
                'FromLocation' => $journey->FromLocation,
                'ToLocation'   => $journey->ToLocation,
            ] : null,
        ], 200);
    }

    // ---------------- COMPLETE PAYMENT ----------------
    public function completePayment(Request $request, $bookingId)
{
    $booking = Booking::where('BookingID', $bookingId)->firstOrFail();

   
    $validMethods = ['Wallet', 'Credit Card', 'EWallet'];

    if (!in_array($request->method, $validMethods, true)) {
        return response()->json([
            'error' => 'Invalid or unsupported payment method.'
        ], 400);
    }

    DB::beginTransaction();
    try {
    
        switch ($request->method) {
            case 'Wallet': 
                $strategy = new WalletPayment(); break;
            case 'Credit Card': 
                $strategy = new CreditCardPayment(); break;
            case 'EWallet': 
                $strategy = new EWalletPayment(); break;
        }

        $context = new PaymentContext($strategy);

        $user = $request->user() ?? User::find($request->input('user_id'));

        if (!$user) {
            throw new Exception("User not found for payment.");
        }

        $context->execute($user, $booking);

        DB::commit();
        return response()->json([
            'message' => "Payment completed via {$booking->PaymentType}!",
            'booking' => $booking
        ], 200);

    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    // ---------------- REFUND ----------------
    public function processRefund(Request $request, $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $journey = Journey::findOrFail($booking->JourneyID);

        $departureDate = Carbon::parse($journey->DepartureTime);
        $now = Carbon::now();

        if ($now->diffInHours($departureDate, false) <= 72) {
            return response()->json([
                'error' => 'Refund is not allowed within 3 days of departure.'
            ], 400);
        }

        $refundAmount = $booking->Price * 0.8;

        DB::beginTransaction();
        try {
           $user = $request->user() ?? User::find($request->user_id);
            if (!$user) {
                throw new Exception("User not found for refund.");
            }

            $user->wallet_balance += $refundAmount;
            $user->save();

            $booking->Status = 'Refunded';
            $booking->save();

            $tickets = Ticket::where('BookingID', $bookingId)->get();
            foreach ($tickets as $ticket) {
                $ticket->update(['Status' => 'Refunded']);
                Seat::where('SeatID', $ticket->SeatID)
                    ->update(['is_available' => 'Y', 'status' => 'Refunded']);
            }

            $journey->increment('SeatAvailable', $booking->TicketNo);

            DB::commit();

            return response()->json([
                'message' => "Refund successful! RM " . number_format($refundAmount, 2) . " returned to wallet.",
                'refund_amount' => $refundAmount
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Refund failed', [
                'bookingId' => $bookingId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Refund failed: ' . $e->getMessage()], 500);
        }
    }
  public function debugUser(Request $request, $user_id = null)
{
    // fallback to route param first, then request payload
    $userIdToUse = $user_id ?? $request->input('user_id');

    $user = $request->user() ?? \App\Models\User::find($userIdToUse);

    return response()->json([
        'auth_user' => $user ? $user->user_id : null,
        'payload_user_id' => $userIdToUse,
        'all_request' => $request->all(),
    ]);
}


}
