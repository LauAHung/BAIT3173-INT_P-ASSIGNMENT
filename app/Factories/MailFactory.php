<?php

namespace App\Factories;

use App\Models\User;
use Illuminate\Support\Facades\Mail;

abstract class MailFactory
{
    protected array $errors = [];

    /**
     * Send mail
     */
    abstract public function sendMail(User $user, array $data = []): bool;

    /**
     * Validate mail data
     */
    abstract protected function validateMailData(User $user, array $data = []): bool;

    /**
     * Get mail errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if there are mail errors
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
