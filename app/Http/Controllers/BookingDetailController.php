<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingDetailController extends Controller
{
    public function show($bookingId)
    {
        try {
            // Fetch the booking with related journey and train data
            $booking = Booking::with(['journey', 'journey.train'])
                ->where('BookingID', $bookingId)
                ->first();

            if (!$booking) {
                Log::warning('Booking not found: ', ['bookingId' => $bookingId]);
                return redirect()->route('bookingdetails')
                    ->with('error', 'Booking not found. Showing default booking page.');
            }

            // Fetch all tickets associated with this booking
            $tickets = Ticket::with(['journey', 'seat', 'passenger'])
                ->where('BookingID', $bookingId)
                ->get();

            Log::info('Tickets retrieved: ', [
                'bookingId' => $bookingId,
                'count' => $tickets->count(),
                'data' => $tickets->isEmpty() ? 'No data' : $tickets->toArray()
            ]);

            return view('BookingDetailPage', compact('booking', 'tickets'));
        } catch (\Exception $e) {
            Log::error('Error fetching booking: ', [
                'bookingId' => $bookingId,
                'error' => $e->getMessage()
            ]);
            return redirect()->route('bookingdetails')
                ->with('error', 'An error occurred while fetching the booking. Showing default booking page.');
        }
    }
}