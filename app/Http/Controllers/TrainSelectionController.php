<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TrainSelectionController extends Controller
{
    protected $apiBaseUrl;
    public function __construct()
    {
        $this->apiBaseUrl = config('app.api_base_url', 'http://localhost:8001/api');
    }

    public function index(Request $request)
    {
        session()->forget(['selected_journey', 'selected_journey2', 'passenger_info', 'passengers_count', 'booking_type']);

        $response = Http::get("{$this->apiBaseUrl}/journeys", $request->all());

        if ($response->successful()) {
            $journeys = $response->json()['journeys'];
            $successMessage = session('success');
            return view('TrainSelectionPage', compact('journeys', 'successMessage'));
        } else {
            Log::error('Failed to fetch journeys from API', ['status' => $response->status(), 'body' => $response->body()]);
            return redirect()->route('train.selection')->with('error', 'Unable to fetch journeys. Please try again.');
        }
    }

    public function indexReturn(Request $request)
    {
        if ($request->has('clear_outbound')) {
            session()->forget(['selected_journey', 'selected_journey2', 'passenger_info', 'passengers_count', 'booking_type']);
            return redirect()->route('train.selection', $request->all());
        }

        $selectedJourney = session('selected_journey');
        $response = Http::get("{$this->apiBaseUrl}/journeys/return", array_merge($request->all(), [
            'selected_journey' => $selectedJourney
        ]));

        if ($response->successful()) {
            $journeys = $response->json()['journeys'];
            $successMessage = session('success');
            return view('TrainSelectionPage', compact('journeys', 'successMessage'));
        } else {
            Log::error('Failed to fetch return journeys from API', ['status' => $response->status(), 'body' => $response->body()]);
            return redirect()->route('train.selection.return')->with('error', 'Unable to fetch return journeys. Please try again.');
        }
    }

    public function showPassengerInfo(Request $request)
    {
        Log::info('showPassengerInfo parameters', $request->all()); //debug use
        if (!Auth::check()) {
            return redirect()->route('signin')->with('info', 'Please login first before make a booking.');
        }

        $response = Http::post("{$this->apiBaseUrl}/journeys/passenger-info", [
            'journey_id'   => $request->input('journey_id'),
            'journey_id2'  => $request->input('journey_id2'),
            'booking_type' => $request->input('booking_type', 'OneWay'),
            'passengers'   => $request->input('passengers', 1),
        ]);

        if ($response->successful()) {
            $data = $response->json();
            session([
                'selected_journey' => $data['journey'],
                'passengers_count' => $data['passengers'],
                'booking_type' => $data['booking_type'],
            ]);

            if (isset($data['journey2'])) {
                session(['selected_journey2' => $data['journey2']]);
            }

            if ($data['booking_type'] == 'Return' && !$request->input('journey_id2')) {
                return redirect()->route('train.selection.return', $request->all());
            }

            $journey = $data['journey'];
            $journey2 = $data['journey2'] ?? null;
            $passengers = $data['passengers'];
            $bookingType = $data['booking_type'];

            return view('PassengerInfoPage', compact('passengers', 'journey', 'journey2', 'bookingType'));
        } else {
            $error = $response->json()['error'] ?? 'Unable to process journey selection.';
            Log::error('Failed to process passenger info', ['status' => $response->status(), 'body' => $response->body()]);
            return redirect()->route('train.selection')->with('error', $error);
        }
    }

    public function storePassengerInfo(Request $request)
    {
        $validated = $request->validate([
            'passenger.*.name' => ['required', 'regex:/^[a-zA-Z\s\'-]{2,}$/', 'max:255'],
            'passenger.*.contact_no' => ['required', 'regex:/^01[0-9]-[0-9]{7,8}$/', 'max:20'],
            'passenger.*.gender' => 'required|in:male,female',
            'passenger.*.ticket_type' => 'required|in:Dewasa/Adult,Pelajar/Student,Warga Emas/Senior Citizen,OKU',
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

        $response = Http::post("{$this->apiBaseUrl}/journeys/passenger-info/store", $request->all());

        if ($response->successful()) {
            $data = $response->json();
            session([
                'passenger_info' => $data['passenger_info'],
                'passengers_count' => $data['passengers_count'],
                'booking_type' => $data['booking_type'],
            ]);

            return redirect()->route('selectseat', [
                'passengers' => $data['passengers_count'],
                'journey_id' => $data['journey_id'],
                'journey_id2' => $data['journey_id2'],
                'booking_type' => $data['booking_type'],
            ]);
        } else {
            $errors = $response->json()['errors'] ?? ['general' => 'Unable to store passenger information.'];
            Log::error('Failed to store passenger info', ['status' => $response->status(), 'body' => $response->body()]);
            return back()->withErrors($errors)->withInput();
        }
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
        $userid = Auth::id();
        $journey = session('selected_journey');
        $journey2 = session('selected_journey2');
        $passengers = session('passenger_info');
        $bookingType = session('booking_type', 'OneWay');
        $selectedSeatsInput = $request->input('selected_seats', []);
        $selectedSeatsInput2 = $request->input('selected_seats2', []);

        $selectedSeats = array_reduce($selectedSeatsInput, function ($carry, $item) {
            return array_merge($carry, is_array($item) ? $item : [$item]);
        }, []);

        // always array even it is null
        $selectedSeats2 = is_array($selectedSeatsInput2) ? array_reduce($selectedSeatsInput2, function ($carry, $item) {
            return array_merge($carry, is_array($item) ? $item : [$item]);
        }, []) : [];
        
        $storeload = [
            'user_id' => $userid,
            'journey' => $journey,
            'passengers' => $passengers,
            'booking_type' => $bookingType,
            'selected_seats' => $selectedSeats,
        ];

        if ($bookingType === 'Return' && $journey2) {
            $storeload['journey2'] = $journey2;
            $storeload['selected_seats2'] = $selectedSeats2;
        }
        
        $response = Http::post("{$this->apiBaseUrl}/bookings", $storeload);

        if ($response->successful()) {
            session()->forget(['selected_journey', 'selected_journey2', 'passenger_info', 'passengers_count', 'booking_type']);
            return redirect()->route('train.selection')->with('success', $response->json()['message']);
        } else {
            $error = $response->json()['error'] ?? 'Failed to create booking.';
            Log::error('Failed to create booking via API', ['status' => $response->status(), 'body' => $response->body()]);
            return redirect()->route('selectseat', [
                'journey_id' => $journey['id'] ?? 0,
                'journey_id2' => $journey2['id'] ?? null,
                'passengers' => count($passengers),
                'booking_type' => $bookingType,
            ])->with('error', $error);
        }
    }
}