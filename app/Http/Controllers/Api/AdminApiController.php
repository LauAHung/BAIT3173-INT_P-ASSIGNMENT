<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Facades\AdminFacade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * Admin API Controller - Exposes REST API endpoints for other modules
 * 
 * This controller provides web services that can be consumed by other modules
 * following the Interface Agreement (IFA) standards.
 */
class AdminApiController extends Controller
{
    /**
     * Get dashboard statistics
     * GET /api/admin/dashboard/stats
     */
    public function getDashboardStats(): JsonResponse
    {
        try {
            $stats = AdminFacade::getDashboardStats();
            
            return response()->json([
                'status' => 'success',
                'data' => $stats['data'] ?? [],
                'message' => 'Dashboard statistics retrieved successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve dashboard statistics',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get all users with pagination and search
     * GET /api/admin/users
     */
    public function getUsers(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            
            $users = AdminFacade::getUsers($page, $perPage, $search);
            
            return response()->json([
                'status' => 'success',
                'data' => $users['data'] ?? [],
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $users['data']->total() ?? 0,
                    'last_page' => $users['data']->lastPage() ?? 1
                ],
                'message' => 'Users retrieved successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve users',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get user by ID
     * GET /api/admin/users/{id}
     */
    public function getUserById($id): JsonResponse
    {
        try {
            $user = AdminFacade::getUserById($id);
            
            if (!$user['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $user['message'],
                    'timestamp' => now()->toISOString()
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $user['data'],
                'message' => 'User retrieved successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve user',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Update user status
     * PUT /api/admin/users/{id}/status
     */
    public function updateUserStatus(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'status' => 'required|string|in:active,inactive,suspended,pending_verification'
            ]);

            $result = AdminFacade::updateUserStatus($id, $request->status);
            
            if (!$result['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'],
                    'timestamp' => now()->toISOString()
                ], 400);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $result['data'],
                'message' => $result['message'],
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update user status',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get user statistics
     * GET /api/admin/users/stats
     */
    public function getUserStats(): JsonResponse
    {
        try {
            $userService = app(\App\Services\UserService::class);
            $stats = $userService->getUserStats();
            
            return response()->json([
                'status' => 'success',
                'data' => $stats['data'] ?? [],
                'message' => 'User statistics retrieved successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve user statistics',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get all trains
     * GET /api/admin/trains
     */
    public function getTrains(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            
            $trains = AdminFacade::getTrains($page, $perPage, $search);
            
            return response()->json([
                'status' => 'success',
                'data' => $trains['data'] ?? [],
                'message' => 'Trains retrieved successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve trains',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Add new train
     * POST /api/admin/trains
     */
    public function addTrain(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'route' => 'required|string|max:255',
                'capacity' => 'required|integer|min:1',
                'departure_time' => 'required|date_format:H:i',
                'arrival_time' => 'required|date_format:H:i|after:departure_time'
            ]);

            $result = AdminFacade::addTrain($request->all());
            
            if (!$result['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'],
                    'timestamp' => now()->toISOString()
                ], 400);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $result['data'],
                'message' => $result['message'],
                'timestamp' => now()->toISOString()
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add train',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Scan QR code
     * POST /api/admin/qr/scan
     */
    public function scanQR(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'qr_code' => 'required|string',
                'operation' => 'required|string|in:check-in,check-out'
            ]);

            $result = AdminFacade::scanQR($request->qr_code, $request->operation);
            
            if (!$result['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'],
                    'timestamp' => now()->toISOString()
                ], 400);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $result['data'],
                'message' => $result['message'],
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to scan QR code',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Generate QR code
     * POST /api/admin/qr/generate
     */
    public function generateQR(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'type' => 'required|string|in:boarding,check-in,check-out'
            ]);

            $result = AdminFacade::generateQR($request->user_id, $request->type);
            
            if (!$result['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'],
                    'timestamp' => now()->toISOString()
                ], 400);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $result['data'],
                'message' => $result['message'],
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate QR code',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Send newsletter
     * POST /api/admin/newsletter/send
     */
    public function sendNewsletter(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
                'recipients' => 'required|string|in:all,active,newsletter_subscribers,verified'
            ]);

            $result = AdminFacade::sendNewsletter($request->all());
            
            if (!$result['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'],
                    'timestamp' => now()->toISOString()
                ], 400);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $result['data'],
                'message' => $result['message'],
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send newsletter',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get newsletter statistics
     * GET /api/admin/newsletter/stats
     */
    public function getNewsletterStats(): JsonResponse
    {
        try {
            $stats = AdminFacade::getNewsletterStats();
            
            return response()->json([
                'status' => 'success',
                'data' => $stats['data'] ?? [],
                'message' => 'Newsletter statistics retrieved successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve newsletter statistics',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Process refund
     * POST /api/admin/refunds/process
     */
    public function processRefund(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'booking_id' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'reason' => 'required|string|max:500'
            ]);

            $result = AdminFacade::processRefund($request->all());
            
            if (!$result['success']) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['message'],
                    'timestamp' => now()->toISOString()
                ], 400);
            }
            
            return response()->json([
                'status' => 'success',
                'data' => $result['data'],
                'message' => $result['message'],
                'timestamp' => now()->toISOString()
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process refund',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get system information
     * GET /api/admin/system/info
     */
    public function getSystemInfo(): JsonResponse
    {
        try {
            $systemInfo = AdminFacade::getSystemInfo();
            
            return response()->json([
                'status' => 'success',
                'data' => $systemInfo['data'] ?? [],
                'message' => 'System information retrieved successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve system information',
                'error' => $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Health check endpoint
     * GET /api/admin/health
     */
    public function healthCheck(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Admin API is healthy',
            'timestamp' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    }
} 