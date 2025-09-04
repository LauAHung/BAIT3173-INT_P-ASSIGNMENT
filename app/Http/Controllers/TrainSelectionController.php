<?php

namespace App\Http\Controllers;

use App\Models\Journey;
use App\Builder\BookingDirector;
use App\Builder\ConcreteBookingBuilder;
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
        session()->forget(['selected_journey', 'selected_journey2', 'passenger_info', 'passengers_count', 'booking_type']);

        $query = Journey::query()->with('train')
            ->orderBy('DepartureTime', 'asc');

        $hasSearchFilters = false;

        if ($request->has('fromlocation') && $request->filled('fromlocation')) {
            $query->where('FromLocation', 'like', '%' . $request->input('fromlocation') . '%');
            $hasSearchFilters = true;
        }
        if ($request->has('tolocation') && $request->filled('tolocation')) {
            $query->where('ToLocation', 'like', '%' . $request->input('tolocation') . '%');
            $hasSearchFilters = true;
        }

        $journeyDate = $request->input('journeydate');
        $returnDate = $request->input('returndate');

        if ($journeyDate || $returnDate) {
            if ($journeyDate && $returnDate) {
                $parsedJourneyDate = \DateTime::createFromFormat('M d, Y', $journeyDate);
                $parsedReturnDate = \DateTime::createFromFormat('M d, Y', $returnDate);
                if ($parsedJourneyDate && $parsedReturnDate) {
                    $query->whereDate('DepartureTime', '>=', $parsedJourneyDate->format('Y-m-d'))
                          ->whereDate('DepartureTime', '<=', $parsedReturnDate->format('Y-m-d'));
                    $hasSearchFilters = true;
                } else {
                    Log::warning('Failed to parse dates', ['journeydate' => $journeyDate, 'returndate' => $returnDate]);
                }
            } elseif ($journeyDate) {
                $parsedDate = \DateTime::createFromFormat('M d, Y', $journeyDate);
                if ($parsedDate) {
                    $query->whereDate('DepartureTime', $parsedDate->format('Y-m-d'));
                    $hasSearchFilters = true;
                } else {
                    Log::warning('Failed to parse journeydate: ', ['input' => $journeyDate]);
                }
            } elseif ($returnDate) {
                $parsedDate = \DateTime::createFromFormat('M d, Y', $returnDate);
                if ($parsedDate) {
                    $query->whereDate('DepartureTime', $parsedDate->format('Y-m-d'));
                    $hasSearchFilters = true;
                } else {
                    Log::warning('Failed to parse returndate: ', ['input' => $returnDate]);
                }
            }
        } else {
            $today = now()->format('Y-m-d');
            $query->whereDate('DepartureTime', '>=', $today);
            $hasSearchFilters = true;
        }

        if ($request->has('train_type') && !empty($request->input('train_type'))) {
            $trainTypes = (array) $request->input('train_type');
            $query->whereHas('train', function ($q) use ($trainTypes) {
                $q->whereIn('TrainService', $trainTypes);
            });
            $hasSearchFilters = true;
        }

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

        if (!$hasSearchFilters) {
            $query->where('Status', 'Scheduled')->take(5);
        }

        $journeys = $query->get();
        $successMessage = session('success');

        return view('TrainSelectionPage', compact('journeys', 'successMessage'));
    }

    public function indexReturn(Request $request)
    {
        if ($request->has('clear_outbound')) {
            session()->forget(['selected_journey', 'selected_journey2', 'passenger_info', 'passengers_count', 'booking_type']);
            return redirect()->route('train.selection', $request->all());
        }

        $query = Journey::query()->with('train')
            ->orderBy('DepartureTime', 'asc');

        $hasSearchFilters = false;

        $selectedJourney = session('selected_journey');
        if ($selectedJourney) {
            $query->where('FromLocation', 'like', '%' . $selectedJourney['to_location'] . '%');
            $query->where('ToLocation', 'like', '%' . $selectedJourney['from_location'] . '%');
            $hasSearchFilters = true;
        }

        if ($request->has('returndate') && $request->input('returndate')) {
            $inputDate = $request->input('returndate');
            $parsedDate = \DateTime::createFromFormat('Y-m-d', $inputDate);
            if ($parsedDate) {
                $query->whereDate('DepartureTime', $parsedDate->format('Y-m-d'));
                $hasSearchFilters = true;
            } else {
                Log::warning('Failed to parse returndate: ', ['input' => $inputDate]);
            }
        } else {
            $today = now()->format('Y-m-d');
            $query->whereDate('DepartureTime', '>=', $today);
            $hasSearchFilters = true;
        }

        if ($request->has('train_type') && !empty($request->input('train_type'))) {
            $trainTypes = (array) $request->input('train_type');
            $query->whereHas('train', function ($q) use ($trainTypes) {
                $q->whereIn('TrainService', $trainTypes);
            });
            $hasSearchFilters = true;
        }

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

        if (!$hasSearchFilters) {
            $query->where('Status', 'Scheduled')->take(5);
        }

        $journeys = $query->get();
        $successMessage = session('success');

        return view('TrainSelectionPage', compact('journeys', 'successMessage'));
    }

    public function showPassengerInfo(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('signin')->with('info', 'Please login first before make a booking.');
        }

        $journeyId = $request->input('journey_id');
        $journeyId2 = $request->input('journey_id2');
        $bookingType = $request->input('booking_type', 'OneWay');
        $passengers = $request->input('passengers', 1);

        $journey = Journey::with('train')->findOrFail($journeyId);

        if ($journey->Train->TrainService === 'ETS' && $passengers > $journey->SeatAvailable) {
            return redirect()->route('train.selection')->with('error', "The selected ETS train has only {$journey->SeatAvailable} seat(s) available, but you are booking for {$passengers} passenger(s). Please select another train or reduce the number of passengers.");
        }

        if ($bookingType == 'Return' && !$journeyId2) {
            session([
                'selected_journey' => [
                    'id' => $journey->JourneyID,
                    'train_id' => $journey->TrainID,
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
                'booking_type' => $bookingType,
            ]);

            return redirect()->route('train.selection.return', $request->all())->with('info', 'Outbound journey selected. Now select your return journey.');
        }

        $journey2 = null;
        if ($bookingType == 'Return' && $journeyId2) {
            $journey2 = Journey::with('train')->findOrFail($journeyId2);

            if ($journey2->FromLocation != $journey->ToLocation || $journey2->ToLocation != $journey->FromLocation) {
                return redirect()->route('train.selection.return')->with('error', 'The return journey must be the reverse route of the outbound journey.');
            }

            if ($journey2->Train->TrainService === 'ETS' && $passengers > $journey2->SeatAvailable) {
                return redirect()->route('train.selection.return')->with('error', "The selected ETS return train has only {$journey2->SeatAvailable} seat(s) available, but you are booking for {$passengers} passenger(s). Please select another train or reduce the number of passengers.");
            }
        }

        $sessionData = [
            'selected_journey' => [
                'id' => $journey->JourneyID,
                'train_id' => $journey->TrainID,
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
            'booking_type' => $bookingType,
        ];

        if ($journey2) {
            $sessionData['selected_journey2'] = [
                'id' => $journey2->JourneyID,
                'train_id' => $journey2->TrainID,
                'train_no' => $journey2->Train->TrainNo,
                'train_service' => $journey2->Train->TrainService,
                'from_location' => $journey2->FromLocation,
                'to_location' => $journey2->ToLocation,
                'departure_time' => $journey2->DepartureTime,
                'arrival_time' => $journey2->ArrivalTime,
                'price' => $journey2->Price,
                'seat_available' => $journey2->SeatAvailable,
            ];
        }

        session($sessionData);

        return view('PassengerInfoPage', compact('passengers', 'journey', 'journey2', 'bookingType'));
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
            'booking_type' => 'required|in:OneWay,Return',
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
        $journeyId2 = $request->input('journey_id2', session('selected_journey2.id'));
        $bookingType = $request->input('booking_type', 'OneWay');

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

            if ($hasPassport && empty($passenger['passport_expiry'])) {
                return back()->withErrors([
                    "passenger.$index.passport_expiry" => 'Passport expiry date is required when Passport is provided.',
                ])->withInput();
            }

            if ($hasMykad && !empty($passenger['passport_expiry'])) {
                return back()->withErrors([
                    "passenger.$index.passport_expiry" => 'Passport expiry date should not be provided when MyKad is entered.',
                ])->withInput();
            }

            $icNo = $passenger['mykad'] ?? null;
            $passportNo = $passenger['passport'] ?? null;

            if ($icNo || $passportNo) {
                $journeyIds = [$journeyId];
                if ($bookingType == 'Return' && $journeyId2) {
                    $journeyIds[] = $journeyId2;
                }

                $existingPassenger = Passenger::where(function ($query) use ($icNo, $passportNo) {
                    if ($icNo) {
                        $query->where('ICno', $icNo);
                    }
                    if ($passportNo) {
                        $query->where('Passportno', $passportNo);
                    }
                })
                ->whereHas('tickets', function ($query) use ($journeyIds) {
                    $query->whereIn('JourneyID', $journeyIds)
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
            'journey_id2' => $journeyId2,
            'booking_type' => $bookingType,
            'passengers_count' => $passengersCount,
            'passenger_info' => $passengers,
        ]);

        session(['passengers_count' => $passengersCount, 'booking_type' => $bookingType]);

        return redirect()->route('selectseat', [
            'passengers' => $passengersCount,
            'journey_id' => $journeyId,
            'journey_id2' => $journeyId2,
            'booking_type' => $bookingType,
        ]);
    }

    public function showSelectSeat(Request $request)
    {
        $journey = session('selected_journey');
        $journey2 = session('selected_journey2');
        $passengers = session('passenger_info');
        $passengersCount = session('passengers_count', 1);
        $bookingType = session('booking_type', 'OneWay');

        return view('SelectSeatPage', compact('journey', 'journey2', 'passengers', 'passengersCount', 'bookingType'));
    }

    public function storeBooking(Request $request)
    {
        $journey = session('selected_journey');
        $journey2 = session('selected_journey2');
        $passengers = session('passenger_info');
        $bookingType = session('booking_type', 'OneWay');
        $selectedSeatsInput = $request->input('selected_seats', []);
        $selectedSeatsInput2 = $request->input('selected_seats2', []);

        $selectedSeats = array_reduce($selectedSeatsInput, function ($carry, $item) {
            return array_merge($carry, is_array($item) ? $item : [$item]);
        }, []);

        $selectedSeats2 = array_reduce($selectedSeatsInput2, function ($carry, $item) {
            return array_merge($carry, is_array($item) ? $item : [$item]);
        }, []);

        try {
            DB::beginTransaction();

            $builder = new ConcreteBookingBuilder();
            $director = new BookingDirector();
            $director->build($builder, $journey, $passengers, $selectedSeats, $journey2 ?? [], $selectedSeats2 ?? []);
            $booking = $builder->getBooking();

            DB::commit();

            session()->forget(['selected_journey', 'selected_journey2', 'passenger_info', 'passengers_count', 'booking_type']);

            return redirect()->route('train.selection')->with('success', 'Booking created successfully. Proceed to payment.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage());
            return redirect()->route('selectseat', [
                'journey_id' => $journey['id'] ?? 0,
                'journey_id2' => $journey2['id'] ?? null,
                'passengers' => count($passengers),
                'booking_type' => $bookingType,
            ])->with('error', 'Failed to create booking: ' . $e->getMessage());
        }
    }
}