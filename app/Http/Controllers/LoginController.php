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
                
                // Check if user is active
                if ($user->account_status !== 'active') {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your account is not active. Please check your email for verification.',
                    ]);
                }

                // Log the user in
                Auth::login($user, $request->boolean('remember'));

                // Redirect to homepage after successful login
                return redirect()->route('HomePage')->with('success', 'Welcome back, ' . $user->first_name . '!');
            }
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Authentication error: ' . $e->getMessage(),
            ])->withInput($request->only('email'));
        }

        // Authentication failed
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
     * Handle forgot password request
     */
    public function handleForgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $success = $this->userRegistrationService->handleForgotPassword($request->email);

        if ($success) {
            return back()->with('success', 'Password reset link has been sent to your email.');
        }

        return back()->withErrors(['email' => 'We could not find a user with that email address.']);
    }

    /**
     * Show password reset form
     */
    public function showResetPasswordForm(string $token)
    {
        return view('auth.reset-password', compact('token'));
    }

    /**
     * Handle password reset
     */
    public function handleResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'nullable|string|same:password',
        ]);

        $success = $this->userRegistrationService->resetPassword(
            $request->token,
            $request->password
        );

        if ($success) {
            return redirect()->route('signin')->with('success', 'Your password has been reset successfully.');
        }

        return back()->withErrors(['email' => 'Invalid or expired reset token.']);
    }

    /**
     * Verify email address
     */
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