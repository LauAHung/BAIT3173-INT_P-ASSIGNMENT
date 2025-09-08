<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Seat;
use App\Models\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class BookingApiController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $ongoing = Booking::with(['journey', 'journey.train'])
            ->where('UserID', $userId)
            ->whereIn('Status', ['Booked', 'Pending'])
            ->get();

        $past = Booking::with(['journey', 'journey.train'])
            ->where('UserID', $userId)
            ->where('Status', 'Completed')
            ->get();

        $refunded = Booking::with(['journey', 'journey.train'])
            ->where('UserID', $userId)
            ->whereIn('Status', ['Refunded', 'Cancelled'])
            ->get();

        return response()->json([
            'ongoing' => $ongoing,
            'past' => $past,
            'refunded' => $refunded,
        ]);
    }

    public function show($id)
    {
        $userId = Auth::id();

        $booking = Booking::with(['journey', 'journey.train'])
            ->where('BookingID', $id)
            ->where('UserID', $userId)
            ->firstOrFail();

        $tickets = Ticket::with(['journey', 'seat', 'passenger'])
            ->where('BookingID', $id)
            ->get();

        return response()->json([
            'booking' => $booking,
            'tickets' => $tickets,
        ]);
    }

    public function cancel($id)
    {
        $userId = Auth::id();

        $booking = Booking::where('BookingID', $id)
            ->where('UserID', $userId)
            ->where('Status', 'Pending')
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $booking->update(['Status' => 'Cancelled']);

            $tickets = Ticket::where('BookingID', $id)->get();
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
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
