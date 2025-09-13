<?php

namespace App\Http\Controllers;

use App\Services\UserRegistrationService;
use App\Factories\AuthFactoryManager;
use App\Factories\MailFactoryManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    private UserRegistrationService $userRegistrationService;
    private AuthFactoryManager $authFactoryManager;
    private MailFactoryManager $mailFactoryManager;

    public function __construct(
        UserRegistrationService $userRegistrationService,
        AuthFactoryManager $authFactoryManager,
        MailFactoryManager $mailFactoryManager
    ) {
        $this->userRegistrationService = $userRegistrationService;
        $this->authFactoryManager = $authFactoryManager;
        $this->mailFactoryManager = $mailFactoryManager;
    }

    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('SignInPage');
    }

    /**
     * Handle user login
     */
    public function handleLogin(Request $request)
    {
        // Validate login credentials
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // Attempt to authenticate user using Factory pattern
        try {
            $user = $this->authFactoryManager->authenticate('email', $credentials);
            
            if ($user) {
                // Update last login timestamp
                $this->userRegistrationService->handleUserLogin($user);
                
                // Check if user is allowed (active or admin)
                if (!in_array($user->account_status, ['active', 'admin'])) {
                    Auth::logout();
                    
                    // Provide specific error message for suspended users
                    if ($user->account_status === 'suspended') {
                        return back()->withErrors([
                            'email' => 'Your account is currently suspended. It may be caused by breaking the rules. Please contact administrator for assistance.',
                        ]);
                    }
                    
                    return back()->withErrors([
                        'email' => 'Your account is not active. Please check your email for verification.',
                    ]);
                }

                // Log the user in, then regenerate session to prevent fixation
                Auth::login($user, false);
                $request->session()->regenerate();

                // Redirect based on role/status
                if ($user->account_status === 'admin') {
                    // Mark recent admin re-auth on fresh login
                    $request->session()->put('admin.recently_authenticated_at', time());
                    // Clear 2FA verified flag on each admin login; enforce 2FA
                    $request->session()->forget('admin.2fa.verified');
                    if ($user->two_factor_enabled && !empty($user->two_factor_secret)) {
                        return redirect()->route('admin.2fa.challenge');
                    }
                    return redirect()->route('admin.2fa.setup');
                }

                // Default redirect to homepage after successful login
                return redirect()->route('HomePage')->with('success', 'Welcome back, ' . $user->first_name . '!');
            } else {
                // Authentication failed - get specific error from factory
                $factory = $this->authFactoryManager->getFactory('email');
                $errors = $factory->getErrors();
                
                if (!empty($errors)) {
                    return back()->withErrors([
                        'email' => $errors[0], // Use the first error message
                    ])->withInput($request->only('email'));
                }
            }
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Authentication error: ' . $e->getMessage(),
            ])->withInput($request->only('email'));
        }

        // Fallback error message
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle user logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('HomePage')->with('success', 'You have been successfully logged out.');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password (OTP) - send OTP to email
     */
    public function handleForgotPasswordOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $sent = $this->userRegistrationService->sendPasswordResetOtp($request->email);
        if ($sent) {
            return redirect()->route('signin')
                ->with(['success' => 'We have emailed a 6-digit OTP to you.', 'show_otp_modal' => true, 'otp_email' => $request->email]);
        }
        return back()->withErrors(['email' => 'We could not find a user with that email address.']);
    }

    /** Show OTP verification form */
    public function showVerifyOtpForm(Request $request)
    {
        $email = $request->query('email');
        return view('auth.verify-otp', compact('email'));
    }

    /** Verify OTP and redirect to reset form */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $valid = $this->userRegistrationService->verifyPasswordResetOtp($request->email, $request->otp);
        if ($valid) {
            return redirect()->route('signin')
                ->with(['success' => 'OTP verified. Please reset your password.', 'show_reset_modal' => true, 'reset_email' => $request->email]);
        }
        return redirect()->route('signin')
            ->withErrors(['otp' => 'Invalid or expired OTP.'])
            ->with(['show_otp_modal' => true, 'otp_email' => $request->email]);
    }

    /** Show reset form after OTP verified */
    public function showResetPasswordOtpForm(Request $request)
    {
        $email = $request->query('email');
        return view('auth.reset-password', compact('email'));
    }

    /** Handle password reset with OTP verified */
    public function handleResetPasswordWithOtp(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'nullable|string|same:password',
            ]);
        } catch (ValidationException $e) {
            // If validation fails (e.g., password mismatch), redirect back to signin with reset modal
            return redirect()->route('signin')
                ->withErrors($e->errors())
                ->with(['show_reset_modal' => true, 'reset_email' => $request->email]);
        }

        $success = $this->userRegistrationService->resetPasswordAfterOtp($request->email, $request->password);
        if ($success) {
            return redirect()->route('signin')->with('success', 'Your password has been reset successfully.');
        }
        return redirect()->route('signin')
            ->withErrors(['password' => 'Password reset failed.'])
            ->with(['show_reset_modal' => true, 'reset_email' => $request->email]);
    }

    public function verifyEmail(string $token)
    {
        $success = $this->userRegistrationService->verifyEmail($token);

        if ($success) {
            return redirect()->route('signin')->with('success', 'Email verified successfully! You can now log in.');
        }

        return redirect()->route('signin')->withErrors(['email' => 'Invalid verification token.']);
    }

    /**
     * Clear session messages
     */
    public function clearSession(Request $request)
    {
        $request->session()->forget(['success', 'error', 'warning', 'info']);
        return response()->json(['success' => true]);
    }
} 