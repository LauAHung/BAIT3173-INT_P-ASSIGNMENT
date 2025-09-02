<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Train;
use App\Models\Station;
use App\Models\Journey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TrainManagementController extends Controller
{
    public function index()
    {
        $trains = Train::with('station')->get();
        $stations = Station::all();
        $journeys = Journey::with('train')->get();
        
        return view('AdminPage.TrainManagement', compact('trains', 'stations', 'journeys'));
    }

    public function storeTrain(Request $request)
    {
        $request->validate([
            'train_id' => 'required|string|max:50|unique:Trains,TrainID',
            'train_no' => 'required|string|max:50',
            'train_service' => 'required|string|max:100',
            'seat_count' => 'required|integer|min:1',
            'is_available' => 'required|in:Active,Unavailable',
            'station_id' => 'required|string|exists:Stations,StationID'
        ]);

        try {
            $train = new Train();
            $train->TrainID = $request->train_id;
            $train->TrainNo = $request->train_no;
            $train->TrainService = $request->train_service;
            $train->SeatCount = $request->seat_count;
            $train->Is_available = $request->is_available;
            $train->StationID = $request->station_id;
            $train->Created_at = now();
            $train->save();

            // Log action
            app(\App\Services\AdminActivityLogger::class)->log('add_train', [
                'train_id' => $train->TrainID,
                'train_no' => $train->TrainNo,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Train added successfully',
                'train' => $train->load('station')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add train: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeStation(Request $request)
    {
        $request->validate([
            'station_id' => 'required|string|max:50|unique:Stations,StationID',
            'station_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'is_active' => 'required|boolean'
        ]);

        try {
            $station = new Station();
            $station->StationID = $request->station_id;
            $station->StationName = $request->station_name;
            $station->Location = $request->location;
            $station->Is_active = $request->is_active;
            $station->Created_at = now();
            $station->save();

            app(\App\Services\AdminActivityLogger::class)->log('add_station', [
                'station_id' => $station->StationID,
                'station_name' => $station->StationName,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Station added successfully',
                'station' => $station
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add station: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeJourney(Request $request)
    {
        $request->validate([
            'journey_id' => 'required|string|max:50|unique:Journeys,JourneyID',
            'train_id' => 'required|string|exists:Trains,TrainID',
            'from_location' => 'required|string|max:100',
            'to_location' => 'required|string|max:100|different:from_location',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'seat_available' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Scheduled,Delayed,Canceled'
        ]);

        try {
            $journey = new Journey();
            $journey->JourneyID = $request->journey_id;
            $journey->TrainID = $request->train_id;
            $journey->FromLocation = $request->from_location;
            $journey->ToLocation = $request->to_location;
            $journey->DepartureTime = $request->departure_time;
            $journey->ArrivalTime = $request->arrival_time;
            $journey->SeatAvailable = $request->seat_available;
            $journey->Price = $request->price;
            $journey->Status = $request->status;
            $journey->Created_at = now();
            $journey->save();

            app(\App\Services\AdminActivityLogger::class)->log('add_journey', [
                'journey_id' => $journey->JourneyID,
                'train_id' => $journey->TrainID,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Journey added successfully',
                'journey' => $journey->load('train')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add journey: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTrains()
    {
        $trains = Train::with('station')->get();
        return response()->json($trains);
    }

    public function getStations()
    {
        $stations = Station::all();
        return response()->json($stations);
    }

    public function getJourneys()
    {
        $journeys = Journey::with('train')->get();
        return response()->json($journeys);
    }

    public function updateTrain(Request $request)
    {
        $request->validate([
            'train_id' => 'required|string|exists:Trains,TrainID',
            'train_no' => 'required|string|max:50',
            'train_service' => 'required|string|max:100',
            'seat_count' => 'required|integer|min:1',
            'is_available' => 'required|in:Active,Unavailable',
            'station_id' => 'required|string|exists:Stations,StationID'
        ]);

        try {
            $train = Train::find($request->train_id);
            if (!$train) {
                return response()->json([
                    'success' => false,
                    'message' => 'Train not found'
                ], 404);
            }

            $train->TrainNo = $request->train_no;
            $train->TrainService = $request->train_service;
            $train->SeatCount = $request->seat_count;
            $train->Is_available = $request->is_available;
            $train->StationID = $request->station_id;
            $train->save();

            app(\App\Services\AdminActivityLogger::class)->log('update_train', [
                'train_id' => $train->TrainID,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Train updated successfully',
                'train' => $train->load('station')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update train: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStation(Request $request)
    {
        $request->validate([
            'station_id' => 'required|string|exists:Stations,StationID',
            'station_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'is_active' => 'required|boolean'
        ]);

        try {
            $station = Station::find($request->station_id);
            if (!$station) {
                return response()->json([
                    'success' => false,
                    'message' => 'Station not found'
                ], 404);
            }

            $station->StationName = $request->station_name;
            $station->Location = $request->location;
            $station->Is_active = $request->is_active;
            $station->save();

            app(\App\Services\AdminActivityLogger::class)->log('update_station', [
                'station_id' => $station->StationID,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Station updated successfully',
                'station' => $station
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update station: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateJourney(Request $request)
    {
        $request->validate([
            'journey_id' => 'required|string|exists:Journeys,JourneyID',
            'train_id' => 'required|string|exists:Trains,TrainID',
            'from_location' => 'required|string|max:100',
            'to_location' => 'required|string|max:100|different:from_location',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'seat_available' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Scheduled,Delayed,Canceled'
        ]);

        try {
            $journey = Journey::find($request->journey_id);
            if (!$journey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Journey not found'
                ], 404);
            }

            $journey->TrainID = $request->train_id;
            $journey->FromLocation = $request->from_location;
            $journey->ToLocation = $request->to_location;
            $journey->DepartureTime = $request->departure_time;
            $journey->ArrivalTime = $request->arrival_time;
            $journey->SeatAvailable = $request->seat_available;
            $journey->Price = $request->price;
            $journey->Status = $request->status;
            $journey->save();

            app(\App\Services\AdminActivityLogger::class)->log('update_journey', [
                'journey_id' => $journey->JourneyID,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Journey updated successfully',
                'journey' => $journey->load('train')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update journey: ' . $e->getMessage()
            ], 500);
        }
    }
}
