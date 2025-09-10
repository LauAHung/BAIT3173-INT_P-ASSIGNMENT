<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    // Show rating form
    public function create($bookingId)
    {
        $booking = Booking::with('Journey')->find($bookingId);

        if (!$booking) {
            return redirect()->route('booking')->with('error', 'Booking not found.');
        }

        return view('RatingSectionPage', compact('booking'));
    }

    // Store rating
    public function store(Request $request, $bookingId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'required|string|max:1000',
        ]);

        // Generate feedback ID (F followed by 5 digits)
        $lastFeedback = Feedback::orderBy('feeback_id', 'desc')->first();
        $lastId = $lastFeedback ? (int)substr($lastFeedback->feeback_id, 1) : 0;
        $newId = 'F' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        Feedback::create([
            'feeback_id'   => $newId,
            'BookingID'    => $bookingId,  // ✅ FIXED
            'feedback_time'=> Carbon::today()->toDateString(),
            'rating_value' => $request->rating,
            'feedback_text'=> $request->feedback,
        ]);

        return redirect()->route('booking')->with('success', 'Thank you for your feedback!');
    }

    // Display feedback
    public function viewFeedback()
    {
        $user = Auth::user();

        $myFeedback = Feedback::select('Feedback.*', 'users.first_name', 'users.last_name')
            ->join('Bookings', 'Feedback.BookingID', '=', 'Bookings.BookingID')  // ✅ FIXED
            ->join('users', 'Bookings.UserID', '=', 'users.user_id')             // ✅ FIXED
            ->where('users.user_id', $user->user_id)
            ->get();

        $otherFeedback = Feedback::select('Feedback.*', 'users.first_name', 'users.last_name')
            ->join('Bookings', 'Feedback.BookingID', '=', 'Bookings.BookingID')  // ✅ FIXED
            ->join('users', 'Bookings.UserID', '=', 'users.user_id')             // ✅ FIXED
            ->where('users.user_id', '!=', $user->user_id)
            ->get();

        return view('ViewFeedbackPage', compact('myFeedback', 'otherFeedback'));
    }
}
