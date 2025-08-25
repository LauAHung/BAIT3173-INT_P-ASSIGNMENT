<?php

namespace App\Factories;

use App\Models\User;

class FacebookUserFactory extends UserFactory
{
    /**
     * Create a Facebook user
     */
    public function createUser(array $data): User
    {
        $this->reset();
        
        if (!$this->validateData($data)) {
            throw new \InvalidArgumentException('Invalid Facebook user data: ' . implode(', ', $this->errors));
        }

        $this->setBasicInfo($data['first_name'], $data['last_name'], $data['email']);
        $this->setSocialMediaPassword();
        $this->setSocialMediaInfo('facebook', $data['provider_id'], $data['provider_data'] ?? []);
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
     * Validate Facebook user data
     */
    protected function validateData(array $data): bool
    {
        $requiredFields = ['first_name', 'last_name', 'email', 'provider_id'];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $this->errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        // Check if Facebook user already exists
        if (isset($data['provider_id']) && User::where('provider_id', $data['provider_id'])
            ->where('auth_provider', 'facebook')->exists()) {
            $this->errors[] = 'Facebook account already exists';
        }

        return !$this->hasErrors();
    }
}
