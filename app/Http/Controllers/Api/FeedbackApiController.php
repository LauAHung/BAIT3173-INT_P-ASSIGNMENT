<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\Journey;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeedbackApiController extends Controller
{
    // ---------------- GET BOOKING INFO ----------------
    public function getBookingInfoForRating($bookingId)
{
    $booking = Booking::where('BookingID', $bookingId)->first();

    if (!$booking) {
        return response()->json(['error' => 'Booking not found'], 404);
    }

    $journey = Journey::with('train')->find($booking->JourneyID);

    return response()->json([
        'booking' => [
            'booking_id' => $booking->BookingID,
            'price'      => $booking->Price,
        ],
        'journey' => $journey ? [
            'train_service'  => $journey->train->TrainService ?? 'default',
            'train_no'       => $journey->train->TrainNo ?? 'Unknown',
            'from_location'  => $journey->FromLocation,
            'to_location'    => $journey->ToLocation,
            'departure_time' => $journey->DepartureTime,
            'arrival_time'   => $journey->ArrivalTime,
        ] : null,
    ]);
}


    // ---------------- STORE FEEDBACK ----------------
    public function store(Request $request, $bookingId)
    {
        $validated = $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'feedback' => 'required|string|max:1000',
            'user_id'  => 'required|integer|exists:users,user_id',
        ]);

        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json(['error' => 'Booking not found.'], 404);
        }

        // Generate feedback ID (F00001 format)
        $lastFeedback = Feedback::orderBy('feeback_id', 'desc')->first();
        $lastId = $lastFeedback ? (int) substr($lastFeedback->feeback_id, 1) : 0;
        $newId = 'F' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        $feedback = Feedback::create([
            'feeback_id'    => $newId,
            'BookingID'     => $bookingId,
            'feedback_time' => Carbon::today()->toDateString(),
            'rating_value'  => $validated['rating'],
            'feedback_text' => $validated['feedback'],
        ]);

        return response()->json([
            'message'  => 'Feedback submitted successfully!',
            'feedback' => $feedback,
        ], 201);
    }

    // ---------------- GET FEEDBACK FOR BOOKING ----------------
    public function getBookingFeedback($bookingId)
    {
        $feedback = Feedback::where('BookingID', $bookingId)->get();

        if ($feedback->isEmpty()) {
            return response()->json(['message' => 'No feedback found for this booking.'], 200);
        }

        return response()->json(['feedback' => $feedback], 200);
    }

    // ---------------- GET FEEDBACK GROUPED (MY vs OTHERS) ----------------
    public function getUserFeedback($userId)
    {
    $myFeedback = Feedback::select('Feedback.*', 'users.first_name', 'users.last_name',  'Journeys.JourneyID',
            'Journeys.FromLocation', 'Journeys.ToLocation', 'Journeys.DepartureTime', 'Journeys.ArrivalTime',
            'trains.TrainService', 'trains.TrainNo')
            ->join('Bookings', 'Feedback.BookingID', '=', 'Bookings.BookingID')
            ->join('users', 'Bookings.UserID', '=', 'users.user_id')
            ->join('Journeys', 'Bookings.JourneyID', '=', 'Journeys.JourneyID')
            ->leftJoin('trains', 'Journeys.TrainID', '=', 'trains.TrainID')
            ->where('users.user_id', $userId)
            ->get();

        $otherFeedback = Feedback::select('Feedback.*', 'users.first_name', 'users.last_name',  'Journeys.JourneyID',
            'Journeys.FromLocation', 'Journeys.ToLocation', 'Journeys.DepartureTime', 'Journeys.ArrivalTime',
            'trains.TrainService', 'trains.TrainNo')
            ->join('Bookings', 'Feedback.BookingID', '=', 'Bookings.BookingID')
            ->join('users', 'Bookings.UserID', '=', 'users.user_id')
            ->join('Journeys', 'Bookings.JourneyID', '=', 'Journeys.JourneyID')
            ->leftJoin('trains', 'Journeys.TrainID', '=', 'trains.TrainID')
            ->where('users.user_id', '!=', $userId)
            ->get();

        return response()->json([
            'myFeedback'     => $myFeedback,
            'otherFeedback'  => $otherFeedback,
        ], 200);
    }
}
