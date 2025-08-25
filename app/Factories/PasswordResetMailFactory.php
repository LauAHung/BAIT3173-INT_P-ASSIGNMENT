<?php

namespace App\Factories;

use App\Models\User;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;

class PasswordResetMailFactory extends MailFactory
{
    /**
     * Send password reset mail
     */
    public function sendMail(User $user, array $data = []): bool
    {
        $this->reset();
        
        if (!$this->validateMailData($user, $data)) {
            return false;
        }

        try {
            $token = $data['token'] ?? '';
            Mail::to($user->email)->send(new PasswordResetMail($user, $token));
            return true;
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to send password reset mail: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Validate password reset mail data
     */
    protected function validateMailData(User $user, array $data = []): bool
    {
        if (!$user) {
            $this->errors[] = 'User is required';
        }

        if (!$user->email) {
            $this->errors[] = 'User email is required';
        }

        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = 'Invalid user email format';
        }

        if (!isset($data['token']) || empty($data['token'])) {
            $this->errors[] = 'Reset token is required';
        }

        return !$this->hasErrors();
    }
}
