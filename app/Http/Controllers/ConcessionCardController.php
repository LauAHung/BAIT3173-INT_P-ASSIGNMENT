<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Models\ConcessionApplication;

class ConcessionCardController extends Controller
{
    public function getApplications(Request $request)
    {
        try {
            // Get applications from database
            $applications = ConcessionApplication::orderBy('created_at', 'desc')->get();
            
            // Transform data to match frontend format
            $transformedApplications = $applications->map(function ($app) {
                $data = [
                    'id' => $app->application_id,
                    'type' => $app->type,
                    'fullName' => $app->full_name,
                    'ic' => $app->ic_number,
                    'status' => $app->status,
                    'applicationDate' => $app->created_at->toIso8601String()
                ];

                // Add type-specific data
                if ($app->type === 'oku') {
                    $data['okuCardNumber'] = $app->oku_card_number;
                    $data['disability'] = $app->disability_info;
                    $data['passportNumber'] = $app->passport_number;
                } elseif ($app->type === 'senior') {
                    $data['age'] = $app->age;
                    $data['citizenship'] = $app->citizenship;
                    $data['gender'] = $app->gender;
                    $data['dateOfBirth'] = $app->date_of_birth;
                } elseif ($app->type === 'student') {
                    $data['matrixNumber'] = $app->matrix_number;
                    $data['schoolName'] = $app->school_name;
                    $data['studentCitizenship'] = $app->citizenship;
                    $data['educationLevel'] = $app->education_level;
                    if ($app->student_id_photo_path) {
                        $data['photoName'] = basename($app->student_id_photo_path);
                        $data['photoUrl'] = asset('storage/' . $app->student_id_photo_path);
                    }
                }

                return $data;
            });

            return response()->json([
                'success' => true,
                'applications' => $transformedApplications
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching applications: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications'
            ], 500);
        }
    }

    public function submitApplication(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:oku,senior,student',
                'fullName' => 'required|string|max:255',
                'ic' => 'required|string|size:12|regex:/^\d+$/',
                'okuCardNumber' => 'required_if:type,oku|nullable|string|min:8',
                'disability' => 'required_if:type,oku|nullable|string',
                'age' => 'required_if:type,senior|nullable|integer|min:60',
                'citizenship' => 'required_if:type,senior|nullable|string',
                'matrixNumber' => 'required_if:type,student|nullable|string|min:4',
                'schoolName' => 'required_if:type,student|nullable|string',
                'studentIdPhoto' => 'required_if:type,student|nullable|image|max:2048',
                'passportNumber' => 'nullable|string',
                'gender' => 'required_if:type,senior|nullable|string|in:male,female',
                'dateOfBirth' => 'nullable|date',
                'studentCitizenship' => 'required_if:type,student|nullable|string',
                'educationLevel' => 'required_if:type,student|nullable|string|in:primary,secondary,college,university'
            ]);

            // Generate unique application ID
            $applicationId = ConcessionApplication::generateApplicationId();

            // Prepare data for database
            $applicationData = [
                'application_id' => $applicationId,
                'type' => $request->type,
                'full_name' => $request->fullName,
                'ic_number' => $request->ic,
                'status' => 'pending'
            ];

            // Handle file upload for student ID photo
            if ($request->hasFile('studentIdPhoto')) {
                $path = $request->file('studentIdPhoto')->store('student_photos', 'public');
                $applicationData['student_id_photo_path'] = $path;
            }

            // Add type-specific fields
            if ($request->type === 'oku') {
                $applicationData['oku_card_number'] = $request->okuCardNumber;
                $applicationData['disability_info'] = $request->disability;
                $applicationData['passport_number'] = $request->passportNumber;
                $applicationData['citizenship'] = $request->citizenship ?? 'MY';
            } elseif ($request->type === 'senior') {
                $applicationData['age'] = (int)$request->age;
                $applicationData['citizenship'] = $request->citizenship;
                $applicationData['gender'] = $request->gender;
                $applicationData['date_of_birth'] = $request->dateOfBirth;
            } elseif ($request->type === 'student') {
                $applicationData['matrix_number'] = $request->matrixNumber;
                $applicationData['school_name'] = $request->schoolName;
                $applicationData['citizenship'] = $request->studentCitizenship;
                $applicationData['education_level'] = $request->educationLevel;
            }

            // Create application in database
            $application = ConcessionApplication::create($applicationData);

            // Return response in the format expected by frontend
            $responseData = [
                'id' => $application->application_id,
                'type' => $application->type,
                'fullName' => $application->full_name,
                'ic' => $application->ic_number,
                'status' => $application->status,
                'applicationDate' => $application->created_at->toIso8601String()
            ];

            // Add type-specific data to response
            if ($application->type === 'oku') {
                $responseData['okuCardNumber'] = $application->oku_card_number;
                $responseData['disability'] = $application->disability_info;
                $responseData['passportNumber'] = $application->passport_number;
            } elseif ($application->type === 'senior') {
                $responseData['age'] = $application->age;
                $responseData['citizenship'] = $application->citizenship;
                $responseData['gender'] = $application->gender;
                $responseData['dateOfBirth'] = $application->date_of_birth;
            } elseif ($application->type === 'student') {
                $responseData['matrixNumber'] = $application->matrix_number;
                $responseData['schoolName'] = $application->school_name;
                $responseData['studentCitizenship'] = $application->citizenship;
                $responseData['educationLevel'] = $application->education_level;
                if ($application->student_id_photo_path) {
                    $responseData['photoName'] = basename($application->student_id_photo_path);
                    $responseData['photoUrl'] = asset('storage/' . $application->student_id_photo_path);
                }
            }

            return response()->json([
                'success' => true,
                'application' => $responseData
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Application submission failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function viewApplication(Request $request, $id)
    {
        try {
            $application = ConcessionApplication::where('application_id', $id)->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Transform data to match frontend format
            $data = [
                'id' => $application->application_id,
                'type' => $application->type,
                'fullName' => $application->full_name,
                'ic' => $application->ic_number,
                'status' => $application->status,
                'applicationDate' => $application->created_at->toIso8601String()
            ];

            // Add type-specific data
            if ($application->type === 'oku') {
                $data['okuCardNumber'] = $application->oku_card_number;
                $data['disability'] = $application->disability_info;
                $data['passportNumber'] = $application->passport_number;
            } elseif ($application->type === 'senior') {
                $data['age'] = $application->age;
                $data['citizenship'] = $application->citizenship;
                $data['gender'] = $application->gender;
                $data['dateOfBirth'] = $application->date_of_birth;
            } elseif ($application->type === 'student') {
                $data['matrixNumber'] = $application->matrix_number;
                $data['schoolName'] = $application->school_name;
                $data['studentCitizenship'] = $application->citizenship;
                $data['educationLevel'] = $application->education_level;
                if ($application->student_id_photo_path) {
                    $data['photoName'] = basename($application->student_id_photo_path);
                    $data['photoUrl'] = asset('storage/' . $application->student_id_photo_path);
                }
            }

            return response()->json([
                'success' => true,
                'application' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error viewing application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to view application'
            ], 500);
        }
    }

    public function approveApplication(Request $request, $id)
    {
        try {
            $application = ConcessionApplication::where('application_id', $id)->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $application->update([
                'status' => 'approved',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'admin_notes' => $request->input('notes', '')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error approving application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application'
            ], 500);
        }
    }

    public function rejectApplication(Request $request, $id)
    {
        try {
            $application = ConcessionApplication::where('application_id', $id)->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $application->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'reviewed_by' => Auth::id(),
                'admin_notes' => $request->input('notes', '')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error rejecting application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application'
            ], 500);
        }
    }

    public function getAdminStats(Request $request)
    {
        try {
            $stats = [
                'total' => ConcessionApplication::count(),
                'pending' => ConcessionApplication::pending()->count(),
                'approved' => ConcessionApplication::approved()->count(),
                'rejected' => ConcessionApplication::rejected()->count()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching admin stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admin stats'
            ], 500);
        }
    }
}