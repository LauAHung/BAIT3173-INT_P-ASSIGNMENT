<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ConcessionCardController extends Controller
{
    public function getApplications(Request $request)
    {
        try {
            // Load applications from session or storage
            $applications = session('concessionApplications', []);
            return response()->json([
                'success' => true,
                'applications' => $applications
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

            $application = [
                'id' => 'APP' . time(),
                'type' => $request->type,
                'fullName' => $request->fullName,
                'ic' => $request->ic,
                'status' => 'pending',
                'applicationDate' => now()->toIso8601String()
            ];

            if ($request->type === 'oku') {
                $application['okuCardNumber'] = $request->okuCardNumber;
                $application['disability'] = $request->disability;
                $application['passportNumber'] = $request->passportNumber;
            } elseif ($request->type === 'senior') {
                $application['age'] = (int)$request->age;
                $application['citizenship'] = $request->citizenship;
                $application['gender'] = $request->gender;
                $application['dateOfBirth'] = $request->dateOfBirth;
            } elseif ($request->type === 'student') {
                $application['matrixNumber'] = $request->matrixNumber;
                $application['schoolName'] = $request->schoolName;
                $application['studentCitizenship'] = $request->studentCitizenship;
                $application['educationLevel'] = $request->educationLevel;
                if ($request->hasFile('studentIdPhoto')) {
                    $path = $request->file('studentIdPhoto')->store('student_photos', 'public');
                    $application['photoName'] = basename($path);
                }
            }

            $applications = session('concessionApplications', []);
            $applications[] = $application;
            session(['concessionApplications' => $applications]);

            return response()->json([
                'success' => true,
                'application' => $application
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
            $applications = session('concessionApplications', []);
            $application = collect($applications)->firstWhere('id', $id);

            if (!$application) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'application' => $application
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
            $applications = session('concessionApplications', []);
            $index = collect($applications)->search(function ($app) use ($id) {
                return $app['id'] === $id;
            });

            if ($index === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $applications[$index]['status'] = 'approved';
            session(['concessionApplications' => $applications]);

            return response()->json([
                'success' => true,
                'application' => $applications[$index]
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
            $applications = session('concessionApplications', []);
            $index = collect($applications)->search(function ($app) use ($id) {
                return $app['id'] === $id;
            });

            if ($index === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $applications[$index]['status'] = 'rejected';
            session(['concessionApplications' => $applications]);

            return response()->json([
                'success' => true,
                'application' => $applications[$index]
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
            $applications = session('concessionApplications', []);
            $stats = [
                'total' => count($applications),
                'pending' => count(array_filter($applications, fn($app) => $app['status'] === 'pending')),
                'approved' => count(array_filter($applications, fn($app) => $app['status'] === 'approved')),
                'rejected' => count(array_filter($applications, fn($app) => $app['status'] === 'rejected'))
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