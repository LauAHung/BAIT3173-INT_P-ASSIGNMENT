<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use App\Models\User;

echo "Testing mail configuration...\n";

// Create a test user
$user = new User();
$user->user_id = 1;
$user->first_name = 'Test';
$user->last_name = 'User';
$user->email = 'test@example.com';
$user->email_verification_token = 'test-token-123';

try {
    // Test sending verification email
    Mail::to('test@example.com')->send(new EmailVerificationMail($user, 'test-token-123'));
    echo "Mail sent successfully to log\n";
    
    // Check if user was created in database
    $dbUser = User::where('email_verification_token', 'test-token-123')->first();
    if ($dbUser) {
        echo "User found in database with token\n";
        echo "User ID: " . $dbUser->user_id . "\n";
        echo "Email: " . $dbUser->email . "\n";
        echo "Token: " . $dbUser->email_verification_token . "\n";
        echo "Status: " . $dbUser->account_status . "\n";
    } else {
        echo "No user found with token in database\n";
    }
    
} catch (Exception $e) {
    echo "Error sending mail: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
