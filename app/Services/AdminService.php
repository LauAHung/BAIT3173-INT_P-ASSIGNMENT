<?php

namespace App\Services;

use App\Models\User;
use App\Models\Train;
use App\Models\Booking;
// use App\Models\Refund; // Removed to avoid missing model error
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use Exception;

class AdminService
{
    /**
     * Get dashboard statistics
     */
    public function getDashboardStats()
    {
        try {
            // Use a very short cache key to avoid DB cache key length limits
            $stats = Cache::remember('ads', 300, function () {
                return [
                    'total_users' => User::count(),
                    'active_users' => User::where('account_status', 'active')->count(),
                    'pending_users' => User::where('account_status', 'pending_verification')->count(),
                    'social_users' => User::whereNotNull('social_provider')->count(),
                    'total_trains' => Train::count(),
                    'active_trains' => Train::where('Is_available', 'Active')->count(),
                    'total_bookings' => Booking::count(),
                    'pending_refunds' => Schema::hasTable('refunds')
                        ? DB::table('refunds')->where('status', 'pending')->count()
                        : 0,
                    'recent_users' => User::latest()->take(5)->get(),
                    'recent_bookings' => Booking::with('user')->latest()->take(5)->get(),
                ];
            });

            return [
                'success' => true,
                'data' => $stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get dashboard stats: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get system information
     */
    public function getSystemInfo()
    {
        try {
            $systemInfo = [
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'database' => config('database.default'),
                'cache_driver' => config('cache.default'),
                'queue_driver' => config('queue.default'),
                'mail_driver' => config('mail.default'),
                'app_environment' => config('app.env'),
                'app_debug' => config('app.debug'),
                'timezone' => config('app.timezone'),
                'locale' => config('app.locale'),
                'server_info' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'memory_usage' => memory_get_usage(true),
                'peak_memory_usage' => memory_get_peak_usage(true),
            ];

            return [
                'success' => true,
                'data' => $systemInfo
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get system info: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Export data
     */
    public function exportData($type, $format = 'csv')
    {
        try {
            switch ($type) {
                case 'users':
                    $data = User::all();
                    $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.' . $format;
                    break;
                case 'trains':
                    $data = Train::all();
                    $filename = 'trains_export_' . date('Y-m-d_H-i-s') . '.' . $format;
                    break;
                case 'bookings':
                    $data = Booking::with('user')->get();
                    $filename = 'bookings_export_' . date('Y-m-d_H-i-s') . '.' . $format;
                    break;
                default:
                    throw new Exception('Invalid export type');
            }

            return [
                'success' => true,
                'data' => $data,
                'filename' => $filename,
                'format' => $format
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to export data: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            Cache::flush();
            return [
                'success' => true,
                'message' => 'Cache cleared successfully'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get database statistics
     */
    public function getDatabaseStats()
    {
        try {
            $stats = [
                'users_table' => DB::table('users')->count(),
                'trains_table' => DB::table('trains')->count(),
                'bookings_table' => DB::table('bookings')->count(),
                'refunds_table' => DB::table('refunds')->count(),
                'database_size' => $this->getDatabaseSize(),
            ];

            return [
                'success' => true,
                'data' => $stats
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to get database stats: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get database size
     */
    private function getDatabaseSize()
    {
        try {
            $database = config('database.connections.' . config('database.default') . '.database');
            $result = DB::select("SELECT pg_size_pretty(pg_database_size('$database')) as size");
            return $result[0]->size ?? 'Unknown';
        } catch (Exception $e) {
            return 'Unknown';
        }
    }
} 