<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use App\Models\ConcessionApplication;
use SplFileObject;

interface ApplicationProcessorInterface {
    public function process(Request $request);
}

class BasicApplicationProcessor implements ApplicationProcessorInterface {
    public function process(Request $request) {
        $applicationId = ConcessionApplication::generateApplicationId();
        $applicationData = [
            'user_id' => Auth::id(),
            'application_id' => $applicationId,
            'type' => $request->type,
            'full_name' => $request->fullName,
            'status' => 'pending',
            'submission_ip' => $request->ip(), // Store IP for anomaly detection
            'submission_city' => $request->submission_city ?? null, // Store city from anomaly detection
        ];
        $application = ConcessionApplication::create($applicationData);
        return $application;
    }
}

class RecaptchaValidator implements ApplicationProcessorInterface {
    protected $processor;
    protected $secretKey;

    public function __construct(ApplicationProcessorInterface $processor, $secretKey) {
        $this->processor = $processor;
        $this->secretKey = $secretKey;
    }

    public function process(Request $request) {
        $recaptchaToken = $request->header('X-Recaptcha-Token');
        if (!$recaptchaToken) {
            Log::warning('reCAPTCHA token missing', ['user_id' => Auth::id() ?? 'guest']);
            throw ValidationException::withMessages(['recaptcha' => 'reCAPTCHA token is required']);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $this->secretKey,
            'response' => $recaptchaToken,
            'remoteip' => $request->ip()
        ]);

        $result = $response->json();
        if (!$result['success'] || $result['score'] < 0.5) {
            Log::warning('reCAPTCHA verification failed', [
                'user_id' => Auth::id() ?? 'guest',
                'score' => $result['score'] ?? null,
                'errors' => $result['error-codes'] ?? []
            ]);
            throw ValidationException::withMessages(['recaptcha' => 'reCAPTCHA verification failed']);
        }

        return $this->processor->process($request);
    }
}

class AnomalyDetectionDecorator implements ApplicationProcessorInterface {
    protected $processor;

    public function __construct(ApplicationProcessorInterface $processor) {
        $this->processor = $processor;
    }

    public function process(Request $request) {
        $icNumber = $request->ic ?? $request->seniorIc ?? $request->studentIc ?? null;
        
        if ($icNumber) {
            // Get city from IP using IPinfo API
            $ip = $request->ip();
            $city = $this->getCityFromIp($ip);
            
            // Check for recent applications with the same IC
            $timeThreshold = now()->subHours(24);
            $recentApplications = ConcessionApplication::where('ic_number', $icNumber)
                ->where('created_at', '>=', $timeThreshold)
                ->whereNotNull('submission_city')
                ->get();

            // Detect anomaly: same IC, different city within 24 hours
            foreach ($recentApplications as $app) {
                if ($app->submission_city && $city && $app->submission_city !== $city) {
                    // Check for VPN/Proxy usage
                    $isVpnOrProxy = $this->isVpnOrProxy($ip);
                    
                    Log::warning('Potential anomalous application detected', [
                        'ic_number' => $icNumber,
                        'current_city' => $city,
                        'previous_city' => $app->submission_city,
                        'ip' => $ip,
                        'is_vpn_or_proxy' => $isVpnOrProxy,
                        'user_id' => Auth::id() ?? 'guest',
                        'application_id' => $app->application_id
                    ]);

                    throw ValidationException::withMessages([
                        'ic' => 'Suspicious activity detected: Multiple applications with the same IC from different cities within 24 hours.'
                    ]);
                }
            }

            // Store city in request for BasicApplicationProcessor
            $request->merge(['submission_city' => $city]);
        }

        return $this->processor->process($request);
    }

    protected function getCityFromIp($ip) {
        try {
            $response = Http::withToken(config('services.ipinfo.token'))
                ->get("https://ipinfo.io/{$ip}/json");
            
            if ($response->successful()) {
                $data = $response->json();
                $isVpnOrProxy = isset($data['privacy']['vpn']) && $data['privacy']['vpn'] || 
                               isset($data['privacy']['proxy']) && $data['privacy']['proxy'];
                
                // Log VPN/Proxy usage
                if ($isVpnOrProxy) {
                    Log::info('VPN or Proxy detected', [
                        'ip' => $ip,
                        'user_id' => Auth::id() ?? 'guest',
                        'privacy_data' => $data['privacy'] ?? []
                    ]);
                }

                return $data['city'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to fetch city from IPinfo', [
                'ip' => $ip,
                'error' => $e->getMessage()
            ]);
        }
        return null;
    }

    protected function isVpnOrProxy($ip) {
        try {
            $response = Http::withToken(config('services.ipinfo.token'))
                ->get("https://ipinfo.io/{$ip}/json");
            
            if ($response->successful()) {
                $data = $response->json();
                return isset($data['privacy']['vpn']) && $data['privacy']['vpn'] ||
                       isset($data['privacy']['proxy']) && $data['privacy']['proxy'];
            }
        } catch (\Exception $e) {
            Log::error('Failed to check VPN/Proxy status', [
                'ip' => $ip,
                'error' => $e->getMessage()
            ]);
        }
        return false;
    }
}

abstract class ApplicationDecorator implements ApplicationProcessorInterface {
    protected $processor;
    public function __construct(ApplicationProcessorInterface $processor) {
        $this->processor = $processor;
    }
    public function process(Request $request) {
        return $this->processor->process($request);
    }
}

class OkuApplicationDecorator extends ApplicationDecorator {
    public function process(Request $request) {
        $request->validate([
            'ic' => 'required|string|size:12|regex:/^\d+$/',
            'okuCardNumber' => 'required|string|min:8',
            'disabilityType' => 'required|string|in:visual,hearing,mobility,cognitive,other',
            'otherDisability' => 'required_if:disabilityType,other|string',
            'okuCardPhoto' => 'required|image|max:2048',
        ]);
        $photoPath = null;
        if ($request->hasFile('okuCardPhoto')) {
            $photoPath = $request->file('okuCardPhoto')->store('oku_card_photos', 'public');
        }
        $application = $this->processor->process($request);
        $disability = $request->disabilityType === 'other' ? $request->otherDisability : $request->disabilityType;
        $application->update([
            'ic_number' => $request->ic,
            'oku_card_number' => $request->okuCardNumber,
            'disability_info' => $disability,
            'citizenship' => $request->citizenship ?? 'Malaysia',
            'oku_card_photo_path' => $photoPath,
        ]);
        return $application;
    }
}

class SeniorApplicationDecorator extends ApplicationDecorator {
    public function process(Request $request) {
        $request->validate([
            'seniorIc' => 'required|string|size:12|regex:/^\d+$/',
            'seniorIcPhoto' => 'required|image|max:2048',
        ]);
        $ic = $request->seniorIc;
        $yy = (int) substr($ic, 0, 2);
        $mm = substr($ic, 2, 2);
        $dd = substr($ic, 4, 2);
        $current_yy = (int) date('y');
        $age = $current_yy - $yy;
        if ($age < 0) {
            $age += 100;
        }
        if ($age < 59) {
            throw ValidationException::withMessages(['seniorIc' => 'The IC number indicates an age below 60. Senior concession requires age 60 or above.']);
        }
        $birth_year = (int) date('Y') - $age;
        $date_of_birth = sprintf('%d-%s-%s', $birth_year, $mm, $dd);
        $last_digit = (int) substr($ic, -1);
        $gender = ($last_digit % 2 === 1) ? 'male' : 'female';
        $photoPath = null;
        if ($request->hasFile('seniorIcPhoto')) {
            $photoPath = $request->file('seniorIcPhoto')->store('senior_ic_photos', 'public');
        }
        $application = $this->processor->process($request);
        $application->update([
            'ic_number' => $request->seniorIc,
            'age' => $age,
            'gender' => $gender,
            'date_of_birth' => $date_of_birth,
            'citizenship' => $request->citizenship ?? 'Malaysia',
            'senior_ic_photo_path' => $photoPath,
        ]);
        return $application;
    }
}

class StudentApplicationDecorator extends ApplicationDecorator {
    public function process(Request $request) {
        $request->validate([
            'studentCitizenship' => 'required|string',
            'educationLevel' => 'required|string|in:primary,secondary,university',
            'schoolName' => 'required|string',
            'matrixNumber' => 'required|string|min:4',
            'studentIdPhoto' => 'required|image|max:2048',
        ]);
        $icNumber = null;
        $passportNumber = $request->passportNumber;
        if ($request->studentCitizenship === 'Malaysia') {
            $request->validate([
                'studentIc' => 'required|string|size:12|regex:/^\d+$/',
            ]);
            $icNumber = $request->studentIc;
        } else {
            $request->validate([
                'passportNumber' => 'required|string',
            ]);
            $icNumber = null;
        }
        $photoPath = null;
        if ($request->hasFile('studentIdPhoto')) {
            $photoPath = $request->file('studentIdPhoto')->store('student_photos', 'public');
        }
        $application = $this->processor->process($request);
        $application->update([
            'ic_number' => $icNumber,
            'passport_number' => $passportNumber,
            'citizenship' => $request->studentCitizenship,
            'education_level' => $request->educationLevel,
            'school_name' => $request->schoolName,
            'matrix_number' => $request->matrixNumber,
            'student_id_photo_path' => $photoPath,
        ]);
        return $application;
    }
}

class ConcessionCardController extends Controller
{
    protected function verifyRecaptcha(Request $request)
    {
        $recaptchaToken = $request->header('X-Recaptcha-Token');
        if (!$recaptchaToken) {
            Log::warning('reCAPTCHA token missing', ['user_id' => Auth::id() ?? 'guest']);
            throw ValidationException::withMessages(['recaptcha' => 'reCAPTCHA token is required']);
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $recaptchaToken,
            'remoteip' => $request->ip()
        ]);

        $result = $response->json();
        if (!$result['success'] || $result['score'] < 0.5) {
            Log::warning('reCAPTCHA verification failed', [
                'user_id' => Auth::id() ?? 'guest',
                'score' => $result['score'] ?? null,
                'errors' => $result['error-codes'] ?? []
            ]);
            throw ValidationException::withMessages(['recaptcha' => 'reCAPTCHA verification failed']);
        }
    }

    public function submitApplication(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:oku,senior,student',
                'fullName' => 'required|string|max:255',
                'passportNumber' => 'nullable|string',
            ]);

            $processor = new BasicApplicationProcessor();
            $type = $request->type;

            if ($type === 'oku') {
                $processor = new OkuApplicationDecorator($processor);
            } elseif ($type === 'senior') {
                $processor = new SeniorApplicationDecorator($processor);
            } elseif ($type === 'student') {
                $processor = new StudentApplicationDecorator($processor);
            }

            $processor = new AnomalyDetectionDecorator($processor); // Add anomaly detection
            $processor = new RecaptchaValidator($processor, config('services.recaptcha.secret_key'));

            $application = $processor->process($request);

            $responseData = [
                'id' => $application->application_id,
                'type' => $application->type,
                'fullName' => $application->full_name,
                'ic' => $application->ic_number,
                'passportNumber' => $application->passport_number,
                'status' => $application->status,
                'applicationDate' => $application->created_at->toIso8601String()
            ];

            if ($application->type === 'oku') {
                $responseData['okuCardNumber'] = $application->oku_card_number;
                $responseData['disability'] = $application->disability_info;
                if ($application->oku_card_photo_path) {
                    $responseData['photoName'] = basename($application->oku_card_photo_path);
                    $responseData['photoUrl'] = asset('storage/' . $application->oku_card_photo_path);
                }
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

    public function getApplications(Request $request)
    {
        try {
            $this->verifyRecaptcha($request);

            if (!Auth::check()) {
                Log::warning('Unauthorized attempt to fetch applications', ['user_id' => Auth::id() ?? 'guest']);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Please log in.'
                ], 401);
            }

            $userId = Auth::id();
            Log::info('Fetching applications for user ID: ' . $userId);

            $applications = ConcessionApplication::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($applications === null) {
                Log::error('Database query returned null for user applications', ['user_id' => $userId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch applications due to database issue'
                ], 500);
            }

            Log::info('Found ' . $applications->count() . ' applications for user ' . $userId);

            $transformedApplications = $applications->map(function ($app) {
                $data = [
                    'id' => $app->application_id,
                    'type' => $app->type,
                    'fullName' => $app->full_name ?? 'N/A',
                    'ic' => $app->ic_number ?? null,
                    'passportNumber' => $app->passport_number ?? null,
                    'status' => $app->status ?? 'pending',
                    'applicationDate' => $app->created_at ? $app->created_at->toIso8601String() : null
                ];

                if ($app->type === 'student') {
                    $data['studentIc'] = $app->ic_number ?? null;
                } elseif ($app->type === 'senior') {
                    $data['seniorIc'] = $app->ic_number ?? null;
                }

                if ($app->type === 'oku') {
                    $data['okuCardNumber'] = $app->oku_card_number ?? null;
                    $data['disability'] = $app->disability_info ?? null;
                    $data['oku_card_photo_path'] = $app->oku_card_photo_path ?? null;
                    if ($app->oku_card_photo_path) {
                        $data['photoName'] = basename($app->oku_card_photo_path);
                        $data['photoUrl'] = asset('storage/' . $app->oku_card_photo_path);
                    }
                } elseif ($app->type === 'senior') {
                    $data['age'] = $app->age ?? null;
                    $data['citizenship'] = $app->citizenship ?? null;
                    $data['gender'] = $app->gender ?? null;
                    $data['dateOfBirth'] = $app->date_of_birth ?? null;
                    $data['senior_ic_photo_path'] = $app->senior_ic_photo_path ?? null;
                    if ($app->senior_ic_photo_path) {
                        $data['photoName'] = basename($app->senior_ic_photo_path);
                        $data['photoUrl'] = asset('storage/' . $app->senior_ic_photo_path);
                    }
                } elseif ($app->type === 'student') {
                    $data['matrixNumber'] = $app->matrix_number ?? null;
                    $data['schoolName'] = $app->school_name ?? null;
                    $data['studentCitizenship'] = $app->citizenship ?? null;
                    $data['educationLevel'] = $app->education_level ?? null;
                    $data['student_id_photo_path'] = $app->student_id_photo_path ?? null;
                    if ($app->student_id_photo_path) {
                        $data['photoName'] = basename($app->student_id_photo_path);
                        $data['photoUrl'] = asset('storage/' . $app->student_id_photo_path);
                    }
                }

                return $data;
            })->toArray();

            return response()->json([
                'success' => true,
                'applications' => $transformedApplications
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed: ' . $e->getMessage(), ['user_id' => Auth::id() ?? 'guest']);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error fetching applications: ' . $e->getMessage(), ['user_id' => Auth::id() ?? 'guest']);
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred while fetching applications'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error fetching applications: ' . $e->getMessage(), ['user_id' => Auth::id() ?? 'guest']);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications: ' . $e->getMessage()
            ], 500);
        }
    }

    public function viewApplication(Request $request, $id)
    {
        try {
            $this->verifyRecaptcha($request);

            if (empty($id) || !is_string($id)) {
                Log::warning('Invalid application ID provided', ['application_id' => $id, 'user_id' => Auth::id() ?? 'guest']);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid application ID'
                ], 400);
            }

            if (!Auth::check()) {
                Log::warning('Unauthorized attempt to view application', ['application_id' => $id, 'user_id' => Auth::id() ?? 'guest']);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access. Please log in.'
                ], 401);
            }

            $application = ConcessionApplication::where('application_id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$application) {
                Log::warning('Application not found or access denied', [
                    'application_id' => $id,
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found or you do not have access to this application'
                ], 404);
            }

            $data = [
                'id' => $application->application_id,
                'type' => $application->type,
                'fullName' => $application->full_name ?? 'N/A',
                'ic' => $application->ic_number ?? null,
                'passportNumber' => $application->passport_number ?? null,
                'status' => $application->status ?? 'pending',
                'applicationDate' => $application->created_at ? $application->created_at->toIso8601String() : null
            ];

            if ($application->type === 'student') {
                $data['studentIc'] = $application->ic_number ?? null;
            } elseif ($application->type === 'senior') {
                $data['seniorIc'] = $application->ic_number ?? null;
            }

            if ($application->type === 'oku') {
                $data['okuCardNumber'] = $application->oku_card_number ?? null;
                $data['disability'] = $application->disability_info ?? null;
                $data['oku_card_photo_path'] = $application->oku_card_photo_path ?? null;
                if ($application->oku_card_photo_path) {
                    $data['photoName'] = basename($application->oku_card_photo_path);
                    $data['photoUrl'] = asset('storage/' . $application->oku_card_photo_path);
                }
            } elseif ($application->type === 'senior') {
                $data['age'] = $application->age ?? null;
                $data['citizenship'] = $application->citizenship ?? null;
                $data['gender'] = $application->gender ?? null;
                $data['dateOfBirth'] = $application->date_of_birth ?? null;
                $data['senior_ic_photo_path'] = $application->senior_ic_photo_path ?? null;
                if ($application->senior_ic_photo_path) {
                    $data['photoName'] = basename($application->senior_ic_photo_path);
                    $data['photoUrl'] = asset('storage/' . $application->senior_ic_photo_path);
                }
            } elseif ($application->type === 'student') {
                $data['matrixNumber'] = $application->matrix_number ?? null;
                $data['schoolName'] = $application->school_name ?? null;
                $data['studentCitizenship'] = $application->citizenship ?? null;
                $data['educationLevel'] = $application->education_level ?? null;
                $data['student_id_photo_path'] = $application->student_id_photo_path ?? null;
                if ($application->student_id_photo_path) {
                    $data['photoName'] = basename($application->student_id_photo_path);
                    $data['photoUrl'] = asset('storage/' . $application->student_id_photo_path);
                }
            }

            Log::info('Successfully fetched application', [
                'application_id' => $application->application_id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'application' => $data
            ], 200);
        } catch (ValidationException $e) {
            Log::error('Validation failed: ' . $e->getMessage(), ['user_id' => Auth::id() ?? 'guest']);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database error viewing application: ' . $e->getMessage(), [
                'application_id' => $id,
                'user_id' => Auth::id() ?? 'guest'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Database error occurred while viewing application'
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error viewing application: ' . $e->getMessage(), [
                'application_id' => $id,
                'user_id' => Auth::id() ?? 'guest'
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to view application: ' . $e->getMessage()
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

    public function exportApplicationsIssueToken(Request $request)
    {
        $request->validate([
            'format' => 'nullable|string|in:csv,json'
        ]);

        $userId = FacadesAuth::id() ?? 'guest';

        $nowMinute = date('YmdHi');
        $minuteKey = 'cex.min.' . $nowMinute;
        $currentCount = (int) $request->session()->get($minuteKey, 0);
        if ($currentCount >= 2) {
            app(\App\Services\AdminActivityLogger::class)->log('concession_export_rate_limited', [ 'user_id' => $userId, 'count_minute' => $currentCount ]);
            return response()->json(['success' => false, 'message' => 'Export rate limit exceeded. Try again later.'], 429);
        }
        $request->session()->put($minuteKey, $currentCount + 1);

        $cooldownKey = 'cex.cool';
        $lastTs = (int) $request->session()->get($cooldownKey, 0);
        if (time() - $lastTs < 30) {
            return response()->json(['success' => false, 'message' => 'Please wait before exporting applications again.'], 429);
        }
        $request->session()->put($cooldownKey, time());

        $token = bin2hex(random_bytes(16));
        $payload = [
            'format' => $request->get('format', 'csv'),
            'issued_at' => time()
        ];
        $request->session()->put('admin.concession_export_tokens.' . $token, $payload);

        app(\App\Services\AdminActivityLogger::class)->log('concession_export_token_issued', [ 'format' => $payload['format'] ]);

        return response()->json([
            'success' => true,
            'download_token' => $token,
            'expires_in_seconds' => 120
        ]);
    }

    public function exportApplicationsDownload(Request $request)
    {
        $request->validate(['token' => 'required|string|size:32']);

        $key = 'admin.concession_export_tokens.' . $request->token;
        $payload = $request->session()->get($key);
        if (!$payload) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired token'], 401);
        }
        if ((time() - ($payload['issued_at'] ?? 0)) > 120) {
            $request->session()->forget($key);
            return response()->json(['success' => false, 'message' => 'Token expired'], 401);
        }
        $request->session()->forget($key);

        $rows = ConcessionApplication::orderBy('created_at', 'desc')->get();
        $format = $payload['format'] ?? 'csv';

        if ($format === 'json') {
            $content = $rows->map(function ($app) {
                return [
                    'applicationId' => $app->application_id,
                    'fullName' => $app->full_name,
                    'type' => $app->type,
                    'status' => $app->status,
                    'ic' => $app->ic_number,
                    'passportNumber' => $app->passport_number,
                    'appliedDate' => optional($app->created_at)->toIso8601String(),
                ];
            });
            $filename = 'concession_applications_' . now()->format('Y-m-d_His') . '.json';
            app(\App\Services\AdminActivityLogger::class)->log('concession_export_download', [ 'format' => 'json', 'count' => $rows->count() ]);
            return response()->json([
                'success' => true,
                'filename' => $filename,
                'format' => 'json',
                'content' => $content,
            ]);
        }

        $headers = ['Application ID','Full Name','Type','Status','IC','Passport','Applied Date'];
        $lines = [];
        $lines[] = implode(',', $headers);
        foreach ($rows as $app) {
            $fields = [
                $app->application_id,
                $app->full_name,
                $app->type,
                $app->status,
                $app->ic_number,
                $app->passport_number,
                optional($app->created_at)->toDateTimeString(),
            ];
            $escaped = array_map(function ($v) {
                $v = (string) $v;
                if (str_contains($v, '"')) { $v = str_replace('"', '""', $v); }
                if (str_contains($v, ',') || str_contains($v, '\n') || str_contains($v, '"')) {
                    $v = '"' . $v . '"';
                }
                return $v;
            }, $fields);
            $lines[] = implode(',', $escaped);
        }
        $csv = implode("\n", $lines);
        $filename = 'concession_applications_' . now()->format('Y-m-d_His') . '.csv';

        app(\App\Services\AdminActivityLogger::class)->log('concession_export_download', [ 'format' => 'csv', 'count' => $rows->count() ]);

        return response()->json([
            'success' => true,
            'filename' => $filename,
            'format' => 'csv',
            'content' => $csv,
        ]);
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

    public function getAllApplicationsForAdmin(Request $request)
    {
        try {
            $this->verifyRecaptcha($request);

            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

            $applications = ConcessionApplication::with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            Log::info('Found ' . $applications->count() . ' applications for admin review');
            Log::info('Current user ID: ' . Auth::id());
            Log::info('Applications data: ' . json_encode($applications->toArray()));

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

                if ($app->type === 'student') {
                    $data['studentIc'] = $app->ic_number;
                } elseif ($app->type === 'senior') {
                    $data['seniorIc'] = $app->ic_number;
                }

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
        } catch (ValidationException $e) {
            Log::error('Validation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error fetching all applications for admin: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch applications'
            ], 500);
        }
    }

    public function getAdminAllStats(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }

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

    public function schoolsData()
    {
        try {
            $csvPath = base_path('SenaraiSekolah.csv');
            if (!file_exists($csvPath)) {
                return response()->json([
                    'primary' => [],
                    'secondary' => []
                ]);
            }

            $file = new SplFileObject($csvPath);
            $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE);
            $file->setCsvControl(',');

            $headers = [];
            $normalizedHeaders = [];
            $primary = [];
            $secondary = [];

            foreach ($file as $index => $row) {
                if ($row === [null] || $row === false) {
                    continue;
                }
                if ($index === 0) {
                    $headers = $row ?: [];
                    $normalizedHeaders = array_map(function ($h) {
                        $h = (string) $h;
                        $h = preg_replace('/^\xEF\xBB\xBF/u', '', $h) ?? $h;
                        return strtoupper(trim($h));
                    }, $headers);
                    continue;
                }

                $record = [];
                foreach ($normalizedHeaders as $i => $key) {
                    if ($key === null || $key === '') continue;
                    $record[$key] = isset($row[$i]) ? (string) $row[$i] : '';
                }

                $level = $record['PERINGKAT'] ?? '';
                $typeLabel = $record['JENIS/LABEL'] ?? ($record['JENIS'] ?? '');
                $nameField = $record['NAMASEKOLAH'] ?? '';

                $state = $record['NEGERI'] ?? '';
                $district = $record['PPD'] ?? '';
                $code = $record['KODSEKOLAH'] ?? '';
                $name = $nameField;

                $school = [
                    'code' => (string) $code,
                    'name' => (string) $name,
                    'state' => (string) $state,
                    'district' => (string) $district
                ];

                $levelNorm = strtoupper(trim((string) $level));
                $typeNorm = strtoupper(trim((string) $typeLabel));
                $typeCompact = preg_replace('/[^A-Z0-9]/', '', $typeNorm);
                $nameNorm = strtoupper(trim((string) $name));
                $nameCompact = preg_replace('/[^A-Z0-9]/', '', $nameNorm);

                if (strpos($levelNorm, 'RENDAH') !== false) {
                    $primary[] = $school;
                    continue;
                }

                if (strpos($levelNorm, 'MENENGAH') !== false) {
                    $secondary[] = $school;
                    continue;
                }

                $isSecondary = false;
                $isPrimary = false;

                $secondaryIndicators = [
                    'SMK','SMKA','SMA','SMJK','SBP','SBPI','MRSM','KV','KPV','SMV','SM SAINS','SEKOLAH MENENGAH','SEK MEN','MENENGAH'
                ];
                foreach ($secondaryIndicators as $ind) {
                    if (strpos($typeNorm, $ind) !== false || strpos($typeCompact, preg_replace('/[^A-Z0-9]/','', $ind)) !== false || strpos($nameNorm, $ind) !== false || strpos($nameCompact, preg_replace('/[^A-Z0-9]/','', $ind)) !== false) {
                        $isSecondary = true;
                        break;
                    }
                }

                $primaryIndicators = [
                    'SK','SJKC','SJK T','SJKT','SJK (C)','SJK (T)','SJK','SRK','SRJK','SEKOLAH KEBANGSAAN','SEK KEBANGSAAN','SEKOLAH JENIS','K9','RENDAH','SKM'
                ];
                foreach ($primaryIndicators as $ind) {
                    if (strpos($typeNorm, $ind) !== false || strpos($typeCompact, preg_replace('/[^A-Z0-9]/','', $ind)) !== false || strpos($nameNorm, $ind) !== false || strpos($nameCompact, preg_replace('/[^A-Z0-9]/','', $ind)) !== false) {
                        $isPrimary = true;
                        break;
                    }
                }

                if ($isSecondary && !$isPrimary) {
                    $secondary[] = $school;
                    continue;
                }

                if ($isPrimary && !$isSecondary) {
                    $primary[] = $school;
                    continue;
                }

                if ($isPrimary) {
                    $primary[] = $school;
                } elseif ($isSecondary) {
                    $secondary[] = $school;
                }
            }

            return response()->json([
                'primary' => $primary,
                'secondary' => $secondary
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to load schools data: ' . $e->getMessage());
            return response()->json([
                'primary' => [],
                'secondary' => []
            ], 500);
        }
    }
}