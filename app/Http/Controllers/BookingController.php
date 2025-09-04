<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ticket;
use App\Models\Seat;
use App\Models\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('signin')->with('info', 'Please login to view your bookings.');
        }

        $userId = (string) Auth::id();

        // Fetch bookings with related journey and train data for the authenticated user
      $ongoingBookings = Booking::with(['journey', 'journey.train'])
    ->where('UserID', (string) Auth::id())
    ->whereIn('Status', ['Booked', 'Pending']) // ignore Paid status
    ->get()
    ->map(function ($booking) {
        // Buttons logic
        $booking->showProceedPayment = $booking->Status === 'Pending' && !$booking->PaymentType;
        $booking->showCancel = $booking->Status === 'Pending' && !$booking->PaymentType;

        $booking->showViewQR = $booking->Status === 'Booked';
        $booking->showRefund = $booking->Status === 'Booked';

        return $booking;
            });

        Log::info('Ongoing Bookings:', ['bookings' => $ongoingBookings->toArray()]);

        $pastBookings = Booking::with(['journey', 'journey.train'])
            ->where('UserID', $userId)
            ->whereIn('Status', ['Completed', 'Cancelled'])
            ->get()
            ->map(function ($booking) {
                $booking->showViewQR = $booking->Status === 'Completed';
                $booking->showRateTrip = $booking->Status === 'Completed';
                return $booking;
            });
            
        $refundedBookings = Booking::with(['journey', 'journey.train'])
        ->where('UserID', $userId)
        ->where('Status', 'Refunded')
        ->get()
        ->map(function ($booking) {
            return $booking;
        });

    Log::info('Refunded Bookings:', ['bookings' => $refundedBookings->toArray()]);
        // Ensure variables are always passed, even if empty
        return view('BookingPage', compact('ongoingBookings', 'pastBookings','refundedBookings'));

    }

    public function show($bookingId)
    {
        $userId = (string) Auth::id();

        // Fetch the booking with related journey and train data
        $booking = Booking::with(['journey', 'journey.train'])
            ->where('BookingID', $bookingId)
            ->where('UserID', $userId)
            ->firstOrFail();

        // Fetch all tickets associated with this booking
        $tickets = Ticket::with(['journey', 'seat', 'passenger'])
            ->where('BookingID', $bookingId)
            ->get();

        return view('BookingDetailPage', compact('booking', 'tickets'));
    }

    public function cancel($bookingId)
    {
        $userId = (string) Auth::id();

        // Fetch the booking and ensure it belongs to the user and is cancellable
        $booking = Booking::where('BookingID', $bookingId)
            ->where('UserID', $userId)
            ->where('Status', 'Pending')
            ->firstOrFail();

        try {
            DB::beginTransaction();

            // Update booking status to Cancelled
            $booking->update(['Status' => 'Cancelled']);

            // Fetch tickets associated with the booking
            $tickets = Ticket::where('BookingID', $bookingId)->get();

            // Update ticket statuses and free up seats
            foreach ($tickets as $ticket) {
                $ticket->update(['Status' => 'Cancelled']);
                Seat::where('SeatID', $ticket->SeatID)
                    ->update(['is_available' => 'Y', 'status' => 'Cancelled']);
            }

            // Update SeatAvailable in Journeys
            $journey = Journey::find($booking->JourneyID);
            if ($journey->train->TrainService === 'ETS') {
                $journey->increment('SeatAvailable', $booking->TicketNo);
            }
            

            DB::commit();

            Log::info('Booking cancelled successfully', [
                'booking_id' => $bookingId,
                'user_id' => $userId
            ]);

            return redirect()->route('booking')->with('success', 'Booking cancelled successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking cancellation failed: ' . $e->getMessage(), [
                'booking_id' => $bookingId,
                'user_id' => $userId
            ]);
            return redirect()->route('booking')->with('error', 'Failed to cancel booking: ' . $e->getMessage());
        }
    }
}