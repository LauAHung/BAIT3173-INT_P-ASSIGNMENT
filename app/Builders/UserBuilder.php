<?php

namespace App\Builders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserBuilder
{
    private User $user;
    private array $data = [];
    private array $errors = [];

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * Set basic user information with validation
     */
    public function setBasicInfo(string $firstName, string $lastName, string $email): self
    {
        // Validate input data
        if (empty(trim($firstName))) {
            $this->errors[] = 'First name is required';
        }
        
        if (empty(trim($lastName))) {
            $this->errors[] = 'Last name is required';
        }
        
        if (empty(trim($email)) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Valid email is required';
        }

        $this->data['first_name'] = trim($firstName);
        $this->data['last_name'] = trim($lastName);
        $this->data['email'] = trim(strtolower($email));
        
        return $this;
    }

    /**
     * Set password and hash it with validation
     */
    public function setPassword(string $password): self
    {
        if (strlen($password) < 8) {
            $this->errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $this->errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
        }

        $this->data['password'] = Hash::make($password);
        return $this;
    }

    /**
     * Set password for social media users (random password)
     */
    public function setSocialMediaPassword(): self
    {
        $this->data['password'] = Hash::make(Str::random(16));
        return $this;
    }

    /**
     * Set profile picture with validation
     */
    public function setProfilePicture(string $profilePicture): self
    {
        if (!empty($profilePicture)) {
            // Validate URL format
            if (!filter_var($profilePicture, FILTER_VALIDATE_URL) && !preg_match('/^\/[^\/].*/', $profilePicture)) {
                $this->errors[] = 'Profile picture must be a valid URL or file path';
            }
        }
        
        $this->data['profile_picture'] = $profilePicture;
        return $this;
    }

    /**
     * Set email subscription preferences
     */
    public function setEmailSubscription(bool $newsletter = false, bool $marketing = false, bool $updates = false): self
    {
        $this->data['email_subscription'] = json_encode([
            'newsletter' => (bool) $newsletter,
            'marketing' => (bool) $marketing,
            'updates' => (bool) $updates
        ]);
        return $this;
    }

    /**
     * Set social media information with validation
     */
    public function setSocialMediaInfo(string $provider, string $providerId, array $providerData = []): self
    {
        $validProviders = ['google', 'facebook', 'twitter', 'linkedin', 'github'];
        
        if (!in_array(strtolower($provider), $validProviders)) {
            $this->errors[] = 'Invalid social media provider. Supported providers: ' . implode(', ', $validProviders);
        }
        
        if (empty(trim($providerId))) {
            $this->errors[] = 'Social provider ID is required';
        }

        $this->data['social_provider'] = strtolower($provider);
        $this->data['social_provider_id'] = trim($providerId);
        $this->data['social_provider_data'] = json_encode($providerData);
        return $this;
    }

    /**
     * Set email verification status
     */
    public function setEmailVerified(bool $verified = false): self
    {
        $this->data['email_verified_at'] = $verified ? now() : null;
        return $this;
    }

    /**
     * Set account status with validation
     */
    public function setAccountStatus(string $status = 'active'): self
    {
        $validStatuses = ['active', 'pending_verification', 'suspended', 'deleted'];
        
        if (!in_array($status, $validStatuses)) {
            $this->errors[] = 'Invalid account status. Valid statuses: ' . implode(', ', $validStatuses);
        }

        $this->data['account_status'] = $status;
        return $this;
    }

    /**
     * Set last login timestamp
     */
    public function setLastLogin(): self
    {
        $this->data['last_login_at'] = now();
        return $this;
    }

    /**
     * Set custom data with validation
     */
    public function setCustomData(array $data): self
    {
        // Validate that custom data doesn't override required fields
        $protectedFields = ['id', 'user_id', 'created_at', 'updated_at'];
        
        foreach ($protectedFields as $field) {
            if (isset($data[$field])) {
                $this->errors[] = "Cannot override protected field: {$field}";
            }
        }

        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Validate required fields before building
     */
    private function validateRequiredFields(): void
    {
        $requiredFields = ['first_name', 'last_name', 'email', 'password'];
        
        foreach ($requiredFields as $field) {
            if (!isset($this->data[$field]) || empty($this->data[$field])) {
                $this->errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Check for email uniqueness
        if (isset($this->data['email'])) {
            $existingUser = User::where('email', $this->data['email'])->first();
            if ($existingUser) {
                $this->errors[] = 'Email address is already registered';
            }
        }
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if builder has errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Build and return the User instance with validation
     */
    public function build(): User
    {
        // Validate required fields
        $this->validateRequiredFields();

        // Throw exception if there are validation errors
        if ($this->hasErrors()) {
            throw new ValidationException(
                'User validation failed',
                $this->errors
            );
        }

        // Set default values if not provided
        if (!isset($this->data['account_status'])) {
            $this->data['account_status'] = 'pending_verification';
        }

        if (!isset($this->data['email_subscription'])) {
            $this->data['email_subscription'] = json_encode([
                'newsletter' => false,
                'marketing' => false,
                'updates' => false
            ]);
        }

        // Create the user
        $this->user->fill($this->data);
        $this->user->save();

        return $this->user;
    }

    /**
     * Build user without saving to database (for validation only)
     */
    public function buildForValidation(): array
    {
        $this->validateRequiredFields();
        return $this->data;
    }

    /**
     * Reset the builder for reuse
     */
    public function reset(): self
    {
        $this->user = new User();
        $this->data = [];
        $this->errors = [];
        return $this;
    }

    /**
     * Get the current data array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Create user from array data
     */
    public function createFromArray(array $userData): User
    {
        $this->reset();

        // Set basic info if provided
        if (isset($userData['first_name']) && isset($userData['last_name']) && isset($userData['email'])) {
            $this->setBasicInfo($userData['first_name'], $userData['last_name'], $userData['email']);
        }

        // Set password if provided
        if (isset($userData['password'])) {
            $this->setPassword($userData['password']);
        }

        // Set profile picture if provided
        if (isset($userData['profile_picture'])) {
            $this->setProfilePicture($userData['profile_picture']);
        }

        // Set email subscription if provided
        if (isset($userData['newsletter']) || isset($userData['marketing']) || isset($userData['updates'])) {
            $this->setEmailSubscription(
                $userData['newsletter'] ?? false,
                $userData['marketing'] ?? false,
                $userData['updates'] ?? false
            );
        }

        // Set social media info if provided
        if (isset($userData['social_provider']) && isset($userData['social_provider_id'])) {
            $this->setSocialMediaInfo(
                $userData['social_provider'],
                $userData['social_provider_id'],
                $userData['social_provider_data'] ?? []
            );
        }

        // Set email verification status if provided
        if (isset($userData['email_verified'])) {
            $this->setEmailVerified($userData['email_verified']);
        }

        // Set account status if provided
        if (isset($userData['account_status'])) {
            $this->setAccountStatus($userData['account_status']);
        }

        // Set custom data if provided
        if (isset($userData['custom_data']) && is_array($userData['custom_data'])) {
            $this->setCustomData($userData['custom_data']);
        }

        return $this->build();
    }
} 