<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\ConcessionApplication;
use SplFileObject;
use Carbon\Carbon;

class SecureConcessionCardController extends ConcessionCardController
{
    /**
     * Constructor to apply additional middleware for security
     */
    public function __construct()
    {
        // Apply rate limiting and CAPTCHA verification middleware to specific methods
        $this->middleware('throttle:10,1')->only(['submitApplication']);
        $this->middleware('verify.captcha')->only(['submitApplication']);
        parent::__construct();
    }

    /**
     * Override submitApplication to include anomaly detection
     */
    public function submitApplication(Request $request)
    {
        try {
            // Perform anomaly detection before processing
            $this->detectAnomalousBehavior($request);

            // Call the parent method to maintain original functionality
            return parent::submitApplication($request);
        } catch (ValidationException $e) {
            Log::error('Validation failed in secure submission: ' . $e->getMessage(), [
                'ip' => $request->ip(),
                'user_id' => Auth::id() ?? 'guest'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Secure application submission failed: ' . $e->getMessage(), [
                'ip' => $request->ip(),
                'user_id' => Auth::id() ?? 'guest'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit application: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Detect anomalous behavior based on request patterns
     */
    protected function detectAnomalousBehavior(Request $request)
    {
        $ip = $request->ip();
        $userId = Auth::id() ?? 'guest';
        $cacheKey = 'anomaly_detection_' . md5($ip . '_' . $userId);
        $currentTime = Carbon::now();

        // Get or initialize anomaly data
        $anomalyData = Cache::get($cacheKey, [
            'submission_count' => 0,
            'last_submission' => null,
            'suspicious_patterns' => []
        ]);

        // Increment submission count
        $anomalyData['submission_count']++;
        $anomalyData['last_submission'] = $currentTime->toIso8601String();

        // Check for rapid submissions (e.g., within 30 seconds)
        if ($anomalyData['last_submission']) {
            $lastSubmission = Carbon::parse($anomalyData['last_submission']);
            if ($currentTime->diffInSeconds($lastSubmission) < 30) {
                $anomalyData['suspicious_patterns'][] = 'rapid_submission';
                Log::warning('Rapid submission detected', [
                    'ip' => $ip,
                    'user_id' => $userId,
                    'time_diff' => $currentTime->diffInSeconds($lastSubmission)
                ]);
            }
        }

        // Check for excessive submissions (e.g., more than 5 in 10 minutes)
        if ($anomalyData['submission_count'] > 5) {
            $anomalyData['suspicious_patterns'][] = 'excessive_submissions';
            Log::warning('Excessive submissions detected', [
                'ip' => $ip,
                'user_id' => $userId,
                'submission_count' => $anomalyData['submission_count']
            ]);
        }

        // Check for suspicious user agent
        $userAgent = $request->header('User-Agent', 'unknown');
        if (empty($userAgent) || strpos($userAgent, 'bot') !== false || strpos($userAgent, 'crawler') !== false) {
            $anomalyData['suspicious_patterns'][] = 'suspicious_user_agent';
            Log::warning('Suspicious user agent detected', [
                'ip' => $ip,
                'user_id' => $userId,
                'user_agent' => $userAgent
            ]);
        }

        // Block request if too many suspicious patterns
        if (count($anomalyData['suspicious_patterns']) >= 2) {
            Log::alert('Anomalous behavior detected, blocking request', [
                'ip' => $ip,
                'user_id' => $userId,
                'patterns' => $anomalyData['suspicious_patterns']
            ]);
            throw new \Exception('Suspicious activity detected. Please try again later.');
        }

        // Store updated anomaly data with 10-minute TTL
        Cache::put($cacheKey, $anomalyData, 600);
    }

    /**
     * Override getApplications to add security logging
     */
    public function getApplications(Request $request)
    {
        Log::info('Secure fetch applications attempt', [
            'ip' => $request->ip(),
            'user_id' => Auth::id() ?? 'guest'
        ]);
        return parent::getApplications($request);
    }

    /**
     * Override viewApplication to add security logging
     */
    public function viewApplication(Request $request, $id)
    {
        Log::info('Secure view application attempt', [
            'ip' => $request->ip(),
            'application_id' => $id,
            'user_id' => Auth::id() ?? 'guest'
        ]);
        return parent::viewApplication($request, $id);
    }

    /**
     * Override approveApplication to add security logging
     */
    public function approveApplication(Request $request, $id)
    {
        Log::info('Secure approve application attempt', [
            'ip' => $request->ip(),
            'application_id' => $id,
            'user_id' => Auth::id() ?? 'guest'
        ]);
        return parent::approveApplication($request, $id);
    }

    /**
     * Override rejectApplication to add security logging
     */
    public function rejectApplication(Request $request, $id)
    {
        Log::info('Secure reject application attempt', [
            'ip' => $request->ip(),
            'application_id' => $id,
            'user_id' => Auth::id() ?? 'guest'
        ]);
        return parent::rejectApplication($request, $id);
    }

    /**
     * Override exportApplicationsIssueToken to add security logging
     */
    public function exportApplicationsIssueToken(Request $request)
    {
        Log::info('Secure export applications token issue attempt', [
            'ip' => $request->ip(),
            'user_id' => Auth::id() ?? 'guest'
        ]);
        return parent::exportApplicationsIssueToken($request);
    }

    /**
     * Override exportApplicationsDownload to add security logging
     */
    public function exportApplicationsDownload(Request $request)
    {
        Log::info('Secure export applications download attempt', [
            'ip' => $request->ip(),
            'user_id' => Auth::id() ?? 'guest',
            'token' => $request->token
        ]);
        return parent::exportApplicationsDownload($request);
    }

    /**
     * Override getAdminStats to add security logging
     */
    public function getAdminStats(Request $request)
    {
        Log::info('Secure admin stats fetch attempt', [
            'ip' => $request->ip(),
            'user_id' => Auth::id() ?? 'guest'
        ]);
        return parent::getAdminStats($request);
    }

    /**
     * Override getAllApplicationsForAdmin to add security logging
     */
    public function getAllApplicationsForAdmin(Request $request)
    {
        Log::info('Secure admin all applications fetch attempt', [
            'ip' => $request->ip(),
            'user_id' => Auth::id() ?? 'guest'
        ]);
        return parent::getAllApplicationsForAdmin($request);
    }

    /**
     * Override getAdminAllStats to add security logging
     */
    public function getAdminAllStats(Request $request)
    {
        Log::info('Secure admin all stats fetch attempt', [
            'ip' => $request->ip(),
            'user_id' => Auth::id() ?? 'guest'
        ]);
        return parent::getAdminAllStats($request);
    }

    /**
     * Override schoolsData to add security logging
     */
    public function schoolsData()
    {
        Log::info('Secure schools data fetch attempt', [
            'ip' => request()->ip(),
            'user_id' => Auth::id() ?? 'guest'
        ]);
        return parent::schoolsData();
    }
}