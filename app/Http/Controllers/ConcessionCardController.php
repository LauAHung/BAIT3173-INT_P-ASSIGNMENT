<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\ConcessionApplication;

class ConcessionCardController extends Controller
{
    public function getApplications(Request $request)
    {
        try {
            $userId = Auth::id();
            Log::info('Getting applications for user ID: ' . $userId);
            
            // Get applications from database - only show user's own applications
            $applications = ConcessionApplication::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();
                
            Log::info('Found ' . $applications->count() . ' applications for user ' . $userId);
            
            // Transform data to match frontend format
            $transformedApplications = $applications->map(function ($app) {
                $data = [
                    'id' => $app->application_id,
                    'type' => $app->type,
                    'fullName' => $app->full_name,
                    'ic' => $app->ic_number,
                    'passportNumber' => $app->passport_number, // Always include passport number
                    'status' => $app->status,
                    'applicationDate' => $app->created_at->toIso8601String()
                ];

                // Add type-specific IC fields for admin page compatibility
                if ($app->type === 'student') {
                    $data['studentIc'] = $app->ic_number;
                } elseif ($app->type === 'senior') {
                    $data['seniorIc'] = $app->ic_number;
                }

                // Add type-specific data
                if ($app->type === 'oku') {
                    $data['okuCardNumber'] = $app->oku_card_number;
                    $data['disability'] = $app->disability_info;
                    $data['oku_card_photo_path'] = $app->oku_card_photo_path; // Add debug field
                    if ($app->oku_card_photo_path) {
                        $data['photoName'] = basename($app->oku_card_photo_path);
                        $data['photoUrl'] = asset('storage/' . $app->oku_card_photo_path);
                    }
                } elseif ($app->type === 'senior') {
                    $data['age'] = $app->age;
                    $data['citizenship'] = $app->citizenship;
                    $data['gender'] = $app->gender;
                    $data['dateOfBirth'] = $app->date_of_birth;
                    $data['senior_ic_photo_path'] = $app->senior_ic_photo_path; // Add debug field
                    if ($app->senior_ic_photo_path) {
                        $data['photoName'] = basename($app->senior_ic_photo_path);
                        $data['photoUrl'] = asset('storage/' . $app->senior_ic_photo_path);
                    }
                } elseif ($app->type === 'student') {
                    $data['matrixNumber'] = $app->matrix_number;
                    $data['schoolName'] = $app->school_name;
                    $data['studentCitizenship'] = $app->citizenship;
                    $data['educationLevel'] = $app->education_level;
                    $data['student_id_photo_path'] = $app->student_id_photo_path; // Add debug field
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
                'ic' => 'nullable|string|size:12|regex:/^\d+$/',
                'passportNumber' => 'nullable|string',
                'okuCardNumber' => 'required_if:type,oku|nullable|string|min:8',
                'disabilityType' => 'required_if:type,oku|nullable|string|in:visual,hearing,mobility,cognitive,other',
                'otherDisability' => 'required_if:disabilityType,other|nullable|string',
                'age' => 'required_if:type,senior|nullable|integer|min:60',
                'citizenship' => 'nullable|string',
                'matrixNumber' => 'required_if:type,student|nullable|string|min:4',
                'schoolName' => 'required_if:type,student|nullable|string',
                'studentIdPhoto' => 'required_if:type,student|nullable|image|max:2048',
                'gender' => 'required_if:type,senior|nullable|string|in:male,female',
                'dateOfBirth' => 'nullable|date',
                'studentCitizenship' => 'required_if:type,student|nullable|string',
                'educationLevel' => 'required_if:type,student|nullable|string|in:primary,secondary,college,university'
            ], [
                'ic_or_passport.required' => 'Either IC Number or Passport Number is required.'
            ]);

            // Generate unique application ID
            $applicationId = ConcessionApplication::generateApplicationId();

            // Prepare data for database
            $applicationData = [
                'user_id' => Auth::id(),
                'application_id' => $applicationId,
                'type' => $request->type,
                'full_name' => $request->fullName,
                'ic_number' => $this->getIcNumber($request),
                'passport_number' => $request->passportNumber, // Always include passport number
                'status' => 'pending'
            ];

            // Handle file upload for student ID photo
            if ($request->hasFile('studentIdPhoto')) {
                $path = $request->file('studentIdPhoto')->store('student_photos', 'public');
                $applicationData['student_id_photo_path'] = $path;
            }
            
            // Handle file upload for OKU card photo
            if ($request->hasFile('okuCardPhoto')) {
                $path = $request->file('okuCardPhoto')->store('oku_card_photos', 'public');
                $applicationData['oku_card_photo_path'] = $path;
            }
            
            // Handle file upload for senior IC photo
            if ($request->hasFile('seniorIcPhoto')) {
                $path = $request->file('seniorIcPhoto')->store('senior_ic_photos', 'public');
                $applicationData['senior_ic_photo_path'] = $path;
            }

            // Add type-specific fields
            if ($request->type === 'oku') {
                $applicationData['oku_card_number'] = $request->okuCardNumber;
                $applicationData['disability_info'] = $request->disability;
                $applicationData['citizenship'] = $request->citizenship ?? 'MY';
            } elseif ($request->type === 'senior') {
                // Use parsed data from frontend
                $applicationData['age'] = (int)$request->age;
                $applicationData['citizenship'] = $request->citizenship ?? 'Malaysia';
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
                'passportNumber' => $application->passport_number, // Always include passport number
                'status' => $application->status,
                'applicationDate' => $application->created_at->toIso8601String()
            ];

            // Add type-specific data to response
            if ($application->type === 'oku') {
                $responseData['okuCardNumber'] = $application->oku_card_number;
                $responseData['disability'] = $application->disability_info;
            } elseif ($application->type === 'senior') {
                $responseData['age'] = $application->age;
                $responseData['citizenship'] = $application->citizenship;
                $responseData['gender'] = $application->gender;
                $responseData['dateOfBirth'] = $application->date_of_birth;
                if ($application->senior_ic_photo_path) {
                    $responseData['photoName'] = basename($application->senior_ic_photo_path);
                    $responseData['photoUrl'] = asset('storage/' . $application->senior_ic_photo_path);
                }
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
            $application = ConcessionApplication::where('application_id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found or access denied'
                ], 404);
            }

            // Transform data to match frontend format
            $data = [
                'id' => $application->application_id,
                'type' => $application->type,
                'fullName' => $application->full_name,
                'ic' => $application->ic_number,
                'passportNumber' => $application->passport_number, // Always include passport number
                'status' => $application->status,
                'applicationDate' => $application->created_at->toIso8601String()
            ];

            // Add type-specific IC fields for admin page compatibility
            if ($application->type === 'student') {
                $data['studentIc'] = $application->ic_number;
            } elseif ($application->type === 'senior') {
                $data['seniorIc'] = $application->ic_number;
            }

            // Add type-specific data
            if ($application->type === 'oku') {
                $data['okuCardNumber'] = $application->oku_card_number;
                $data['disability'] = $application->disability_info;
            } elseif ($application->type === 'senior') {
                $data['age'] = $application->age;
                $data['citizenship'] = $application->citizenship;
                $data['gender'] = $application->gender;
                $data['dateOfBirth'] = $application->date_of_birth;
                if ($application->senior_ic_photo_path) {
                    $data['photoName'] = basename($application->senior_ic_photo_path);
                    $data['photoUrl'] = asset('storage/' . $application->senior_ic_photo_path);
                }
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
            // For admin approval, don't restrict by user_id
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
            // For admin rejection, don't restrict by user_id
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
            $userId = Auth::id();
            $stats = [
                'total' => ConcessionApplication::where('user_id', $userId)->count(),
                'pending' => ConcessionApplication::where('user_id', $userId)->pending()->count(),
                'approved' => ConcessionApplication::where('user_id', $userId)->approved()->count(),
                'rejected' => ConcessionApplication::where('user_id', $userId)->rejected()->count()
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

    /**
     * Get all applications for admin approval page
     * This method returns ALL applications regardless of user
     */
    public function getAllApplicationsForAdmin(Request $request)
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }
            
            // For now, allow all authenticated users to access admin data
            // TODO: Add proper admin role checking

            // Get all applications from database
            $applications = ConcessionApplication::with('user')
                ->orderBy('created_at', 'desc')
                ->get();
                
            Log::info('Found ' . $applications->count() . ' applications for admin review');
            Log::info('Current user ID: ' . Auth::id());
            Log::info('Applications data: ' . json_encode($applications->toArray()));
            
            // Transform data to match frontend format
            $transformedApplications = $applications->map(function ($app) {
                $data = [
                    'id' => $app->application_id,
                    'type' => $app->type,
                    'fullName' => $app->full_name,
                    'ic' => $app->ic_number,
                    'passportNumber' => $app->passport_number,
                    'status' => $app->status,
                    'applicationDate' => $app->created_at->toIso8601String(),
                    'userId' => $app->user_id,
                    'userName' => $app->user->name ?? 'Unknown User',
                    'reviewedBy' => $app->reviewed_by,
                    'reviewedAt' => $app->reviewed_at ? $app->reviewed_at->toIso8601String() : null,
                    'adminNotes' => $app->admin_notes
                ];

                // Add type-specific IC fields for admin page compatibility
                if ($app->type === 'student') {
                    $data['studentIc'] = $app->ic_number;
                } elseif ($app->type === 'senior') {
                    $data['seniorIc'] = $app->ic_number;
                }

                // Add type-specific data
                if ($app->type === 'oku') {
                    $data['okuCardNumber'] = $app->oku_card_number;
                    $data['disability'] = $app->disability_info;
                    $data['oku_card_photo_path'] = $app->oku_card_photo_path;
                    if ($app->oku_card_photo_path) {
                        $data['photoName'] = basename($app->oku_card_photo_path);
                        $data['photoUrl'] = asset('storage/' . $app->oku_card_photo_path);
                    }
                } elseif ($app->type === 'senior') {
                    $data['age'] = $app->age;
                    $data['citizenship'] = $app->citizenship;
                    $data['gender'] = $app->gender;
                    $data['dateOfBirth'] = $app->date_of_birth;
                    $data['senior_ic_photo_path'] = $app->senior_ic_photo_path;
                    if ($app->senior_ic_photo_path) {
                        $data['photoName'] = basename($app->senior_ic_photo_path);
                        $data['photoUrl'] = asset('storage/' . $app->senior_ic_photo_path);
                    }
                } elseif ($app->type === 'student') {
                    $data['matrixNumber'] = $app->matrix_number;
                    $data['schoolName'] = $app->school_name;
                    $data['studentCitizenship'] = $app->citizenship;
                    $data['educationLevel'] = $app->education_level;
                    $data['student_id_photo_path'] = $app->student_id_photo_path;
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
            Log::error('Error fetching all applications for admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications'
            ], 500);
        }
    }

    /**
     * Get admin statistics for all applications
     */
    public function getAdminAllStats(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }
            
            // For now, allow all authenticated users to access admin data
            // TODO: Add proper admin role checking

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
            Log::error('Error fetching admin all stats: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch admin stats'
            ], 500);
        }
    }

    private function getIcNumber($request)
    {
        // Return the correct IC number based on application type
        if ($request->type === 'student') {
            return $request->studentIc ?? $request->ic;
        } elseif ($request->type === 'senior') {
            return $request->seniorIc ?? $request->ic;
        } else {
            // OKU uses the standard ic field
            return $request->ic;
        }
    }
}