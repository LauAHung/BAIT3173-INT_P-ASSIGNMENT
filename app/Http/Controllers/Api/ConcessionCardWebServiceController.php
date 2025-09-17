<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\ConcessionApplication;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ConcessionCardWebServiceController extends Controller
{
    /**
     * Get Concession Applications by User ID
     * REST API: GET /api/concession/user/{userId}
     */
    public function getUserApplications($userId)
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

        $applications = ConcessionApplication::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $transformedApplications = $applications->map(function ($app) {
            return [
                'applicationId' => $app->application_id,
                'type' => $app->type,
                'fullName' => $app->full_name,
                'status' => $app->status,
                'appliedDate' => $app->created_at->toIso8601String(),
                'icNumber' => $app->ic_number,
                'passportNumber' => $app->passport_number
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'User applications retrieved successfully',
            'data' => [
                'userId' => $userId,
                'totalApplications' => $applications->count(),
                'applications' => $transformedApplications
            ]
        ]);
    }

    /**
     * Get Concession Application Details
     * REST API: GET /api/concession/application/{applicationId}
     */
    public function getApplicationDetails($applicationId)
    {
        $application = ConcessionApplication::with('user')->find($applicationId);
        
        if (!$application) {
            return response()->json([
                'status' => 'error',
                'message' => 'Application not found',
                'data' => null
            ], 404);
        }

        $response = [
            'applicationId' => $application->application_id,
            'type' => $application->type,
            'fullName' => $application->full_name,
            'status' => $application->status,
            'appliedDate' => $application->created_at->toIso8601String(),
            'userDetails' => [
                'userId' => $application->user_id,
                'userName' => $application->user->name,
                'userEmail' => $application->user->email
            ]
        ];

        // Add type-specific details
        if ($application->type === 'oku') {
            $response['okuDetails'] = [
                'okuCardNumber' => $application->oku_card_number,
                'disabilityType' => $application->disability_type,
                'disabilityInfo' => $application->disability_info,
                'okuCardPhoto' => $application->oku_card_photo_path
            ];
        } elseif ($application->type === 'senior') {
            $response['seniorDetails'] = [
                'age' => $application->age,
                'gender' => $application->gender,
                'citizenship' => $application->citizenship,
                'dateOfBirth' => $application->date_of_birth,
                'icPhoto' => $application->senior_ic_photo_path
            ];
        } elseif ($application->type === 'student') {
            $response['studentDetails'] = [
                'matrixNumber' => $application->matrix_number,
                'schoolName' => $application->school_name,
                'citizenship' => $application->citizenship,
                'educationLevel' => $application->education_level,
                'studentIdPhoto' => $application->student_id_photo_path
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Application details retrieved successfully',
            'data' => $response
        ]);
    }

    /**
     * Submit Concession Application
     * REST API: POST /api/concession/application
     */
    public function submitApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:oku,senior,student',
            'fullName' => 'required|string|max:255',
            'ic' => 'required|string|max:12',
            'passportNumber' => 'nullable|string|max:20',
            'okuCardNumber' => 'required_if:type,oku|nullable|string',
            'disabilityType' => 'required_if:type,oku|nullable|string',
            'disabilityInfo' => 'nullable|string',
            'age' => 'required_if:type,senior|nullable|integer',
            'gender' => 'required_if:type,senior|nullable|string',
            'citizenship' => 'required_if:type,student|nullable|string',
            'matrixNumber' => 'required_if:type,student|nullable|string',
            'schoolName' => 'required_if:type,student|nullable|string',
            'educationLevel' => 'required_if:type,student|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'data' => null
            ], 400);
        }

        // Get user ID from authenticated user or request
        $userId = Auth::id() ?? $request->get('userId');
        
        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'User authentication required',
                'data' => null
            ], 401);
        }

        $applicationData = [
            'user_id' => $userId,
            'type' => $request->type,
            'full_name' => $request->fullName,
            'ic_number' => $request->ic,
            'passport_number' => $request->passportNumber,
            'status' => 'pending'
        ];

        // Add type-specific data
        if ($request->type === 'oku') {
            $applicationData['oku_card_number'] = $request->okuCardNumber;
            $applicationData['disability_type'] = $request->disabilityType;
            $applicationData['disability_info'] = $request->disabilityInfo;
        } elseif ($request->type === 'senior') {
            $applicationData['age'] = $request->age;
            $applicationData['gender'] = $request->gender;
            $applicationData['citizenship'] = 'Malaysian';
            $applicationData['date_of_birth'] = $request->dateOfBirth;
        } elseif ($request->type === 'student') {
            $applicationData['matrix_number'] = $request->matrixNumber;
            $applicationData['school_name'] = $request->schoolName;
            $applicationData['citizenship'] = $request->citizenship;
            $applicationData['education_level'] = $request->educationLevel;
        }

        $application = ConcessionApplication::create($applicationData);

        return response()->json([
            'status' => 'success',
            'message' => 'Application submitted successfully',
            'data' => [
                'applicationId' => $application->application_id,
                'type' => $application->type,
                'status' => $application->status,
                'appliedDate' => $application->created_at->toIso8601String()
            ]
        ], 201);
    }

    /**
     * Get Concession Application Statistics
     * REST API: GET /api/concession/statistics
     */
    public function getStatistics(Request $request)
    {
        $queryFlag = $request->get('queryFlag', 1);
        
        $totalApplications = ConcessionApplication::count();
        $pendingApplications = ConcessionApplication::where('status', 'pending')->count();
        $approvedApplications = ConcessionApplication::where('status', 'approved')->count();
        $rejectedApplications = ConcessionApplication::where('status', 'rejected')->count();

        $response = [
            'status' => 'A',
            'totalApplications' => $totalApplications,
            'pendingApplications' => $pendingApplications,
            'approvedApplications' => $approvedApplications,
            'rejectedApplications' => $rejectedApplications
        ];

        if ($queryFlag == 2 || $queryFlag == 3) {
            $response['typeBreakdown'] = [
                'oku' => ConcessionApplication::where('type', 'oku')->count(),
                'senior' => ConcessionApplication::where('type', 'senior')->count(),
                'student' => ConcessionApplication::where('type', 'student')->count()
            ];
        }

        if ($queryFlag == 3) {
            $response['recentApplications'] = ConcessionApplication::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($app) {
                    return [
                        'applicationId' => $app->application_id,
                        'type' => $app->type,
                        'fullName' => $app->full_name,
                        'status' => $app->status,
                        'appliedDate' => $app->created_at->toIso8601String(),
                        'userName' => $app->user->name
                    ];
                });
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Statistics retrieved successfully',
            'data' => $response
        ]);
    }

    /**
     * Get All Applications for Admin
     * REST API: GET /api/concession/applications
     */
    public function getAllApplications()
    {
        try {
            $applications = ConcessionApplication::with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            $transformedApplications = $applications->map(function ($app) {
                return [
                    'id' => $app->application_id,
                    'type' => $app->type,
                    'fullName' => $app->full_name,
                    'status' => $app->status,
                    'applicationDate' => $app->created_at->toIso8601String(),
                    'icNumber' => $app->ic_number,
                    'passportNumber' => $app->passport_number,
                    'userDetails' => [
                        'userId' => $app->user_id,
                        'userName' => $app->user ? $app->user->name : 'Unknown',
                        'userEmail' => $app->user ? $app->user->email : 'Unknown'
                    ]
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Applications retrieved successfully',
                'data' => $transformedApplications
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getAllApplications: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve applications: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Approve Application
     * REST API: POST /api/concession/applications/{applicationId}/approve
     */
    public function approveApplication(Request $request, $applicationId)
    {
        try {
            $application = ConcessionApplication::where('application_id', $applicationId)->first();
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $application->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'admin_notes' => $request->input('notes', '')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error approving application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject Application
     * REST API: POST /api/concession/applications/{applicationId}/reject
     */
    public function rejectApplication(Request $request, $applicationId)
    {
        try {
            $application = ConcessionApplication::where('application_id', $applicationId)->first();
            
            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $application->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => auth()->id(),
                'admin_notes' => $request->input('notes', '')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error rejecting application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application: ' . $e->getMessage()
            ], 500);
        }
    }
}



