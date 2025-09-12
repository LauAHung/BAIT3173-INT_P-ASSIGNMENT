<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journey;
use App\Models\Passenger;
use App\Builder\BookingDirector;
use App\Builder\ConcreteBookingBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;

class TrainApiController extends Controller
{
    private function authorizeJourneyAccess($journeyId, $context = 'view')
    {
        $query = Journey::where('JourneyID', $journeyId)
            ->where('Status', 'Scheduled')
            ->where('DepartureTime', '>', now());

        $journey = $query->first();
         // Log for debug use
        if (!$journey) {
            Log::warning('Journey access denied', [
                'journey_id' => $journeyId,
                'reason' => 'Journey is not scheduled or is in the past'
            ]);
            throw new Exception('Only scheduled journeys with future departure times can be accessed.', 403);
        }

        Log::info('Journey access granted', [
            'journey_id' => $journeyId,
            'status' => $journey->Status,
            'departure_time' => $journey->DepartureTime
        ]);
        return $journey;
    }
    public function getJourney(Request $request)
    {
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
        return response()->json(['journeys' => $journeys], 200);
    }

    public function getJourneyReturn(Request $request)
    {
        $query = Journey::query()->with('train')
            ->orderBy('DepartureTime', 'asc');

        $hasSearchFilters = false;

        $selectedJourney = $request->input('selected_journey');
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
        return response()->json(['journeys' => $journeys], 200);
    }

    public function showPassengerInfo(Request $request)
    {
        $journeyId = $request->input('journey_id');
        $journeyId2 = $request->input('journey_id2');
        $bookingType = $request->input('booking_type', 'OneWay');
        $passengers = $request->input('passengers', 1);

        // for authorize journey access use
        try {
            $journey = $this->authorizeJourneyAccess($journeyId);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }

        if ($journey->Train->TrainService === 'ETS' && $passengers > $journey->SeatAvailable) {
            return response()->json([
                'error' => "The selected ETS train has only {$journey->SeatAvailable} seat(s) available, but you are booking for {$passengers} passenger(s). Please select another train or reduce the number of passengers."
            ], 400);
        }

        $journeyData = [
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
        ];

        $response = [
            'journey' => $journeyData,
            'passengers' => $passengers,
            'booking_type' => $bookingType,
        ];

        if ($bookingType == 'Return' && $journeyId2) {
            $journey2 = Journey::with('train')->find($journeyId2);
            if (!$journey2) {
                return response()->json(['error' => 'Return journey not found.'], 404);
            }

            if ($journey2->FromLocation != $journey->ToLocation || $journey2->ToLocation != $journey->FromLocation) {
                return response()->json(['error' => 'The return journey must be the reverse route of the outbound journey.'], 400);
            }

            if ($journey2->Train->TrainService === 'ETS' && $passengers > $journey2->SeatAvailable) {
                return response()->json([
                    'error' => "The selected ETS return train has only {$journey2->SeatAvailable} seat(s) available, but you are booking for {$passengers} passenger(s). Please select another train or reduce the number of passengers."
                ], 400);
            }

            $response['journey2'] = [
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

        return response()->json($response, 200);
    }

    public function storePassengerInfo(Request $request)
    {
        $passengers = $request->input('passenger', []);
        $journeyId = $request->input('journey_id');
        $journeyId2 = $request->input('journey_id2');
        $bookingType = $request->input('booking_type', 'OneWay');

        foreach ($passengers as $index => $passenger) {
            $hasMykad = !empty($passenger['mykad']);
            $hasPassport = !empty($passenger['passport']);

            if (!$hasMykad && !$hasPassport) {
                return response()->json([
                    'errors' => [
                        "passenger.$index.mykad" => 'Either MyKad or Passport is required.',
                        "passenger.$index.passport" => 'Either MyKad or Passport is required.',
                    ]
                ], 422);
            }

            if ($hasMykad && $hasPassport) {
                return response()->json([
                    'errors' => [
                        "passenger.$index.mykad" => 'Please enter either MyKad or Passport, not both.',
                        "passenger.$index.passport" => 'Please enter either MyKad or Passport, not both.',
                    ]
                ], 422);
            }

            if ($hasPassport && empty($passenger['passport_expiry'])) {
                return response()->json([
                    'errors' => [
                        "passenger.$index.passport_expiry" => 'Passport expiry date is required when Passport is provided.',
                    ]
                ], 422);
            }

            if ($hasMykad && !empty($passenger['passport_expiry'])) {
                return response()->json([
                    'errors' => [
                        "passenger.$index.passport_expiry" => 'Passport expiry date should not be provided when MyKad is entered.',
                    ]
                ], 422);
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
                    return response()->json([
                        'errors' => [
                            "passenger.$index.mykad" => 'A passenger with this MyKad or Passport is already booked on this journey.',
                            "passenger.$index.passport" => 'A passenger with this MyKad or Passport is already booked on this journey.',
                        ]
                    ], 422);
                }
            }
        }

        return response()->json([
            'passenger_info' => $passengers,
            'passengers_count' => count($passengers),
            'booking_type' => $bookingType,
            'journey_id' => $journeyId,
            'journey_id2' => $journeyId2,
        ], 200);
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'journey' => 'required|array',
            'passengers' => 'required|array',
            'booking_type' => 'required|in:OneWay,Return',
            'selected_seats' => 'required_if:journey.train_service,ETS|array',
            'selected_seats2' => 'required_if:journey2.train_service,ETS|array',
            'journey2' => 'required_if:booking_type,Return|array',
        ]);

        $journey = $request->input('journey');
        $journey2 = $request->input('journey2', []);
        $passengers = $request->input('passengers');
        $bookingType = $request->input('booking_type');
        $selectedSeats = $request->input('selected_seats', []);
        $selectedSeats2 = $request->input('selected_seats2', []);

        try {
            DB::beginTransaction();

            $builder = new ConcreteBookingBuilder();
            $builder->setUserId($request->input('user_id'));
            $director = new BookingDirector();
            $director->build($builder, $journey, $passengers, $selectedSeats, $journey2, $selectedSeats2);
            $booking = $builder->getBooking();

            DB::commit();

            return response()->json([
                'message' => 'Booking created successfully. Proceed to payment.',
                'booking' => $booking
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage()); //debug use
            return response()->json(['error' => 'Failed to create booking, Please try again later.'], 500);
        }
    }
}