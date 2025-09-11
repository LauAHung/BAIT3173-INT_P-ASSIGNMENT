<?php

namespace App\Services;

use App\Factories\UserFactoryManager;
use App\Factories\MailFactoryManager;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Exception;

class UserRegistrationService
{
    private UserFactoryManager $userFactoryManager;
    private MailFactoryManager $mailFactoryManager;

    public function __construct(
        UserFactoryManager $userFactoryManager,
        MailFactoryManager $mailFactoryManager
    ) {
        $this->userFactoryManager = $userFactoryManager;
        $this->mailFactoryManager = $mailFactoryManager;
    }

    /**
     * Register a new user with standard registration
     */
    public function registerUser(array $userData): User
    {
        try {
            // Validate required fields
            $this->validateUserData($userData);

            // Create user using Factory pattern
            $user = $this->userFactoryManager->createUser('regular', $userData);

            // Send verification email
            $this->sendVerificationEmail($user);

            return $user;
        } catch (ValidationException $e) {
            throw new Exception('User registration failed: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('User registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Register user via social media
     */
    public function registerSocialMediaUser(array $socialData): User
    {
        try {
            // Validate required social media fields
            $this->validateSocialMediaData($socialData);

            // Determine provider type
            $provider = $socialData['provider'] ?? '';
            $factoryType = $this->getFactoryTypeFromProvider($provider);

            // Create user using Factory pattern
            $user = $this->userFactoryManager->createUser($factoryType, $socialData);

            return $user;
        } catch (ValidationException $e) {
            throw new Exception('Social media registration failed: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Social media registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Create user with custom array configuration
     */
    public function createUserFromArray(array $userData): User
    {
        try {
            // Determine user type based on data
            $userType = $this->determineUserType($userData);
            return $this->userFactoryManager->createUser($userType, $userData);
        } catch (ValidationException $e) {
            throw new Exception('Custom user creation failed: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Custom user creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Update existing user profile
     */
    public function updateUserProfile(User $user, array $profileData): User
    {
        try {
            // Validate profile data
            $this->validateProfileData($profileData);

            // Update only provided fields
            if (isset($profileData['first_name'])) {
                $user->first_name = trim($profileData['first_name']);
            }
            if (isset($profileData['last_name'])) {
                $user->last_name = trim($profileData['last_name']);
            }
            if (isset($profileData['email'])) {
                $user->email = trim(strtolower($profileData['email']));
            }
            if (isset($profileData['phone'])) {
                $user->phone = preg_replace('/\s+/', '', $profileData['phone']);
            }
            if (isset($profileData['gender'])) {
                $user->gender = $profileData['gender'];
            }
            if (isset($profileData['date_of_birth'])) {
                $user->date_of_birth = $profileData['date_of_birth'];
            }
            if (isset($profileData['profile_picture'])) {
                $user->profile_picture = $profileData['profile_picture'];
            }
            if (isset($profileData['email_subscription'])) {
                $user->email_subscription = json_encode($profileData['email_subscription']);
            }

            $user->save();
            return $user;
        } catch (Exception $e) {
            throw new Exception('Profile update failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle user login and update last login
     */
    public function handleUserLogin(User $user): User
    {
        try {
            $user->last_login_at = now();
            $user->save();
            return $user;
        } catch (Exception $e) {
            throw new Exception('Login handling failed: ' . $e->getMessage());
        }
    }

    /**
     * Handle forgot password process
     */
    public function handleForgotPassword(string $email): bool
    {
        try {
            if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Valid email is required');
            }

            $user = User::where('email', trim(strtolower($email)))->first();
            if (!$user) {
                return false;
            }

            // Generate 6-digit OTP and expiry (10 minutes)
            $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->password_reset_otp = $otp;
            $user->password_reset_otp_expires_at = now()->addMinutes(10);
            $user->save();

            // Send OTP email
            $this->mailFactoryManager->sendMail('password_reset_otp', $user, ['otp' => $otp]);
            return true;
        } catch (Exception $e) {
            throw new Exception('Forgot password process failed: ' . $e->getMessage());
        }
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $newPassword): bool
    {
        try {
            if (empty($token)) {
                throw new Exception('Reset token is required');
            }

            if (strlen($newPassword) < 8) {
                throw new Exception('Password must be at least 8 characters long');
            }

            $user = User::where('password_reset_token', $token)
                ->where('password_reset_expires_at', '>', now())
                ->first();

            if (!$user) {
                return false;
            }

            $user->password = Hash::make($newPassword);
            $user->password_reset_token = null;
            $user->password_reset_expires_at = null;
            $user->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('Password reset failed: ' . $e->getMessage());
        }
    }

    /**
     * Send password reset OTP
     */
    public function sendPasswordResetOtp(string $email): bool
    {
        return $this->handleForgotPassword($email);
    }

    /**
     * Verify password reset OTP
     */
    public function verifyPasswordResetOtp(string $email, string $otp): bool
    {
        try {
            $user = User::where('email', trim(strtolower($email)))->first();
            if (!$user) { return false; }
            if (empty($user->password_reset_otp) || $user->password_reset_otp !== $otp) { return false; }
            if ($user->password_reset_otp_expires_at && $user->password_reset_otp_expires_at->isPast()) { return false; }
            // Mark OTP as verified by extending a short window to reset
            $user->password_reset_otp_expires_at = now()->addMinutes(5);
            $user->save();
            return true;
        } catch (Exception $e) {
            throw new Exception('OTP verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Reset password after OTP verification
     */
    public function resetPasswordAfterOtp(string $email, string $newPassword): bool
    {
        try {
            $user = User::where('email', trim(strtolower($email)))->first();
            if (!$user) { return false; }
            if ($user->password_reset_otp_expires_at && $user->password_reset_otp_expires_at->isPast()) { return false; }
            if (strlen($newPassword) < 8) {
                throw new Exception('Password must be at least 8 characters long');
            }
            $user->password = Hash::make($newPassword);
            // clear OTP fields
            $user->password_reset_otp = null;
            $user->password_reset_otp_expires_at = null;
            $user->save();
            return true;
        } catch (Exception $e) {
            throw new Exception('Password reset with OTP failed: ' . $e->getMessage());
        }
    }

    /**
     * Set password for social login users who don't have a password
     */
    public function setPassword(User $user, string $newPassword): bool
    {
        try {
            if (strlen($newPassword) < 8) {
                throw new Exception('Password must be at least 8 characters long');
            }

            $user->password = Hash::make($newPassword);
            $user->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('Password setup failed: ' . $e->getMessage());
        }
    }

    /**
     * Change password for authenticated user
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        try {
            if (empty($currentPassword)) {
                throw new Exception('Current password is required');
            }

            if (strlen($newPassword) < 8) {
                throw new Exception('New password must be at least 8 characters long');
            }

            if (!Hash::check($currentPassword, $user->password)) {
                return false;
            }

            $user->password = Hash::make($newPassword);
            $user->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('Password change failed: ' . $e->getMessage());
        }
    }

    /**
     * Verify email address
     */
    public function verifyEmail(string $token): bool
    {
        try {
            if (empty($token)) {
                throw new Exception('Verification token is required');
            }

            $user = User::where('email_verification_token', $token)->first();

            if (!$user) {
                return false;
            }

            $user->email_verified_at = now();
            $user->email_verification_token = null;
            $user->account_status = 'active';
            $user->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('Email verification failed: ' . $e->getMessage());
        }
    }

    /**
     * Update email subscription preferences
     */
    public function updateEmailSubscription(User $user, array $subscriptionData): User
    {
        try {
            $validKeys = ['newsletter', 'marketing', 'updates'];
            
            foreach ($subscriptionData as $key => $value) {
                if (!in_array($key, $validKeys)) {
                    throw new Exception('Invalid subscription key: ' . $key);
                }
            }

            $user->email_subscription = json_encode($subscriptionData);
            $user->save();
            return $user;
        } catch (Exception $e) {
            throw new Exception('Email subscription update failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate user data for registration
     */
    private function validateUserData(array $userData): void
    {
        $requiredFields = ['first_name', 'last_name', 'email', 'password'];
        
        foreach ($requiredFields as $field) {
            if (!isset($userData[$field]) || empty(trim($userData[$field]))) {
                throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
            }
        }

        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Valid email is required');
        }

        if (strlen($userData['password']) < 8) {
            throw new Exception('Password must be at least 8 characters long');
        }
    }

    /**
     * Validate social media data
     */
    private function validateSocialMediaData(array $socialData): void
    {
        if (!isset($socialData['email']) || empty(trim($socialData['email']))) {
            throw new Exception('Email is required for social media registration');
        }

        if (!filter_var($socialData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Valid email is required');
        }

        if (!isset($socialData['provider']) || empty(trim($socialData['provider']))) {
            throw new Exception('Social media provider is required');
        }

        if (!isset($socialData['provider_id']) || empty(trim($socialData['provider_id']))) {
            throw new Exception('Social media provider ID is required');
        }
    }

    /**
     * Validate profile data
     */
    private function validateProfileData(array $profileData): void
    {
        if (isset($profileData['first_name']) && empty(trim($profileData['first_name']))) {
            throw new Exception('First name cannot be empty');
        }

        if (isset($profileData['last_name']) && empty(trim($profileData['last_name']))) {
            throw new Exception('Last name cannot be empty');
        }

        if (isset($profileData['profile_picture']) && !empty($profileData['profile_picture'])) {
            $picturePath = $profileData['profile_picture'];
            $isUrl = filter_var($picturePath, FILTER_VALIDATE_URL) !== false;
            $isAbsolutePath = (bool)preg_match('/^\//', $picturePath);
            // Allow storage-relative paths like "profile-pictures/abc.jpg"
            $isStorageRelativePath = (bool)preg_match('/^[A-Za-z0-9_\-\/\.]+$/', $picturePath);

            if (!$isUrl && !$isAbsolutePath && !$isStorageRelativePath) {
                throw new Exception('Profile picture must be a valid URL or file path');
            }
        }
    }

    /**
     * Send verification email
     */
    public function sendVerificationEmail(User $user): void
    {
        try {
            $token = Str::random(64);
            $user->email_verification_token = $token;
            $user->save();

            // Send actual verification email using Factory pattern
            $this->mailFactoryManager->sendMail('email_verification', $user, ['token' => $token]);
        } catch (Exception $e) {
            throw new Exception('Failed to send verification email: ' . $e->getMessage());
        }
    }

    /**
     * Send password reset email
     */
    private function sendPasswordResetEmail(User $user, string $token): void
    {
        try {
            // Send actual password reset email using Factory pattern
            $this->mailFactoryManager->sendMail('password_reset', $user, ['token' => $token]);
        } catch (Exception $e) {
            throw new Exception('Failed to send password reset email: ' . $e->getMessage());
        }
    }

    /**
     * Get factory type from provider
     */
    private function getFactoryTypeFromProvider(string $provider): string
    {
        $providerMap = [
            'facebook' => 'facebook',
            'google' => 'google',
        ];

        return $providerMap[$provider] ?? 'regular';
    }

    /**
     * Determine user type based on data
     */
    private function determineUserType(array $userData): string
    {
        if (isset($userData['provider'])) {
            return $this->getFactoryTypeFromProvider($userData['provider']);
        }

        if (isset($userData['auth_provider'])) {
            return $this->getFactoryTypeFromProvider($userData['auth_provider']);
        }

        return 'regular';
    }

    /**
     * Create user with custom configuration
     */
    public function createUserWithCustomConfig(callable $factoryCallback): User
    {
        try {
            // This method is now simplified since we use Factory pattern
            // You can pass custom data and use the appropriate factory
            $userData = $factoryCallback();
            $userType = $this->determineUserType($userData);
            return $this->userFactoryManager->createUser($userType, $userData);
        } catch (ValidationException $e) {
            throw new Exception('Custom user creation failed: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Custom user creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Validate user data without creating user
     */
    public function validateUserDataOnly(array $userData): array
    {
        try {
            $userType = $this->determineUserType($userData);
            $factory = $this->userFactoryManager->getFactory($userType);
            
            // Validate data using factory
            if (!$factory->validateUserData($userData)) {
                return [
                    'valid' => false,
                    'errors' => $factory->getErrors()
                ];
            }

            return [
                'valid' => true,
                'errors' => []
            ];
        } catch (Exception $e) {
            throw new Exception('Data validation failed: ' . $e->getMessage());
        }
    }
} 