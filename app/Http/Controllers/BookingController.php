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

        return view('BookingPage');
    }

    public function show($bookingId)
    {
        if (!Auth::check()) {
            return redirect()->route('signin')->with('info', 'Please login to view your bookings.');
        }

        return view('BookingDetailPage',['bookingId' => $bookingId]);
    }
}