<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserRegistrationService;
use App\Factories\UserFactoryManager;
use App\Factories\AuthFactoryManager;
use App\Factories\MailFactoryManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignupController extends Controller
{
    private UserRegistrationService $userRegistrationService;
    private UserFactoryManager $userFactoryManager;
    private AuthFactoryManager $authFactoryManager;
    private MailFactoryManager $mailFactoryManager;

    public function __construct(
        UserRegistrationService $userRegistrationService,
        UserFactoryManager $userFactoryManager,
        AuthFactoryManager $authFactoryManager,
        MailFactoryManager $mailFactoryManager
    ) {
        $this->userRegistrationService = $userRegistrationService;
        $this->userFactoryManager = $userFactoryManager;
        $this->authFactoryManager = $authFactoryManager;
        $this->mailFactoryManager = $mailFactoryManager;
    }

    public function showForm()
    {
        return view('SignUpPage');
    }

    public function handleSignup(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'nullable|string|same:password',
            'newsletter' => 'boolean',
            'marketing' => 'boolean',
            'updates' => 'boolean',
        ]);

        try {
            // Create the user using the Factory pattern
            $user = $this->userFactoryManager->createUser('regular', $validated);

            // Send email verification using UserRegistrationService
            $this->userRegistrationService->sendVerificationEmail($user);

            // Log the user in
            Auth::login($user);

            // Redirect to the login page or dashboard
            return redirect()->route('signin')->with('success', 'Registration successful! Please check your email to verify your account.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    public function handleOAuth(Request $request)
    {
        try {
            if ($request->has('google')) {
                $user = $this->authFactoryManager->authenticate('social', [
                    'provider' => 'google',
                    'provider_id' => $request->input('google_id'),
                ]);
            } elseif ($request->has('facebook')) {
                $user = $this->authFactoryManager->authenticate('social', [
                    'provider' => 'facebook',
                    'provider_id' => $request->input('facebook_id'),
                ]);
            } else {
                return redirect()->route('home')->with('error', 'Invalid OAuth provider');
            }

            if ($user) {
                Auth::login($user);
                return redirect()->route('home')->with('success', 'OAuth login successful');
            } else {
                return redirect()->route('home')->with('error', 'OAuth authentication failed');
            }
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'OAuth authentication error: ' . $e->getMessage());
        }
    }

    /**
     * Handle forgot password request
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

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
}
