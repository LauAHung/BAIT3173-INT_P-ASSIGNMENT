<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;

class BookingDetailController extends Controller
{
    public function show($bookingId)
    {
        // Fetch the booking with related journey and train data
        $booking = Booking::with(['journey', 'journey.train'])->where('BookingID', $bookingId)->firstOrFail();

        // Fetch all tickets associated with this booking
        $tickets = Ticket::with(['journey', 'seat', 'passenger'])
            ->where('BookingID', $bookingId)
            ->get();

        return view('BookingDetailPage', compact('booking', 'tickets'));
    }
}