<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Seat;
use App\Models\Journey;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;

class BookingApiController extends Controller
{
    public function index($userId)
    {
        $ongoing = Booking::with(['journey', 'journey.train'])
            ->where('UserID', $userId)
            ->whereIn('Status', ['Booked', 'Pending'])
            ->orderBy('created_at', 'desc')
            ->get();

       $past = Booking::with(['journey', 'journey.train'])
            ->where('UserID', $userId)
            ->where('Status', 'Completed')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                // Check if this booking already has feedback
                $booking->hasFeedback = Feedback::where('BookingID', $booking->BookingID)->exists();

                // Only show "Rate Trip" if completed and no feedback yet
                $booking->showRateTrip = $booking->Status === 'Completed' && !$booking->hasFeedback;

                return $booking;
            });

        $refunded = Booking::with(['journey', 'journey.train'])
            ->where('UserID', $userId)
            ->whereIn('Status', ['Refunded', 'Cancelled'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'ongoing' => $ongoing,
            'past' => $past,
            'refunded' => $refunded,
        ]);
    }

    public function show($bookingId, $userId)
    {
        $booking = Booking::with(['journey', 'journey2','journey.train'])
            ->where('BookingID', $bookingId)
            ->where('UserID', $userId)
            ->firstOrFail();

        $tickets = Ticket::with(['journey','journey', 'seat', 'passenger'])
            ->where('BookingID', $bookingId)
            ->get();

        return response()->json([
            'booking' => $booking,
            'tickets' => $tickets,
        ]);
    }

    public function cancel($bookingId, $userId)
    {
        $booking = Booking::where('BookingID', $bookingId)
            ->where('UserID', $userId)
            ->where('Status', 'Pending')
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $booking->update(['Status' => 'Cancelled']);

            $tickets = Ticket::where('BookingID', $bookingId)->get();
            foreach ($tickets as $ticket) {
                $ticket->update(['Status' => 'Cancelled']);
                Seat::where('SeatID', $ticket->SeatID)
                    ->update(['is_available' => 'Y', 'status' => 'Cancelled']);
            }

            $journey = Journey::find($booking->JourneyID);
            if ($journey->train->TrainService === 'ETS') {
                $journey->increment('SeatAvailable', $booking->TicketNo);
            }

            DB::commit();

            return response()->json(['message' => 'Booking cancelled successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Cancellation failed', 'details' => $e->getMessage()], 500);
        }
    }
}
