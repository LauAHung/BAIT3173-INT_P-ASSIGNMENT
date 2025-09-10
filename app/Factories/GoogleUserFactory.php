<?php

namespace App\Factories;

use App\Models\User;

class GoogleUserFactory extends UserFactory
{
    /**
     * Create a Google user
     */
    public function createUser(array $data): User
    {
        $this->reset();
        
        if (!$this->validateData($data)) {
            throw new \InvalidArgumentException('Invalid Google user data: ' . implode(', ', $this->errors));
        }

        $this->setBasicInfo($data['first_name'], $data['last_name'], $data['email']);
        $this->setSocialMediaPassword();
        $this->setSocialMediaInfo('google', $data['provider_id'], $data['provider_data'] ?? []);
        $this->setEmailSubscription(
            $data['newsletter'] ?? false,
            $data['marketing'] ?? false,
            $data['updates'] ?? false
        );
        $this->setAccountStatus('active');
        $this->setEmailVerified(true);

        if (isset($data['profile_picture'])) {
            $this->setProfilePicture($data['profile_picture']);
        }

        return User::create($this->data);
    }

    /**
     * Validate Google user data
     */
    protected function validateData(array $data): bool
    {
        $requiredFields = ['first_name', 'last_name', 'email', 'provider_id'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $this->errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Check if Google user already exists (use social_* columns)
        if (isset($data['provider_id']) && User::where('social_provider_id', $data['provider_id'])
            ->where('social_provider', 'google')->exists()) {
            $this->errors[] = 'Google account already exists';
        }

        return !$this->hasErrors();
    }
}
