<?php
/**
 * author: Lau Aik Hung
 * student id: 23WMR14555
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\AdminService;
use App\Services\UserService;
use App\Services\TrainService;
use App\Services\TrainManagementService;
use App\Services\QRScannerService;
use App\Services\NewsletterService;
use App\Services\RefundService;

/**
 * Admin Facade - Simplified interface for admin operations
 * 
 * This facade provides a unified interface for all administrative tasks
 * including user management, train management, QR operations, and more.
 */
class AdminFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'admin.facade';
    }

    /**
     * Get dashboard statistics
     */
    public static function getDashboardStats()
    {
        return app(AdminService::class)->getDashboardStats();
    }

    /**
     * Get all users with pagination
     */
    public static function getUsers($page = 1, $perPage = 10, $search = null)
    {
        return app(UserService::class)->getUsers($page, $perPage, $search);
    }

    /**
     * Get user details by ID
     */
    public static function getUserById($userId)
    {
        return app(UserService::class)->getUserById($userId);
    }

    /**
     * Update user status
     */
    public static function updateUserStatus($userId, $status)
    {
        return app(UserService::class)->updateUserStatus($userId, $status);
    }

    /**
     * Get all trains with pagination
     */
    public static function getTrains($page = 1, $perPage = 10, $search = null)
    {
        return app(TrainManagementService::class)->listTrains((int)$page, (int)$perPage, $search);
    }

    /**
     * Add new train
     */
    public static function addTrain($trainData)
    {
        return app(TrainManagementService::class)->createTrain($trainData);
    }

    /**
     * Update train information
     */
    public static function updateTrain($trainId, $trainData)
    {
        $merged = array_merge($trainData, ['train_id' => $trainId]);
        return app(TrainManagementService::class)->updateTrain($merged);
    }

    /**
     * Delete train
     */
    

    // Stations
    public static function addStation($data)
    {
        return app(TrainManagementService::class)->createStation($data);
    }
    public static function updateStation($data)
    {
        return app(TrainManagementService::class)->updateStation($data);
    }

    // Journeys
    public static function addJourney($data)
    {
        return app(TrainManagementService::class)->createJourney($data);
    }
    public static function updateJourney($data)
    {
        return app(TrainManagementService::class)->updateJourney($data);
    }

    // Users
    public static function adminUpdateUserStatus($userId, $status)
    {
        return app(TrainManagementService::class)->updateUserStatus($userId, $status);
    }

    /**
     * Scan QR code for check-in/check-out
     */
    public static function scanQR($qrCode, $operation = 'check-in')
    {
        return app(QRScannerService::class)->scanQR($qrCode, $operation);
    }

    /**
     * Generate QR code for user
     */
    public static function generateQR($userId, $type = 'boarding')
    {
        return app(QRScannerService::class)->generateQR($userId, $type);
    }

    /**
     * Send newsletter to users
     */
    public static function sendNewsletter($newsletterData)
    {
        return app(NewsletterService::class)->sendNewsletter($newsletterData);
    }

    /**
     * Get newsletter statistics
     */
    public static function getNewsletterStats()
    {
        return app(NewsletterService::class)->getNewsletterStats();
    }

    /**
     * Process refund request
     */
    public static function processRefund($refundData)
    {
        return app(RefundService::class)->processRefund($refundData);
    }

    /**
     * Get refund statistics
     */
    public static function getRefundStats()
    {
        return app(RefundService::class)->getRefundStats();
    }

    /**
     * Get system information
     */
    public static function getSystemInfo()
    {
        return app(AdminService::class)->getSystemInfo();
    }

    /**
     * Export data (users, trains, etc.)
     */
    public static function exportData($type, $format = 'csv')
    {
        return app(AdminService::class)->exportData($type, $format);
    }
} 