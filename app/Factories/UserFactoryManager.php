<?php

namespace App\Factories;

use App\Models\User;

class UserFactoryManager
{
    private array $factories = [];

    public function __construct()
    {
        $this->factories = [
            'regular' => new RegularUserFactory(),
            'facebook' => new FacebookUserFactory(),
            'google' => new GoogleUserFactory(),
        ];
    }

    /**
     * Create user based on type
     */
    public function createUser(string $type, array $data): User
    {
        if (!isset($this->factories[$type])) {
            throw new \InvalidArgumentException("Unknown user type: {$type}");
        }

        return $this->factories[$type]->createUser($data);
    }

    /**
     * Get factory by type
     */
    public function getFactory(string $type): UserFactory
    {
        if (!isset($this->factories[$type])) {
            throw new \InvalidArgumentException("Unknown user type: {$type}");
        }

        return $this->factories[$type];
    }

    /**
     * Get available user types
     */
    public function getAvailableTypes(): array
    {
        return array_keys($this->factories);
    }

    /**
     * Check if user type is supported
     */
    public function isSupported(string $type): bool
    {
        return isset($this->factories[$type]);
    }
}
