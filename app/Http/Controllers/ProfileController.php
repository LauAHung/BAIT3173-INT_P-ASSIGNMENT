<?php

namespace App\Http\Controllers;

use App\Services\UserRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    private UserRegistrationService $userRegistrationService;

    public function __construct(UserRegistrationService $userRegistrationService)
    {
        $this->userRegistrationService = $userRegistrationService;
    }

    /**
     * Show user profile
     */
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    /**
     * Show profile edit form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'profile_picture' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'newsletter' => 'boolean',
            'marketing' => 'boolean',
            'updates' => 'boolean',
        ]);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
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

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
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
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8',
            'password_confirmation' => 'nullable|string|same:password',
        ]);

        $user = Auth::user();
        $success = $this->userRegistrationService->changePassword(
            $user,
            $request->current_password,
            $request->password
        );

        if ($success) {
            return redirect()->route('profile.show')->with('success', 'Password changed successfully!');
        }

        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
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

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password is incorrect.']);
        }

        // Soft delete or mark as inactive
        $user->account_status = 'deleted';
        $user->save();

        Auth::logout();

        return redirect()->route('home')->with('success', 'Your account has been deleted successfully.');
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