<?php

namespace App\Http\Controllers;

use App\Models\Journey;
use App\Models\Booking;
use App\Models\Passenger;
use App\Models\Ticket;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
            $query->where('Status', 'Scheduled')->take(5);
        }

        $journeys = $query->get();

        // Get the success message from the session and pass it to the view
        $successMessage = session('success');

        return view('TrainSelectionPage', compact('journeys', 'successMessage'));
    }

    public function showPassengerInfo(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('signin')->with('info', 'Please login first before make a booking.');
        }
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
        $validated = $request->validate([
            'passenger.*.name' => 'required|string|max:255',
            'passenger.*.contact_no' => 'required|string|max:20',
            'passenger.*.gender' => 'required|in:male,female',
            'passenger.*.ticket_type' => 'required|in:Dewasa/Adult,Kanak-kanak/Child,OKU',
            'passenger.*.mykad' => 'nullable|string|max:20',
            'passenger.*.passport' => 'nullable|string|max:20',
            'passenger.*.passport_expiry' => 'nullable|date',
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

            // Check for duplicate ICno or Passportno on the same journey
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
                    $query->where('JourneyID', $journeyId);
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
        $passengersCount = (int) session('passengers_count', 1);
        $selectedSeatsInput = $request->input('selected_seats', []);

        Log::info('Raw selected_seats input:', [$selectedSeatsInput]);
        $selectedSeats = array_reduce($selectedSeatsInput, function ($carry, $item) {
            return array_merge($carry, is_array($item) ? $item : [$item]);
        }, []);

        Log::info('Flattened selected_seats:', [$selectedSeats]);
        Log::info('Count of selectedSeats:', [count($selectedSeats)]);
        Log::info('PassengersCount:', [$passengersCount]);
        Log::info('Journey exists:', [$journey ? 'yes' : 'no']);
        Log::info('Passengers exists:', [$passengers ? 'yes' : 'no']);

        // Re-index the passengers array to start from 0
        Log::info('Passengers array before reindex:', [$passengers]);
        $passengers = array_values($passengers);
        Log::info('Passengers array after reindex:', [$passengers]);

        if (!$journey || !$passengers || count($selectedSeats) !== $passengersCount) {
            return redirect()->route('selectseat', ['journey_id' => $journey['id'] ?? 0, 'passengers' => $passengersCount])
                ->with('error', 'Invalid booking data or incorrect number of seats selected.');
        }

        $availableSeats = Seat::where('JourneyID', $journey['id'])
            ->whereIn('SeatNo', $selectedSeats)
            ->where('is_available', 'Y')
            ->count();

        if ($availableSeats !== $passengersCount) {
            return redirect()->route('selectseat', ['journey_id' => $journey['id'] ?? 0, 'passengers' => $passengersCount])
                ->with('error', 'Some selected seats are no longer available.');
        }

        try {
            DB::beginTransaction();

            $totalPrice = $passengersCount * ($journey['price'] ?? Journey::find($journey['id'])->Price);

            $booking = Booking::create([
                'BookingID' => 'BK' . str_pad(mt_rand(5, 99999), 5, '0', STR_PAD_LEFT),
                'UserID' => (string) Auth::id(),
                'TrainID' => $journey['train_id'] ?? Journey::find($journey['id'])->TrainID,
                'JourneyID' => $journey['id'],
                'BookingType' => 'OneWay',
                'PaymentType' => null,
                'TicketNo' => $passengersCount,
                'Price' => $totalPrice,
                'Status' => 'Pending',
                'Created_at' => now()->format('d-m-Y'),
            ]);

            foreach ($passengers as $index => $passenger) {
                $passengerData = [
                    'PassengerID' => 'PS' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                    'BookingID' => $booking->BookingID,
                    'Name' => $passenger['name'],
                    'Gender' => $passenger['gender'],
                    'ICno' => $passenger['mykad'] ?? null,
                    'Passportno' => $passenger['passport'] ?? null,
                    'PassportExpiryDate' => $passenger['passport_expiry'] ?? null,
                    'TicketType' => $passenger['ticket_type'],
                    'Created_at' => now()->format('d-m-Y'),
                ];
                Log::info('Passenger data before create:', [$passengerData]);  // Debug log
                $passengerModel = Passenger::create($passengerData);

                if (!isset($selectedSeats[$index])) {
                    throw new \Exception("Seat index $index not found in selectedSeats: " . json_encode($selectedSeats));
                }
                $seatNo = $selectedSeats[$index];
                $seat = Seat::where('JourneyID', $journey['id'])->where('SeatNo', $seatNo)->first();

                Ticket::create([
                    'TicketID' => 'TK' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                    'BookingID' => $booking->BookingID,
                    'JourneyID' => $journey['id'],
                    'SeatID' => $seat->SeatID,
                    'PassengerID' => $passengerModel->PassengerID,
                    'Status' => 'Pending',
                    'Created_at' => now()->format('d-m-Y'),
                ]);

                $seat->update(['is_available' => 'N']);
            }

            $journeyModel = Journey::find($journey['id']);
            $journeyModel->decrement('SeatAvailable', $passengersCount);

            DB::commit();

            session()->forget(['selected_journey','passenger_info', 'passengers_count']);

            return redirect()->route('train.selection')->with('success', 'Booking created successfully. Proceed to payment.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage() . ' | Full trace: ' . $e->getTraceAsString());
            return redirect()->route('selectseat', ['journey_id' => $journey['id'] ?? 0, 'passengers' => $passengersCount])
                ->with('error', 'Failed to create booking: ' . $e->getMessage());
        }
    }
}