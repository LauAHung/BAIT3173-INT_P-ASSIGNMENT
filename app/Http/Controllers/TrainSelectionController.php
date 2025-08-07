<?php

namespace App\Http\Controllers;

use App\Models\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrainSelectionController extends Controller
{
    public function index(Request $request)
    {

        $query = Journey::query()->with('train')
            ->orderBy('DepartureTime', 'asc');
        $journeys = $query->get();

        // Track if any meaningful filters (excluding passengers) are applied
        $hasSearchFilters = false;

        // Apply search filters
        if ($request->has('fromlocation') && $request->filled('fromlocation')) {
            $query->where('FromLocation', 'like', '%' . $request->input('fromlocation') . '%');
            $hasSearchFilters = true;
        }
        if ($request->has('tolocation') && $request->filled('tolocation')) {
            $query->where('ToLocation', 'like', '%' . $request->input('tolocation') . '%');
            $hasSearchFilters = true;
        }
        if ($request->has('journeydate') && $request->input('journeydate')) {
            $inputDate = $request->input('journeydate');
            $parsedDate = \DateTime::createFromFormat('M j, Y', $inputDate);
            if ($parsedDate) {
                $query->whereDate('DepartureTime', $parsedDate->format('Y-m-d'));
                $hasSearchFilters = true;
            } else {
                Log::warning('Failed to parse journeydate: ', ['input' => $inputDate]);
            }
        }

        // Apply filter for train service type
        if ($request->has('train_type') && !empty($request->input('train_type'))) {
            $trainTypes = (array) $request->input('train_type');
            $query->whereHas('train', function ($q) use ($trainTypes) {
                $q->whereIn('TrainService', $trainTypes);
            });
            $hasSearchFilters = true;
        }

        // Apply filter for departure time
        if ($request->has('departure_time') && !empty($request->input('departure_time'))) {
            $timeRanges = (array) $request->input('departure_time');
            $validRanges = ['early', 'morning', 'afternoon', 'night'];
            $query->where(function ($q) use ($timeRanges, $validRanges) {
                foreach ($timeRanges as $timeRange) {
                    if (in_array($timeRange, $validRanges)) {
                        switch ($timeRange) {
                            case 'early':
                                $q->orWhereRaw('TIME(DepartureTime) BETWEEN ? AND ?', ['00:00:00', '06:00:00']);
                                break;
                            case 'morning':
                                $q->orWhereRaw('TIME(DepartureTime) BETWEEN ? AND ?', ['06:00:00', '12:00:00']);
                                break;
                            case 'afternoon':
                                $q->orWhereRaw('TIME(DepartureTime) BETWEEN ? AND ?', ['12:00:00', '18:00:00']);
                                break;
                            case 'night':
                                $q->orWhereRaw('TIME(DepartureTime) BETWEEN ? AND ?', ['18:00:00', '23:59:59']);
                                break;
                        }
                    } else {
                    }
                }
            });
            $hasSearchFilters = true;
        }

        // Always apply passengers filter (default to 1 if not provided)
        $passengers = $request->filled('passengers') ? $request->input('passengers') : 1;
        $query->where('SeatAvailable', '>=', $passengers);

        // Default: Fetch the first 3 scheduled journeys if no other filters are applied
        if (!$hasSearchFilters) {
            $query->where('Status', 'Scheduled')->take(3);
        }

        $journeys = $query->get();

        return view('TrainSelectionPage', compact('journeys'));
    }

    public function showPassengerInfo(Request $request)
    {
        // Get the journey ID from the request (e.g., passed as a query parameter or route parameter)
        $journeyId = $request->input('journey_id'); // Assume journey_id is passed in the URL or form
        $passengers = $request->input('passengers', 1); // Default to 1 if not set

        // Fetch the journey details
        $journey = Journey::with('train')->findOrFail($journeyId);

        // Store journey details in session
        session([
            'selected_journey' => [
                'id' => $journey->JourneyID,
                'train_no' => $journey->Train->TrainNo,
                'train_service' => $journey->Train->TrainService,
                'from_location' => $journey->FromLocation,
                'to_location' => $journey->ToLocation,
                'departure_time' => $journey->DepartureTime,
                'arrival_time' => $journey->ArrivalTime,
                'price' => $journey->Price,
                'seat_available' => $journey->SeatAvailable,
            ],
            'passengers_count' => $passengers,
        ]);

        return view('PassengerInfoPage', compact('passengers'));
    }

    public function storePassengerInfo(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'passenger.*.name' => 'required|string|max:255',
            'passenger.*.contact_no' => 'required|string|max:20',
            'passenger.*.gender' => 'required|in:male,female',
            'passenger.*.ticket_type' => 'required|in:Dewasa/Adult,Kanak-kanak/Child,OKU',
            'passenger.*.mykad' => 'nullable|string|max:20',
            'passenger.*.passport' => 'nullable|string|max:20',
            'passenger.*.passport_expiry' => 'nullable|date',
        ], [
            'passenger.*.mykad.required' => 'MyKad or Passport is required for each passenger.',
            'passenger.*.passport.required' => 'MyKad or Passport is required for each passenger.',
        ]);

        // Custom validation to ensure either MyKad or Passport is provided
        foreach ($request->input('passenger', []) as $index => $passenger) {
            if (empty($passenger['mykad']) && empty($passenger['passport'])) {
                return back()->withErrors([
                    "passenger.$index.mykad" => 'Either MyKad or Passport is required.',
                    "passenger.$index.passport" => 'Either MyKad or Passport is required.',
                ])->withInput();
            }
        }

        // Store passenger info in session
        session(['passenger_info' => $request->input('passenger')]);

        // Retrieve passengers count and journey_id from session or request
        $passengers = session('passengers_count', $request->input('passengers', 1));
        $journeyId = $request->input('journey_id', session('selected_journey.id'));

        // Redirect to seat selection page
        return redirect()->route('selectseat', [
            'passengers' => $passengers,
            'journey_id' => $journeyId,
        ]);
    }
    public function showSelectSeat(Request $request)
    {
        // Retrieve journey and passenger info from session
        $journey = session('selected_journey');
        $passengers = session('passenger_info');
        $passengersCount = session('passengers_count', 1);

        // If session data is missing, redirect back to TrainSelectionPage
        if (!$journey || !$passengers) {
            return redirect()->route('train.selection')->with('error', 'Please select a journey and enter passenger details.');
        }

        return view('SelectSeatPage', compact('journey', 'passengers', 'passengersCount'));
    }
}