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
        $result = \App\Facades\AdminFacade::addTrain($request->all());
        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    public function storeStation(Request $request)
    {
        $result = \App\Facades\AdminFacade::addStation($request->all());
        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    public function storeJourney(Request $request)
    {
        $result = \App\Facades\AdminFacade::addJourney($request->all());
        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
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
        $result = \App\Facades\AdminFacade::updateTrain($request->train_id, $request->all());
        $code = ($result['success'] ?? false) ? 200 : (($result['message'] ?? null) ? 422 : 404);
        return response()->json($result, $code);
    }

    public function updateStation(Request $request)
    {
        $result = \App\Facades\AdminFacade::updateStation($request->all());
        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }

    public function updateJourney(Request $request)
    {
        $result = \App\Facades\AdminFacade::updateJourney($request->all());
        return response()->json($result, ($result['success'] ?? false) ? 200 : 422);
    }
}
