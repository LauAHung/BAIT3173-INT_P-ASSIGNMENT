<?php

namespace App\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

abstract class UserFactory
{
    protected array $data = [];
    protected array $errors = [];

    /**
     * Create a new user instance
     */
    abstract public function createUser(array $data): User;

    /**
     * Validate user data
     */
    abstract protected function validateData(array $data): bool;

    /**
     * Public method to validate user data
     */
    public function validateUserData(array $data): bool
    {
        return $this->validateData($data);
    }

    /**
     * Set basic user information
     */
    protected function setBasicInfo(string $firstName, string $lastName, string $email): void
    {
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
    }

    /**
     * Set password with validation
     */
    protected function setPassword(string $password): void
    {
        if (strlen($password) < 8) {
            $this->errors[] = 'Password must be at least 8 characters long';
        }
        
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $password)) {
            $this->errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, and one number';
        }

        $this->data['password'] = Hash::make($password);
    }

    /**
     * Set social media password
     */
    protected function setSocialMediaPassword(): void
    {
        $this->data['password'] = Hash::make(Str::random(16));
    }

    /**
     * Set profile picture
     */
    protected function setProfilePicture(string $profilePicture): void
    {
        if (!empty($profilePicture)) {
            if (!filter_var($profilePicture, FILTER_VALIDATE_URL) && !preg_match('/^\/[^\/].*/', $profilePicture)) {
                $this->errors[] = 'Profile picture must be a valid URL or file path';
            }
        }
        
        $this->data['profile_picture'] = $profilePicture;
    }

    /**
     * Set email subscription preferences
     */
    protected function setEmailSubscription(bool $newsletter = false, bool $marketing = false, bool $updates = false): void
    {
        $this->data['email_subscription'] = json_encode([
            'newsletter' => (bool) $newsletter,
            'marketing' => (bool) $marketing,
            'updates' => (bool) $updates
        ]);
    }

    /**
     * Set social media information
     */
    protected function setSocialMediaInfo(string $provider, string $providerId, array $providerData = []): void
    {
        $this->data['auth_provider'] = $provider;
        $this->data['provider_id'] = $providerId;
        $this->data['provider_data'] = json_encode($providerData);
    }

    /**
     * Set email verification status
     */
    protected function setEmailVerified(bool $verified = false): void
    {
        $this->data['email_verified_at'] = $verified ? now() : null;
    }

    /**
     * Set account status
     */
    protected function setAccountStatus(string $status = 'active'): void
    {
        $validStatuses = ['active', 'inactive', 'suspended', 'pending'];
        if (!in_array($status, $validStatuses)) {
            $this->errors[] = 'Invalid account status';
            return;
        }
        
        $this->data['account_status'] = $status;
    }

    /**
     * Set last login timestamp
     */
    protected function setLastLogin(): void
    {
        $this->data['last_login_at'] = now();
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if there are validation errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Reset factory state
     */
    protected function reset(): void
    {
        $this->data = [];
        $this->errors = [];
    }
}
