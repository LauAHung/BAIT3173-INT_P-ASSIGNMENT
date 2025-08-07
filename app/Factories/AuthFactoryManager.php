<?php

namespace App\Factories;

use App\Models\User;

class AuthFactoryManager
{
    private array $factories = [];

    public function __construct()
    {
        $this->factories = [
            'email' => new EmailAuthFactory(),
            'social' => new SocialAuthFactory(),
        ];
    }

    /**
     * Authenticate user based on type
     */
    public function authenticate(string $type, array $credentials): ?User
    {
        if (!isset($this->factories[$type])) {
            throw new \InvalidArgumentException("Unknown authentication type: {$type}");
        }

        return $this->factories[$type]->authenticate($credentials);
    }

    /**
     * Get factory by type
     */
    public function getFactory(string $type): AuthFactory
    {
        if (!isset($this->factories[$type])) {
            throw new \InvalidArgumentException("Unknown authentication type: {$type}");
        }

        return $this->factories[$type];
    }

    /**
     * Get available authentication types
     */
    public function getAvailableTypes(): array
    {
        return array_keys($this->factories);
    }

    /**
     * Check if authentication type is supported
     */
    public function isSupported(string $type): bool
    {
        return isset($this->factories[$type]);
    }
}
