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
        ->whereIn('Status', ['Booked', 'Pending'])
        ->get()
        ->map(function ($booking) {
            $booking->Journey->DepartureTime = \Carbon\Carbon::parse($booking->Journey->DepartureTime)->format('Y-m-d');
            $booking->showViewQR = $booking->Status === 'Booked';
            $booking->showRefund = $booking->Status === 'Booked';
            $booking->showProceedPayment = $booking->Status === 'Pending';
            $booking->showCancel = $booking->Status === 'Pending';
            return $booking;
        });

        Log::info('Ongoing Bookings:', ['bookings' => $ongoingBookings->toArray()]);

        $pastBookings = Booking::with(['journey', 'journey.train'])
        ->whereIn('Status', ['Completed','Cancelled'])
        ->get()
        ->map(function ($booking) {
            $booking->Journey->DepartureTime = \Carbon\Carbon::parse($booking->Journey->DepartureTime)->format('Y-m-d');
            $booking->showViewQR = $booking->Status === 'Completed';
            $booking->showRateTrip = $booking->Status === 'Completed';
            return $booking;
        });

        // Ensure variables are always passed, even if empty
        return view('BookingPage', compact('ongoingBookings', 'pastBookings'));
    }
}