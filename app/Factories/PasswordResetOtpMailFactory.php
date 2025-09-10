<?php

namespace App\Factories;

use App\Models\User;
use App\Mail\PasswordResetOtpMail;
use Illuminate\Support\Facades\Mail;

class PasswordResetOtpMailFactory extends MailFactory
{
    /** Send password reset OTP mail */
    public function sendMail(User $user, array $data = []): bool
    {
        $this->reset();

        if (!$this->validateMailData($user, $data)) {
            return false;
        }

        try {
            $otp = $data['otp'] ?? '';
            Mail::to($user->email)->send(new PasswordResetOtpMail($user, $otp));
            return true;
        } catch (\Exception $e) {
            $this->errors[] = 'Failed to send password reset OTP mail: ' . $e->getMessage();
            return false;
        }
    }

    /** Validate OTP mail data */
    protected function validateMailData(User $user, array $data = []): bool
    {
        if (!$user) { $this->errors[] = 'User is required'; }
        if (!$user->email) { $this->errors[] = 'User email is required'; }
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) { $this->errors[] = 'Invalid user email format'; }
        if (!isset($data['otp']) || !preg_match('/^\d{6}$/', (string)$data['otp'])) { $this->errors[] = 'Valid 6-digit OTP is required'; }
        return !$this->hasErrors();
    }
}


