<?php

namespace App\Factories;

use App\Models\User;
use App\Mail\EmailVerificationMail;
use Illuminate\Support\Facades\Mail;

class EmailVerificationMailFactory extends MailFactory
{
    /**
     * Send email verification mail
     */
    public function sendMail(User $user, array $data = []): bool
    {
        $this->reset();
        
        if (!$this->validateMailData($user, $data)) {
            return false;
        }

        try {
            $token = $data['token'] ?? '';
            Mail::to($user->email)->send(new EmailVerificationMail($user, $token));
            return true;
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to send email verification mail: ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Validate email verification mail data
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

        if ($user->email_verified_at) {
            $this->errors[] = 'User email is already verified';
        }

        return !$this->hasErrors();
    }
}
