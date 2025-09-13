<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\Train;
use App\Models\AdminActivityLog;
use App\Models\Journey;
use App\Models\NewsletterSubscriber;
use App\Models\ConcessionApplication;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminWebServiceController extends Controller
{
    /**
     * Get User Information by User ID
     * REST API: GET /api/admin/user/{userId}
     */
    public function getUserInfo(Request $request, $userId)
    {
        $validator = Validator::make(['userId' => $userId], [
            'userId' => 'required|string|regex:/^[a-zA-Z0-9]+$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid userId format. Only alphabets and numbers allowed.',
                'data' => null
            ], 400);
        }

        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'status' => 'I',
                'message' => 'User not found',
                'data' => null
            ], 404);
        }

        $queryFlag = $request->get('queryFlag', 1);
        
        $response = [
            'status' => $user->status === 'active' ? 'A' : 'I',
            'userName' => $user->name,
            'userEmail' => $user->email,
            'userDetails' => []
        ];

        // Based on queryFlag, include different information
        if ($queryFlag == 1 || $queryFlag == 3) {
            $response['userDetails']['HpNo'] = $user->phone ?? 'N/A';
        }
        
        if ($queryFlag == 2 || $queryFlag == 3) {
            $response['userDetails']['HouseAdd'] = $user->address ?? 'N/A';
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User information retrieved successfully',
            'data' => $response
        ]);
    }

    /**
     * Get Concession Application Details
     * REST API: GET /api/admin/concession/{applicationId}
     */
    public function getConcessionApplication($applicationId)
    {
        $application = ConcessionApplication::with('user')->find($applicationId);
        
        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Application details retrieved successfully',
            'data' => [
                'applicationId' => $application->application_id,
                'type' => $application->type,
                'fullName' => $application->full_name,
                'status' => $application->status,
                'appliedDate' => $application->created_at->toISOString(),
                'userDetails' => [
                    'userId' => $application->user_id,
                    'userName' => $application->user->name,
                    'userEmail' => $application->user->email
                ]
            ]
        ]);
    }

    /**
     * Get Train Information
     * REST API: GET /api/admin/train/{trainId}
     */
    public function getTrainInfo($trainId)
    {
        $train = Train::find($trainId);
        
        if (!$train) {
            return response()->json([
                'status' => 'error',
                'message' => 'Train not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Train information retrieved successfully',
            'data' => [
                'trainId' => $train->id,
                'trainName' => $train->name,
                'trainNumber' => $train->train_number,
                'status' => $train->status,
                'capacity' => $train->capacity,
                'route' => $train->route
            ]
        ]);
    }

    /**
     * Get Admin Activity Logs
     * REST API: GET /api/admin/logs
     */
    public function getAdminLogs(Request $request)
    {
        $queryFlag = $request->get('queryFlag', 1);
        $limit = $request->get('limit', 50);
        
        $logs = AdminActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        $response = [
            'status' => 'A',
            'totalLogs' => $logs->count(),
            'logs' => []
        ];

        foreach ($logs as $log) {
            $logData = [
                'logId' => $log->id,
                'action' => $log->action,
                'timestamp' => $log->created_at->toISOString()
            ];

            if ($queryFlag == 1 || $queryFlag == 3) {
                $logData['userDetails'] = [
                    'userId' => $log->user_id,
                    'userName' => $log->user->name ?? 'Unknown'
                ];
            }

            if ($queryFlag == 2 || $queryFlag == 3) {
                $logData['details'] = $log->details;
                $logData['ipAddress'] = $log->ip_address;
            }

            $response['logs'][] = $logData;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Admin logs retrieved successfully',
            'data' => $response
        ]);
    }

    public function getJourneyById($journeyId)
    {
        $validator = Validator::make(['journeyId' => $journeyId], [
            'journeyId' => ['required','string','regex:/^[a-zA-Z0-9]+$/']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid JourneyID format. Only alphabets and numbers allowed.',
                'data' => null
            ], 400);
        }

        $journey = Journey::with('train')->where('JourneyID', $journeyId)->first();
        if (!$journey) {
            return response()->json([
                'status' => 'error',
                'message' => 'Journey not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Journey details retrieved successfully',
            'data' => [
                'TrainID' => (string) $journey->TrainID,
                'FromLocation' => (string) $journey->FromLocation,
                'ToLocation' => (string) $journey->ToLocation,
                'DepartureTime' => optional($journey->DepartureTime)->format('Y-m-d H:i:s'),
                'ArrivalTime' => optional($journey->ArrivalTime)->format('Y-m-d H:i:s'),
                'SeatAvailable' => (int) $journey->SeatAvailable,
                'Price' => number_format((float) $journey->Price, 2, '.', ''),
            ]
        ]);
    }

    /**
     * Get Newsletter Subscribers
     * REST API: GET /api/admin/newsletter/subscribers
     */
    public function getNewsletterSubscribers(Request $request)
    {
        $queryFlag = $request->get('queryFlag', 1);
        $limit = $request->get('limit', 100);
        
        $subscribers = NewsletterSubscriber::limit($limit)->get();

        $response = [
            'status' => 'A',
            'totalSubscribers' => $subscribers->count(),
            'subscribers' => []
        ];

        foreach ($subscribers as $subscriber) {
            $subscriberData = [
                'subscriberId' => $subscriber->id,
                'email' => $subscriber->email,
                'subscribedAt' => $subscriber->created_at->toISOString()
            ];

            if ($queryFlag == 2 || $queryFlag == 3) {
                $subscriberData['status'] = $subscriber->status;
                $subscriberData['preferences'] = $subscriber->preferences ?? [];
            }

            $response['subscribers'][] = $subscriberData;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Newsletter subscribers retrieved successfully',
            'data' => $response
        ]);
    }

    
    public function decideConcession(Request $request)
    {
        $request->validate([
            'applicationId' => ['required','string'],
            'decision' => ['required','in:approve,reject'],
            'remark' => ['nullable','string','max:500']
        ]);

        $application = \App\Models\ConcessionApplication::where('application_id', $request->applicationId)->first();
        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found',
                'data' => null
            ], 404);
        }

        $newStatus = $request->decision === 'approve' ? 'approved' : 'rejected';
        $application->status = $newStatus;
        if ($request->filled('remark')) {
            $application->admin_notes = $request->remark;
        }
        $application->reviewed_at = now();
        $application->reviewed_by = Auth::id();
        $application->save();

        // optional audit
        try {
            Log::info('concession_decision', [
                'application_id' => $application->application_id,
                'status' => $newStatus,
                'reviewed_by' => Auth::id(),
            ]);
        } catch (\Throwable $e) {}

        return response()->json([
            'status' => 'success',
            'message' => 'Decision recorded',
            'data' => [
                'applicationId' => $application->application_id,
                'status' => $application->status,
                'reviewedAt' => optional($application->reviewed_at)->toISOString(),
            ]
        ]);
    }
}







