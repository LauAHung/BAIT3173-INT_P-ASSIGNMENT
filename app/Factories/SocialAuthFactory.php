<?php

namespace App\Factories;

use App\Models\User;

class SocialAuthFactory extends AuthFactory
{
    /**
     * Authenticate user with social media provider
     */
    public function authenticate(array $credentials): ?User
    {
        $this->reset();
        
        if (!$this->validateCredentials($credentials)) {
            return null;
        }

        $user = User::where('provider_id', $credentials['provider_id'])
            ->where('auth_provider', $credentials['provider'])
            ->first();

        if (!$user) {
            $this->errors[] = 'Social media account not found';
            return null;
        }

        if ($user->account_status !== 'active') {
            $this->errors[] = 'Account is not active';
            return null;
        }

        // Update last login
        $user->update(['last_login_at' => now()]);

        return $user;
    }

    /**
     * Validate social media authentication credentials
     */
    protected function validateCredentials(array $credentials): bool
    {
        if (!isset($credentials['provider']) || empty(trim($credentials['provider']))) {
            $this->errors[] = 'Provider is required';
        }

        if (!isset($credentials['provider_id']) || empty(trim($credentials['provider_id']))) {
            $this->errors[] = 'Provider ID is required';
        }

        $validProviders = ['facebook', 'google'];
        if (isset($credentials['provider']) && !in_array($credentials['provider'], $validProviders)) {
            $this->errors[] = 'Invalid provider';
        }

        return !$this->hasErrors();
    }
}
