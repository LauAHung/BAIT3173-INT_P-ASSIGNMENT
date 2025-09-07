<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('signin')->with('info', 'Please login to view your bookings.');
        }

        // Call API with user_id
        $response = Http::get(url('/api/bookings'), [
            'user_id' => Auth::id()
        ]);

        $data = $response->json();

        return view('BookingPage', [
            'ongoingBookings' => collect($data['ongoing'] ?? []),
            'pastBookings' => collect($data['past'] ?? []),
            'refundedBookings' => collect($data['refunded'] ?? []),
        ]);
    }

    public function show($bookingId, Request $request)
    {
        $response = Http::get(url("/api/bookings/{$bookingId}"), [
            'user_id' => Auth::id()
        ]);

        $data = $response->json();

        return view('BookingDetailPage', [
            'booking' => $data['booking'] ?? null,
            'tickets' => $data['tickets'] ?? [],
        ]);
    }

    public function cancel($bookingId, Request $request)
    {
        $response = Http::post(url("/api/bookings/{$bookingId}/cancel"), [
            'user_id' => Auth::id()
        ]);

        if ($response->successful()) {
            return redirect()->route('booking')->with('success', 'Booking cancelled successfully.');
        }

        return redirect()->route('booking')->with('error', 'Failed to cancel booking.');
    }
}
