<?php

namespace App\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmailAuthFactory extends AuthFactory
{
    /**
     * Authenticate user with email and password
     */
    public function authenticate(array $credentials): ?User
    {
        $this->reset();
        
        if (!$this->validateCredentials($credentials)) {
            return null;
        }

        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            $this->errors[] = 'Invalid email or password';
            return null;
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            $this->errors[] = 'Invalid email or password';
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
     * Validate email authentication credentials
     */
    protected function validateCredentials(array $credentials): bool
    {
        if (!isset($credentials['email']) || empty(trim($credentials['email']))) {
            $this->errors[] = 'Email is required';
        }

        if (!isset($credentials['password']) || empty($credentials['password'])) {
            $this->errors[] = 'Password is required';
        }

        if (isset($credentials['email']) && !filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Invalid email format';
        }

        return !$this->hasErrors();
    }
}
