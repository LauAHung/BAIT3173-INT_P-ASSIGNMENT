<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * User Web Service Consumer - Consumes web services from other modules
 * 
 * This service demonstrates how the User module can consume web services
 * from other modules following the Interface Agreement (IFA) standards.
 */
class UserWebServiceConsumer
{
    private $baseUrl;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.admin.base_url', 'http://localhost:8000/api/admin');
        $this->timeout = config('services.admin.timeout', 30);
    }

    /**
     * Get admin dashboard statistics from Admin module
     */
    public function getAdminDashboardStats()
    {
        try {
            $cacheKey = 'admin_dashboard_stats';
            
            // Check cache first
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/dashboard/stats');

            if ($response->successful()) {
                $data = $response->json();
                
                // Cache for 5 minutes
                Cache::put($cacheKey, $data, 300);
                
                return $data;
            }

            return [
                'status' => 'error',
                'message' => 'Failed to retrieve admin dashboard stats',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get user statistics from Admin module
     */
    public function getAdminUserStats()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/users/stats');

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to retrieve admin user stats',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get users from Admin module
     */
    public function getAdminUsers($page = 1, $perPage = 10, $search = null)
    {
        try {
            $params = [
                'page' => $page,
                'per_page' => $perPage
            ];

            if ($search) {
                $params['search'] = $search;
            }

            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/users', $params);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to retrieve users from admin service',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get user by ID from Admin module
     */
    public function getAdminUserById($userId)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/users/' . $userId);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to retrieve user from admin service',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Update user status via Admin module
     */
    public function updateUserStatusViaAdmin($userId, $status)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->put($this->baseUrl . '/users/' . $userId . '/status', [
                    'status' => $status
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to update user status via admin service',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get trains from Admin module
     */
    public function getAdminTrains($page = 1, $perPage = 10, $search = null)
    {
        try {
            $params = [
                'page' => $page,
                'per_page' => $perPage
            ];

            if ($search) {
                $params['search'] = $search;
            }

            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/trains', $params);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to retrieve trains from admin service',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate QR code via Admin module
     */
    public function generateQRViaAdmin($userId, $type = 'boarding')
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/qr/generate', [
                    'user_id' => $userId,
                    'type' => $type
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to generate QR code via admin service',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send newsletter via Admin module
     */
    public function sendNewsletterViaAdmin($subject, $content, $recipients = 'all')
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/newsletter/send', [
                    'subject' => $subject,
                    'content' => $content,
                    'recipients' => $recipients
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to send newsletter via admin service',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get system information from Admin module
     */
    public function getAdminSystemInfo()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/system/info');

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to retrieve system info from admin service',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check admin service health
     */
    public function checkAdminServiceHealth()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/health');

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Admin service is not healthy',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process refund via Admin module
     */
    public function processRefundViaAdmin($userId, $bookingId, $amount, $reason)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/refunds/process', [
                    'user_id' => $userId,
                    'booking_id' => $bookingId,
                    'amount' => $amount,
                    'reason' => $reason
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to process refund via admin service',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get newsletter statistics from Admin module
     */
    public function getAdminNewsletterStats()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->baseUrl . '/newsletter/stats');

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Failed to retrieve newsletter stats from admin service',
                'error' => $response->body()
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to admin service',
                'error' => $e->getMessage()
            ];
        }
    }
} 