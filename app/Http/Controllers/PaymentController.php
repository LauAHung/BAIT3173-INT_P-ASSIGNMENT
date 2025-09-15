<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->middleware('auth'); 
        $this->apiBaseUrl = config('app.api_base_url', 'http://localhost:8001/api');
    }

    // ---------------- STRIPE TOP-UP ----------------
    public function showPaymentForm()
    {    $booking = Booking::findOrFail($bookingId);
        return view('payment');
    }

    public function processPayment(Request $request)
    {
        $response = Http::post("{$this->apiBaseUrl}/payment/topup", $request->all());

        return $response->successful()
            ? back()->with('success', $response['message'])
            : back()->with('error', $response['error'] ?? 'Top-up failed.');
    }

    // ---------------- BOOKING PAYMENT ----------------
    public function showPaymentPage($bookingId)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('signin')->with('error', 'Please log in to proceed with payment.');
        }

        $response = Http::get("{$this->apiBaseUrl}/payment/{$bookingId}/info");

        if ($response->successful()) {
            $bookingData = $response->json();
            return view('PaymentPage', [
                'user'    => $user,
                'booking' => (object) $bookingData['booking'],
                'journey' => (object) $bookingData['journey']
            ]);
        }

        return redirect()->route('booking')->with('error', 'Unable to load booking info.');
    }

    public function completePayment(Request $request, $bookingId)
    {
        $user = Auth::user();

        // ✅ Merge user_id into the payload
       $payload = array_merge($request->all(), [
    'user_id' => $user->user_id,  // ✅ use user_id, not id
]);

        $response = Http::post("{$this->apiBaseUrl}/payment/{$bookingId}/pay", $payload);
        
        return $response->successful()
            ? back()->with('success', $response['message'])
            : back()->with('error', $response['error'] ?? 'Payment failed.');
    }

    // ---------------- REFUND ----------------
    public function showRefundPage($bookingId)
{
    $user = Auth::user();

    // Call API to get booking details
    $response = Http::get("{$this->apiBaseUrl}/payment/{$bookingId}/info");

    if ($response->successful()) {
        $bookingData = $response->json();

        return view('RefundPage', [
            'user'    => $user,
            'booking' => (object) $bookingData['booking'],
            'journey' => (object) $bookingData['journey']
        ]);
    }

    return redirect()->route('booking')->with('error', 'Unable to load booking info for refund.');
}


   public function processRefund(Request $request, $bookingId)
{
    $user = Auth::user();

    $payload = array_merge($request->all(), [
        'user_id' => $user->user_id,
    ]);

    $response = Http::post("{$this->apiBaseUrl}/payment/{$bookingId}/refund", $payload);

    return $response->successful()
        ? back()->with('success', $response['message'])
        : back()->with('error', $response['error'] ?? 'Refund failed.');
}

}
