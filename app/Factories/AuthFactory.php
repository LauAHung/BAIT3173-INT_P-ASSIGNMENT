<?php

namespace App\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

abstract class AuthFactory
{
    protected array $errors = [];

    /**
     * Authenticate user
     */
    abstract public function authenticate(array $credentials): ?User;

    /**
     * Validate authentication credentials
     */
    abstract protected function validateCredentials(array $credentials): bool;

    /**
     * Get authentication errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if there are authentication errors
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
        $this->errors = [];
    }
}
