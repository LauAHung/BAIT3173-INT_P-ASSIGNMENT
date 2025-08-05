<?php

namespace App\Http\Controllers;

use App\Models\Journey;
use Illuminate\Http\Request;

class TrainSelectionController extends Controller
{
    public function index(Request $request)
    {

        $query = Journey::query()->with('train');

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
        if ($request->has('journeydate') && $request->filled('journeydate')) {
            $query->whereDate('DepartureTime', $request->input('journeydate'));
            $hasSearchFilters = true;
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
        $passengers = $request->input('passengers', 1); // Default to 1 if not set
        return view('PassengerInfoPage', compact('passengers'));
    }
}