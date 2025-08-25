<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class BookingStepMiddleware
{
    public function handle(Request $request, Closure $next, $step)
    {
        if ($step === 'passengerinfo' && !session('selected_journey')) {
            return Redirect::route('train.selection')->with('error', 'Please select a train journey first.');
        }

        if ($step === 'selectseat' && !session('selected_journey')) {
            return Redirect::route('train.selection')->with('error', 'Please select a train journey first.');
        }

        if ($step === 'selectseat' && !session('passenger_info')) {
            return Redirect::route('passengerinfo', [
                'journey_id' => session('selected_journey.id'),
                'passengers' => session('passengers_count', 1),
            ])->with('error', 'Please enter passenger details before selecting seats.');
        }

        return $next($request);
    }
}