<?php

namespace App\Http\Controllers;

use App\Services\UserRegistrationService;
use App\Factories\UserFactoryManager;
use App\Factories\MailFactoryManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /** @var \App\Services\UserRegistrationService */
    private UserRegistrationService $userRegistrationService;
    /** @var \App\Factories\UserFactoryManager */
    private UserFactoryManager $userFactoryManager;
    /** @var \App\Factories\MailFactoryManager */
    private MailFactoryManager $mailFactoryManager;

    public function __construct(
        UserRegistrationService $userRegistrationService,
        UserFactoryManager $userFactoryManager,
        MailFactoryManager $mailFactoryManager
    ) {
        $this->userRegistrationService = $userRegistrationService;
        $this->userFactoryManager = $userFactoryManager;
        $this->mailFactoryManager = $mailFactoryManager;
        $this->middleware('auth.required');
    }

    /**
     * Show user profile
     */
    public function show()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // If there's a success message, refresh the user data to get the latest wallet balance
        if (session('success') && str_contains(session('success'), 'Topup successful')) {
            $user->refresh();
        }
        
        return view('ProfilePage', compact('user'));
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->user_id . ',user_id',
            'phone' => 'sometimes|nullable|string|regex:/^\+?[0-9\s\-]{7,15}$/|unique:users,phone,' . $user->user_id . ',user_id',
            'gender' => 'sometimes|string|in:male,female',
            // Must be a date and at least 13 years old
            'date_of_birth' => 'sometimes|date|before_or_equal:' . now()->subYears(13)->toDateString(),
            'profile_picture' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'confirm_password' => 'sometimes|string',
            'newsletter' => 'boolean',
            'marketing' => 'boolean',
            'updates' => 'boolean',
        ]);

        // Handle email update with password confirmation
        if ($request->has('email') && $request->email !== $user->email) {
            // For social login users without password, skip password confirmation
            if ($user->hasPassword()) {
                if (!$request->has('confirm_password') || !Hash::check($request->confirm_password, $user->password)) {
                    return back()->withErrors(['confirm_password' => 'Password is incorrect.']);
                }
            }
            $validated['email'] = $request->email;
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old avatar if exists
            if (!empty($user->profile_picture)) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile-pictures', 'public');
            $validated['profile_picture'] = $path;
        }

        // Handle email subscription preferences
        if (isset($validated['newsletter']) || isset($validated['marketing']) || isset($validated['updates'])) {
            $validated['email_subscription'] = [
                'newsletter' => $validated['newsletter'] ?? false,
                'marketing' => $validated['marketing'] ?? false,
                'updates' => $validated['updates'] ?? false,
            ];
        }

        $updatedUser = $this->userRegistrationService->updateUserProfile($user, $validated);

        $message = 'Profile updated successfully!';
        if (isset($validated['email'])) {
            $message = 'Email updated successfully!';
        }
        if (isset($validated['phone'])) {
            $message = 'Phone number updated successfully!';
        }

        return redirect()->route('profile')->with('success', $message);
    }

    /**
     * Show change password form
     */
    public function showChangePassword()
    {
        return view('profile.change-password');
    }

    /**
     * Change user password
     */
    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // If user has no password (social login), allow setting password without current password
        if ($user->needsPasswordSetup()) {
            $request->validate([
                'password' => 'required|string|min:8',
                'password_confirmation' => 'nullable|string|same:password',
            ]);

            $success = $this->userRegistrationService->setPassword($user, $request->password);
            
            if ($success) {
                return redirect()->route('profile')->with('success', 'Password set successfully!');
            }
        } else {
            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'nullable|string|same:password',
            ]);

            $success = $this->userRegistrationService->changePassword(
                $user,
                $request->current_password,
                $request->password
            );

            if ($success) {
                return redirect()->route('profile')->with('success', 'Password changed successfully!');
            }

            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        return back()->withErrors(['password' => 'Password update failed.']);
    }

    /**
     * Update email subscription preferences
     */
    public function updateEmailSubscription(Request $request)
    {
        $request->validate([
            'newsletter' => 'boolean',
            'marketing' => 'boolean',
            'updates' => 'boolean',
        ]);

        $user = Auth::user();
        $subscriptionData = [
            'newsletter' => $request->boolean('newsletter'),
            'marketing' => $request->boolean('marketing'),
            'updates' => $request->boolean('updates'),
        ];

        $this->userRegistrationService->updateEmailSubscription($user, $subscriptionData);

        return back()->with('success', 'Email subscription preferences updated successfully!');
    }

    /**
     * Delete user account
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        // Soft delete or mark as inactive
        $user->account_status = 'deleted';
        $user->save();

        Auth::logout();

        return redirect()->route('HomePage')->with('success', 'Your account has been deleted successfully.');
    }

    /**
     * Show user activity/logs
     */
    public function activity()
    {
        $user = Auth::user();
        // You can add activity logging functionality here
        return view('profile.activity', compact('user'));
    }
} 