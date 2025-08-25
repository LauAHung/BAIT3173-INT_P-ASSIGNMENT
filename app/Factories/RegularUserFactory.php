<?php

namespace App\Factories;

use App\Models\User;

class RegularUserFactory extends UserFactory
{
    /**
     * Create a regular user with email and password
     */
    public function createUser(array $data): User
    {
        $this->reset();
        
        if (!$this->validateData($data)) {
            throw new \InvalidArgumentException('Invalid user data: ' . implode(', ', $this->errors));
        }

        $this->setBasicInfo($data['first_name'], $data['last_name'], $data['email']);
        $this->setPassword($data['password']);
        $this->setEmailSubscription(
            $data['newsletter'] ?? false,
            $data['marketing'] ?? false,
            $data['updates'] ?? false
        );
        $this->setAccountStatus('pending');
        $this->setEmailVerified(false);

        if (isset($data['profile_picture'])) {
            $this->setProfilePicture($data['profile_picture']);
        }

        return User::create($this->data);
    }

    /**
     * Validate regular user data
     */
    protected function validateData(array $data): bool
    {
        $requiredFields = ['first_name', 'last_name', 'email', 'password'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $this->errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Check if email already exists
        if (isset($data['email']) && User::where('email', trim(strtolower($data['email'])))->exists()) {
            $this->errors[] = 'Email already exists';
        }

        return !$this->hasErrors();
    }
}
