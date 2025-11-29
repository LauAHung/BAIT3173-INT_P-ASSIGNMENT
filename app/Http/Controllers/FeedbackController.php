<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->middleware('auth'); 
        $this->apiBaseUrl = config('app.api_base_url', 'http://localhost:8001/api');
    }

    // ---------------- CREATE FEEDBACK ----------------
   public function create($bookingId)
{
    // Call the correct API endpoint for rating section info
    $response = Http::get("{$this->apiBaseUrl}/feedback/ratingsection/{$bookingId}/info");

    $bookingData = $response->successful() ? $response->json() : null;

    // Prevent null access
    $booking = isset($bookingData['booking']) ? (object) $bookingData['booking'] : null;
    $journey = isset($bookingData['journey']) ? (object) $bookingData['journey'] : null;

    if (!$booking) {
        return back()->with('error', 'Booking not found.');
    }

    return view('RatingSectionPage', [
        'BookingID' => $booking->booking_id ?? $bookingId,
        'booking'   => $booking,
        'journey'   => $journey
    ]);
}


    // ---------------- STORE FEEDBACK ----------------
    public function store(Request $request, $bookingId)
    {
        $user = Auth::user();

        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:500',
        ]);

        $payload = [
            'rating'   => $request->input('rating'),
            'feedback' => $request->input('feedback'),
            'user_id'  => $user->user_id,
        ];

        $response = Http::post("{$this->apiBaseUrl}/feedback/{$bookingId}/store", $payload);

        if ($response->successful()) {
            return redirect()->route('booking')
                ->with('success', 'Thank you for your feedback!');
        }

        Log::error("Feedback API failed", [
            'bookingId' => $bookingId,
            'user_id'   => $user->user_id,
            'payload'   => $payload,
            'response'  => $response->json(),
        ]);

        return back()->with('error', $response['error'] ?? 'Feedback failed.');
    }

    // ---------------- VIEW FEEDBACK ----------------
    public function viewFeedback()
    {
        $user = Auth::user();

        $response = Http::get("{$this->apiBaseUrl}/feedback/user/{$user->user_id}");

        if ($response->successful()) {
            $data = $response->json();
            return view('ViewFeedbackPage', [
                'myFeedback'    => collect($data['myFeedback']),
                'otherFeedback' => collect($data['otherFeedback']),
            ]);
        }

        return back()->with('error', 'Unable to load feedback.');
    }
}
