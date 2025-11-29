<?php
/**
 * author: Lau Aik Hung
 * student id: 23WMR14555
 */

namespace App\Services;

use App\Models\Train;
use App\Models\Station;
use App\Models\Journey;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TrainManagementService
{
    /**
     * List trains with pagination and optional search
     */
    public function listTrains(int $page = 1, int $perPage = 10, ?string $search = null): array
    {
        try {
            $q = Train::query()->with('station');
            if ($search) {
                $like = "%{$search}%";
                $q->where(function($qq) use ($like) {
                    $qq->where('TrainNo', 'like', $like)
                       ->orWhere('TrainService', 'like', $like)
                       ->orWhere('Is_available', 'like', $like);
                });
            }
            $p = $q->orderByDesc('Created_at')
                  ->paginate($perPage, ['*'], 'page', $page);
            return [
                'success' => true,
                'data' => $p,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to list trains: ' . $e->getMessage(),
            ];
        }
    }
    public function createTrain(array $data): array
    {
        // Auto-generate Train ID
        $trainId = $this->generateTrainId();
        
        $validated = validator($data, [
            'train_no' => 'required|string|max:50',
            'train_service' => 'required|string|max:100',
            'is_available' => 'required|in:Active,Unavailable',
            'station_id' => 'required|string|exists:Stations,StationID',
        ])->validate();

        $train = new Train();
        $train->TrainID = $trainId;
        $train->TrainNo = $validated['train_no'];
        $train->TrainService = $validated['train_service'];
        $train->SeatCount = 200; // Default value
        $train->Is_available = $validated['is_available'];
        $train->StationID = $validated['station_id'];
        $train->Created_at = now();
        $train->save();

        app(AdminActivityLogger::class)->log('add_train', [
            'train_id' => $train->TrainID,
            'train_no' => $train->TrainNo,
        ]);

        return ['success' => true, 'message' => 'Train added successfully', 'train' => $train->load('station')];
    }

    public function updateTrain(array $data): array
    {
        $validated = validator($data, [
            'train_id' => 'required|string|exists:Trains,TrainID',
            'train_no' => 'required|string|max:50',
            'train_service' => 'required|string|max:100',
            'is_available' => 'required|in:Active,Unavailable',
            'station_id' => 'required|string|exists:Stations,StationID',
        ])->validate();

        $train = Train::find($validated['train_id']);
        if (!$train) return ['success' => false, 'message' => 'Train not found'];

        $train->TrainNo = $validated['train_no'];
        $train->TrainService = $validated['train_service'];
        $train->SeatCount = 200; // Default value
        $train->Is_available = $validated['is_available'];
        $train->StationID = $validated['station_id'];
        $train->save();

        app(AdminActivityLogger::class)->log('update_train', [ 'train_id' => $train->TrainID ]);

        return ['success' => true, 'message' => 'Train updated successfully', 'train' => $train->load('station')];
    }

    public function createStation(array $data): array
    {
        // Auto-generate Station ID
        $stationId = $this->generateStationId();
        
        $validated = validator($data, [
            'station_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ])->validate();

        $station = new Station();
        $station->StationID = $stationId;
        $station->StationName = $validated['station_name'];
        $station->Location = $validated['location'];
        $station->Is_active = $validated['is_active'];
        $station->Created_at = now();
        $station->save();

        app(AdminActivityLogger::class)->log('add_station', [
            'station_id' => $station->StationID,
            'station_name' => $station->StationName,
        ]);

        return ['success' => true, 'message' => 'Station added successfully', 'station' => $station];
    }

    public function updateStation(array $data): array
    {
        $validated = validator($data, [
            'station_id' => 'required|string|exists:Stations,StationID',
            'station_name' => 'required|string|max:100',
            'location' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ])->validate();

        $station = Station::find($validated['station_id']);
        if (!$station) return ['success' => false, 'message' => 'Station not found'];
        $station->StationName = $validated['station_name'];
        $station->Location = $validated['location'];
        $station->Is_active = $validated['is_active'];
        $station->save();

        app(AdminActivityLogger::class)->log('update_station', [ 'station_id' => $station->StationID ]);

        return ['success' => true, 'message' => 'Station updated successfully', 'station' => $station];
    }

    public function createJourney(array $data): array
    {
        // Auto-generate Journey ID
        $journeyId = $this->generateJourneyId();
        
        $validated = validator($data, [
            'train_id' => 'required|string|exists:Trains,TrainID',
            'from_location' => 'required|string|max:100',
            'to_location' => 'required|string|max:100|different:from_location',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'price' => 'required|numeric|min:0',
        ])->validate();

        $journey = new Journey();
        $journey->JourneyID = $journeyId;
        $journey->TrainID = $validated['train_id'];
        $journey->FromLocation = $validated['from_location'];
        $journey->ToLocation = $validated['to_location'];
        $journey->DepartureTime = $validated['departure_time'];
        $journey->ArrivalTime = $validated['arrival_time'];
        $journey->SeatAvailable = 200; // Default value
        $journey->Price = $validated['price'];
        $journey->Status = 'Scheduled'; // Default value
        $journey->Created_at = now();
        $journey->save();

        app(AdminActivityLogger::class)->log('add_journey', [ 'journey_id' => $journey->JourneyID, 'train_id' => $journey->TrainID ]);

        return ['success' => true, 'message' => 'Journey added successfully', 'journey' => $journey->load('train')];
    }

    public function updateJourney(array $data): array
    {
        $validated = validator($data, [
            'journey_id' => 'required|string|exists:Journeys,JourneyID',
            'train_id' => 'required|string|exists:Trains,TrainID',
            'from_location' => 'required|string|max:100',
            'to_location' => 'required|string|max:100|different:from_location',
            'departure_time' => 'required|date',
            'arrival_time' => 'required|date|after:departure_time',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Scheduled,Delayed,Canceled',
        ])->validate();

        $journey = Journey::find($validated['journey_id']);
        if (!$journey) return ['success' => false, 'message' => 'Journey not found'];

        $journey->TrainID = $validated['train_id'];
        $journey->FromLocation = $validated['from_location'];
        $journey->ToLocation = $validated['to_location'];
        $journey->DepartureTime = $validated['departure_time'];
        $journey->ArrivalTime = $validated['arrival_time'];
        $journey->SeatAvailable = 200; // Default value
        $journey->Price = $validated['price'];
        $journey->Status = $validated['status']; // Use the selected status
        $journey->save();

        app(AdminActivityLogger::class)->log('update_journey', [ 'journey_id' => $journey->JourneyID ]);

        return ['success' => true, 'message' => 'Journey updated successfully', 'journey' => $journey->load('train')];
    }

    public function updateUserStatus(int $userId, string $status): array
    {
        $user = User::find($userId);
        if (!$user) return ['success' => false, 'message' => 'User not found'];
        $user->account_status = $status;
        $user->save();
        app(AdminActivityLogger::class)->log('change_user_status', [ 'target_user_id' => $user->user_id, 'new_status' => $status ]);
        return ['success' => true, 'message' => 'User status updated successfully', 'user' => $user];
    }

    /**
     * Generate auto-incrementing Train ID starting with TR
     */
    private function generateTrainId(): string
    {
        $lastTrain = Train::orderBy('TrainID', 'desc')->first();
        
        if (!$lastTrain) {
            return 'TR001';
        }
        
        // Extract the number part from the last TrainID
        $lastNumber = (int) substr($lastTrain->TrainID, 2);
        $newNumber = $lastNumber + 1;
        
        return 'TR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate auto-incrementing Station ID starting with ST
     */
    private function generateStationId(): string
    {
        $lastStation = Station::orderBy('StationID', 'desc')->first();
        
        if (!$lastStation) {
            return 'ST001';
        }
        
        // Extract the number part from the last StationID
        $lastNumber = (int) substr($lastStation->StationID, 2);
        $newNumber = $lastNumber + 1;
        
        return 'ST' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate auto-incrementing Journey ID starting with JR
     */
    private function generateJourneyId(): string
    {
        $lastJourney = Journey::orderBy('JourneyID', 'desc')->first();
        
        if (!$lastJourney) {
            return 'JR001';
        }
        
        // Extract the number part from the last JourneyID
        $lastNumber = (int) substr($lastJourney->JourneyID, 2);
        $newNumber = $lastNumber + 1;
        
        return 'JR' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}



