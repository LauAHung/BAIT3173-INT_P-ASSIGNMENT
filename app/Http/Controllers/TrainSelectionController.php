<?php

namespace App\Http\Controllers;

use App\Models\Journey;
use App\Builder\BookingDirector;
use App\Builder\ConcreteBookingBuilder;
use App\Models\Passenger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainSelectionController extends Controller
{
    public function index(Request $request)
    {
        // Initialize the base query
        $query = Journey::query()->with('train')
            ->orderBy('DepartureTime', 'asc');

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
        } else {
            // Use today's date if journeydate is empty
            $today = now()->format('Y-m-d');
            $query->whereDate('DepartureTime', $today);
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
                    }
                }
            });
            $hasSearchFilters = true;
        }

        // Always apply passengers filter (default to 1 if not provided)
        $passengers = $request->filled('passengers') ? (int) $request->input('passengers') : 1;
        $query->where('SeatAvailable', '>=', $passengers);

        // Default: Fetch the first 5 scheduled journeys if no filters are applied
        if (!$hasSearchFilters) {
            $query->where('Status', 'Scheduled')->take(5);
        }

        // Execute the query
        $journeys = $query->get();

        // Get the success message from the session
        $successMessage = session('success');

        return view('TrainSelectionPage', compact('journeys', 'successMessage'));
    }

    public function showPassengerInfo(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('signin')->with('info', 'Please login first before make a booking.');
        }
        // Get the journey ID from the request
        $journeyId = $request->input('journey_id');
        $passengers = $request->input('passengers', 1);

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

        return view('PassengerInfoPage', compact('passengers', 'journey'));
    }

    public function storePassengerInfo(Request $request)
    {
        $validated = $request->validate([
            'passenger.*.name' => ['required', 'regex:/^[a-zA-Z\s\'-]{2,}$/', 'max:255'],
            'passenger.*.contact_no' => ['required', 'regex:/^01[0-9]-[0-9]{7,8}$/', 'max:20'],
            'passenger.*.gender' => 'required|in:male,female',
            'passenger.*.ticket_type' => 'required|in:Dewasa/Adult,Kanak-kanak/Child,OKU',
            'passenger.*.mykad' => ['nullable', 'regex:/^\d{12}$/', 'max:20', 'required_without:passenger.*.passport'],
            'passenger.*.passport' => ['nullable', 'regex:/^[a-zA-Z0-9]{6,12}$/', 'max:20', 'required_without:passenger.*.mykad'],
            'passenger.*.passport_expiry' => ['nullable', 'date'],
        ], [
            'passenger.*.name.regex' => 'Invalid name provided',
            'passenger.*.mykad.regex' => 'Invalid MyKad format: Must be exactly 12 digits.',
            'passenger.*.passport.regex' => 'Invalid Passport format: Must be 6-12 alphanumeric characters.',
            'passenger.*.contact_no.regex' => 'Invalid contact number format: Use format 01x-xxxxxxxx.',
            'passenger.*.mykad.required_without' => 'Either MyKad or Passport number is required.',
            'passenger.*.passport.required_without' => 'Either MyKad or Passport number is required.',
        ]);

        $passengers = $request->input('passenger', []);
        $journeyId = $request->input('journey_id', session('selected_journey.id'));

        // Custom validation: Ensure either MyKad or Passport, but not both
        foreach ($passengers as $index => $passenger) {
            $hasMykad = !empty($passenger['mykad']);
            $hasPassport = !empty($passenger['passport']);

            if (!$hasMykad && !$hasPassport) {
                return back()->withErrors([
                    "passenger.$index.mykad" => 'Either MyKad or Passport is required.',
                    "passenger.$index.passport" => 'Either MyKad or Passport is required.',
                ])->withInput();
            }

            if ($hasMykad && $hasPassport) {
                return back()->withErrors([
                    "passenger.$index.mykad" => 'Please enter either MyKad or Passport, not both.',
                    "passenger.$index.passport" => 'Please enter either MyKad or Passport, not both.',
                ])->withInput();
            }

            // Validate Passport expiry if Passport is provided
            if ($hasPassport && empty($passenger['passport_expiry'])) {
                return back()->withErrors([
                    "passenger.$index.passport_expiry" => 'Passport expiry date is required when Passport is provided.',
                ])->withInput();
            }

            // New validation: If MyKad is provided, Passport expiry should not be provided
            if ($hasMykad && !empty($passenger['passport_expiry'])) {
                return back()->withErrors([
                    "passenger.$index.passport_expiry" => 'Passport expiry date should not be provided when MyKad is entered.',
                ])->withInput();
            }

            // Check for duplicate ICno or Passportno on the same journey for Booked or Pending bookings
            $icNo = $passenger['mykad'] ?? null;
            $passportNo = $passenger['passport'] ?? null;

            if ($icNo || $passportNo) {
                $existingPassenger = Passenger::where(function ($query) use ($icNo, $passportNo) {
                    if ($icNo) {
                        $query->where('ICno', $icNo);
                    }
                    if ($passportNo) {
                        $query->where('Passportno', $passportNo);
                    }
                })
                ->whereHas('tickets', function ($query) use ($journeyId) {
                    $query->where('JourneyID', $journeyId)
                          ->whereHas('booking', function ($query) {
                              $query->whereIn('Status', ['Booked', 'Pending']);
                          });
                })
                ->first();

                if ($existingPassenger) {
                    return back()->withErrors([
                        "passenger.$index.mykad" => 'A passenger with this MyKad or Passport is already booked on this journey.',
                        "passenger.$index.passport" => 'A passenger with this MyKad or Passport is already booked on this journey.',
                    ])->withInput();
                }
            }
        }

        session(['passenger_info' => $passengers]);
        $passengersCount = count($passengers);

        Log::info('Session data set for storePassengerInfo', [
            'journey_id' => $journeyId,
            'passengers_count' => $passengersCount,
            'passenger_info' => $passengers,
        ]);

        session(['passengers_count' => $passengersCount]);

        return redirect()->route('selectseat', [
            'passengers' => $passengersCount,
            'journey_id' => $journeyId,
        ]);
    }

    public function showSelectSeat(Request $request)
    {
        // Retrieve journey and passenger info from session
        $journey = session('selected_journey');
        $passengers = session('passenger_info');
        $passengersCount = session('passengers_count', 1);

        return view('SelectSeatPage', compact('journey', 'passengers', 'passengersCount'));
    }

    public function storeBooking(Request $request)
    {
        $journey = session('selected_journey');
        $passengers = session('passenger_info');
        $selectedSeatsInput = $request->input('selected_seats', []);

        // Flatten seats (from your original code)
        $selectedSeats = array_reduce($selectedSeatsInput, function ($carry, $item) {
            return array_merge($carry, is_array($item) ? $item : [$item]);
        }, []);

        try {
            DB::beginTransaction();

            // Use Builder via Director
            $builder = new ConcreteBookingBuilder();
            $director = new BookingDirector();
            $director->build($builder, $journey, $passengers, $selectedSeats);

            $booking = $builder->getBooking();  // Get the final product

            DB::commit();

            session()->forget(['selected_journey', 'passenger_info', 'passengers_count']);

            return redirect()->route('train.selection')->with('success', 'Booking created successfully. Proceed to payment.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage());
            return redirect()->route('selectseat', [
                'journey_id' => $journey['id'] ?? 0,
                'passengers' => count($passengers),
            ])->with('error', 'Failed to create booking, please try again later!');
        }
    }
}