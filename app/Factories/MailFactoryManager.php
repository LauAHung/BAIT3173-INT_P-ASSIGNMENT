<?php

namespace App\Factories;

use App\Models\User;

class MailFactoryManager
{
    private array $factories = [];

    public function __construct()
    {
        $this->factories = [
            'email_verification' => new EmailVerificationMailFactory(),
            'password_reset' => new PasswordResetMailFactory(),
            'password_reset_otp' => new PasswordResetOtpMailFactory(),
        ];
    }

    /**
     * Send mail based on type
     */
    public function sendMail(string $type, User $user, array $data = []): bool
    {
        if (!isset($this->factories[$type])) {
            throw new \InvalidArgumentException("Unknown mail type: {$type}");
        }

        return $this->factories[$type]->sendMail($user, $data);
    }

    /**
     * Get factory by type
     */
    public function getFactory(string $type): MailFactory
    {
        if (!isset($this->factories[$type])) {
            throw new \InvalidArgumentException("Unknown mail type: {$type}");
        }

        return $this->factories[$type];
    }

    /**
     * Get available mail types
     */
    public function getAvailableTypes(): array
    {
        return array_keys($this->factories);
    }

    /**
     * Check if mail type is supported
     */
    public function isSupported(string $type): bool
    {
        return isset($this->factories[$type]);
    }
}
