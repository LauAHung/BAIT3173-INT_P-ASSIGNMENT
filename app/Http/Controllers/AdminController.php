<?php

namespace App\Http\Controllers;

use App\Facades\AdminFacade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->middleware('auth');
        // Add admin middleware here if you have one
        // $this->middleware('admin');
        $this->middleware('admin');

        $this->apiBaseUrl = config('app.api_base_url', 'http://localhost:8001/api');
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
        $resp = Http::get("{$this->apiBaseUrl}/admin/dashboard/stats");
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to fetch admin dashboard stats', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to fetch dashboard stats'], $resp->status());
    }

    /**
     * Dashboard datasets: filters
     */
    public function getDashboardFilters(): JsonResponse
    {
        $resp = Http::get("{$this->apiBaseUrl}/admin/dashboard/filters");
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to fetch dashboard filters', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to fetch filters'], $resp->status());
    }

    /**
     * Trips per month (optionally filter by state/location and station)
     */
    public function getTripsPerMonth(\Illuminate\Http\Request $request): JsonResponse
    {
        $resp = Http::get("{$this->apiBaseUrl}/admin/dashboard/trips", $request->only(['state','station']));
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to fetch trips per month', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to fetch trips'], $resp->status());
    }

    /**
     * Registered users growth per month
     */
    public function getUsersGrowth(): JsonResponse
    {
        $resp = Http::get("{$this->apiBaseUrl}/admin/dashboard/users-growth");
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to fetch users growth', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to fetch users growth'], $resp->status());
    }

    /**
     * Profit trends per month
     */
    public function getProfitTrends(): JsonResponse
    {
        $resp = Http::get("{$this->apiBaseUrl}/admin/dashboard/profit");
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to fetch profit trends', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to fetch profit trends'], $resp->status());
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
        
        $resp = Http::get("{$this->apiBaseUrl}/admin/users", [
            'page' => $page,
            'search' => $search,
            'status' => $status,
            'role' => $role,
        ]);
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to fetch users list', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to fetch users'], $resp->status());
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

        $resp = Http::put("{$this->apiBaseUrl}/admin/users/{$request->user_id}/status", [
            'status' => $request->status,
        ]);
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to update user status', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to update user status'], $resp->status());
    }

    /**
     * Delete user via API
     */
    public function deleteUser($userId): JsonResponse
    {
        $resp = Http::delete("{$this->apiBaseUrl}/admin/users/{$userId}");
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to delete user', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to delete user'], $resp->status());
    }

    /**
     * Get trains via API
     */
    public function getTrains(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $search = $request->get('search');
        
        $resp = Http::get("{$this->apiBaseUrl}/admin/trains", [
            'page' => $page,
            'search' => $search,
        ]);
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to fetch trains', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to fetch trains'], $resp->status());
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

        $resp = Http::post("{$this->apiBaseUrl}/admin/trains", $request->all());
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to add train', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to add train'], $resp->status());
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

        $resp = Http::put("{$this->apiBaseUrl}/admin/trains/{$trainId}", $request->all());
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to update train', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to update train'], $resp->status());
    }

    /**
     * Delete train via API
     */
    public function deleteTrain($trainId): JsonResponse
    {
        $resp = Http::delete("{$this->apiBaseUrl}/admin/trains/{$trainId}");
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to delete train', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to delete train'], $resp->status());
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

        $resp = Http::post("{$this->apiBaseUrl}/admin/qr/scan", [
            'qr_code' => $request->qr_code,
            'operation' => $request->operation,
        ]);
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to scan QR', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to scan QR'], $resp->status());
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

        $resp = Http::post("{$this->apiBaseUrl}/admin/qr/generate", [
            'user_id' => $request->user_id,
            'type' => $request->type,
        ]);
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to generate QR', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to generate QR'], $resp->status());
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

        $resp = Http::post("{$this->apiBaseUrl}/admin/newsletter/send", $request->all());
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to send newsletter', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to send newsletter'], $resp->status());
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

        $resp = Http::post("{$this->apiBaseUrl}/admin/refunds/process", $request->all());
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to process refund', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to process refund'], $resp->status());
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

        $resp = Http::get("{$this->apiBaseUrl}/admin/export", $request->only(['type','format']));
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to issue export token', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to issue export token'], $resp->status());
    }

    /**
     * Download exported data using short-lived token (reauth + throttle advised at route level)
     */
    public function downloadExport(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string|size:32'
        ]);

        $resp = Http::get("{$this->apiBaseUrl}/admin/export/download", $request->only(['token']));
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to download export', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to download export'], $resp->status());
    }

    /**
     * Get system info via API
     */
    public function getSystemInfo(): JsonResponse
    {
        $resp = Http::get("{$this->apiBaseUrl}/admin/system/info");
        if ($resp->successful()) {
            return response()->json($resp->json());
        }
        Log::error('Failed to fetch system info', ['status' => $resp->status(), 'body' => $resp->body()]);
        return response()->json(['success' => false, 'message' => 'Failed to fetch system info'], $resp->status());
    }
} 