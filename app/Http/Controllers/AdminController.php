<?php

namespace App\Http\Controllers;

use App\Facades\AdminFacade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Add admin middleware here if you have one
        // $this->middleware('admin');
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = AdminFacade::getDashboardStats();
        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Show user management page
     */
    public function users(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $status = $request->get('status');
        $role = $request->get('role');
        
        $userService = app(\App\Services\UserService::class);
        $result = $userService->getUsers($page, 10, $search);
        
        // Check if the result is successful
        if (!$result['success']) {
            // If failed, create empty result
            $users = [
                'success' => false,
                'data' => collect([])->paginate(10)
            ];
        } else {
            $users = $result;
        }
        
        return view('AdminPage.UserManagement', compact('users'));
    }

    /**
     * Show train management page
     */
    public function trains(Request $request)
    {
        $page = $request->get('page', 1);
        $search = $request->get('search');
        
        $trains = AdminFacade::getTrains($page, 10, $search);
        return view('admin.trains', compact('trains'));
    }

    /**
     * Show QR scanner page
     */
    public function qrScanner()
    {
        return view('admin.qr-scanner');
    }

    /**
     * Show newsletter page
     */
    public function newsletter()
    {
        $stats = AdminFacade::getNewsletterStats();
        return view('admin.newsletter', compact('stats'));
    }

    /**
     * Show refund management page
     */
    public function refunds()
    {
        $stats = AdminFacade::getRefundStats();
        return view('admin.refunds', compact('stats'));
    }

    /**
     * Show system information page
     */
    public function systemInfo()
    {
        $systemInfo = AdminFacade::getSystemInfo();
        return view('admin.system-info', compact('systemInfo'));
    }

    // API Endpoints for AJAX requests

    /**
     * Get dashboard stats via API
     */
    public function getDashboardStats(): JsonResponse
    {
        $stats = AdminFacade::getDashboardStats();
        return response()->json($stats);
    }

    /**
     * Dashboard datasets: filters
     */
    public function getDashboardFilters(): JsonResponse
    {
        $states = \App\Models\Station::query()
            ->whereNotNull('Location')
            ->distinct()
            ->pluck('Location')
            ->values();
        $stations = \App\Models\Station::query()
            ->whereNotNull('StationName')
            ->orderBy('StationName')
            ->pluck('StationName')
            ->values();
        return response()->json([
            'success' => true,
            'data' => [
                'states' => $states,
                'stations' => $stations,
            ]
        ]);
    }

    /**
     * Trips per month (optionally filter by state/location and station)
     */
    public function getTripsPerMonth(\Illuminate\Http\Request $request): JsonResponse
    {
        $state = $request->get('state');
        $station = $request->get('station');

        $q = \App\Models\Booking::query()
            ->join('Journeys', 'Bookings.JourneyID', '=', 'Journeys.JourneyID')
            ->leftJoin('Stations as S', 'S.StationName', '=', 'Journeys.FromLocation')
            ->when($station, function ($qq) use ($station) {
                $qq->where('Journeys.FromLocation', $station);
            })
            ->when($state, function ($qq) use ($state) {
                $qq->where('S.Location', $state);
            })
            ->selectRaw("strftime('%Y-%m', Bookings.Created_at) as ym, count(*) as total")
            ->groupBy('ym')
            ->orderBy('ym');

        $rows = $q->get();
        return response()->json([
            'success' => true,
            'data' => $rows,
        ]);
    }

    /**
     * Registered users growth per month
     */
    public function getUsersGrowth(): JsonResponse
    {
        $rows = \App\Models\User::query()
            ->selectRaw("strftime('%Y-%m', created_at) as ym, count(*) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();
        return response()->json(['success' => true, 'data' => $rows]);
    }

    /**
     * Profit trends per month
     */
    public function getProfitTrends(): JsonResponse
    {
        $rows = \App\Models\Booking::query()
            ->selectRaw("strftime('%Y-%m', Created_at) as ym, sum(Price) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();
        return response()->json(['success' => true, 'data' => $rows]);
    }

    /**
     * Get users via API
     */
    public function getUsers(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $search = $request->get('search');
        $status = $request->get('status');
        $role = $request->get('role');
        
        $userService = app(\App\Services\UserService::class);
        $users = $userService->getUsers($page, 10, $search);
        return response()->json($users);
    }

    /**
     * Update user status via API
     */
    public function updateUserStatus(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'status' => 'required|string|in:active,inactive,suspended,pending_verification'
        ]);

        $userService = app(\App\Services\UserService::class);
        $result = $userService->updateUserStatus($request->user_id, $request->status);
        return response()->json($result);
    }

    /**
     * Delete user via API
     */
    public function deleteUser($userId): JsonResponse
    {
        $userService = app(\App\Services\UserService::class);
        $result = $userService->deleteUser($userId);
        return response()->json($result);
    }

    /**
     * Get trains via API
     */
    public function getTrains(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $search = $request->get('search');
        
        $trains = AdminFacade::getTrains($page, 10, $search);
        return response()->json($trains);
    }

    /**
     * Add train via API
     */
    public function addTrain(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'route' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i|after:departure_time'
        ]);

        $result = AdminFacade::addTrain($request->all());
        return response()->json($result);
    }

    /**
     * Update train via API
     */
    public function updateTrain(Request $request, $trainId): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'route' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'required|date_format:H:i|after:departure_time'
        ]);

        $result = AdminFacade::updateTrain($trainId, $request->all());
        return response()->json($result);
    }

    /**
     * Delete train via API
     */
    public function deleteTrain($trainId): JsonResponse
    {
        $result = AdminFacade::deleteTrain($trainId);
        return response()->json($result);
    }

    /**
     * Scan QR code via API
     */
    public function scanQR(Request $request): JsonResponse
    {
        $request->validate([
            'qr_code' => 'required|string',
            'operation' => 'required|string|in:check-in,check-out'
        ]);

        $result = AdminFacade::scanQR($request->qr_code, $request->operation);
        return response()->json($result);
    }

    /**
     * Generate QR code via API
     */
    public function generateQR(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'type' => 'required|string|in:boarding,check-in,check-out'
        ]);

        $result = AdminFacade::generateQR($request->user_id, $request->type);
        return response()->json($result);
    }

    /**
     * Send newsletter via API
     */
    public function sendNewsletter(Request $request): JsonResponse
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'recipients' => 'required|string|in:all,active,newsletter_subscribers,verified'
        ]);

        $result = AdminFacade::sendNewsletter($request->all());
        return response()->json($result);
    }

    /**
     * Process refund via API
     */
    public function processRefund(Request $request): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|integer',
            'booking_id' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500'
        ]);

        $result = AdminFacade::processRefund($request->all());
        return response()->json($result);
    }

    /**
     * Export data via API
     */
    public function exportData(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|string|in:users,trains,bookings',
            'format' => 'required|string|in:csv,json,xml'
        ]);

        $result = AdminFacade::exportData($request->type, $request->format);
        return response()->json($result);
    }

    /**
     * Get system info via API
     */
    public function getSystemInfo(): JsonResponse
    {
        $systemInfo = AdminFacade::getSystemInfo();
        return response()->json($systemInfo);
    }
} 