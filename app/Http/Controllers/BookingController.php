<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Fetch bookings with related journey and train data
        $ongoingBookings = Booking::with(['journey', 'journey.train'])
        ->where('Status', 'Booked')
        ->get()
        ->map(function ($booking) {
            $booking->Journey->DepartureTime = \Carbon\Carbon::parse($booking->Journey->DepartureTime)->format('Y-m-d');
            return $booking;
        });

        $pastBookings = Booking::with(['journey', 'journey.train'])
        ->where('Status', 'Completed')
        ->get()
        ->map(function ($booking) {
            $booking->Journey->DepartureTime = \Carbon\Carbon::parse($booking->Journey->DepartureTime)->format('Y-m-d');
            return $booking;
        });

        // Ensure variables are always passed, even if empty
        return view('BookingPage', compact('ongoingBookings', 'pastBookings'));
    }
}