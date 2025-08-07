<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Factories\UserFactoryManager;
use App\Factories\MailFactoryManager;
use App\Services\UserRegistrationService;
use App\Models\User;

echo "Testing complete signup and verification flow...\n";

try {
    // Create service instances
    $userFactoryManager = new UserFactoryManager();
    $mailFactoryManager = new MailFactoryManager();
    $userRegistrationService = new UserRegistrationService($userFactoryManager, $mailFactoryManager);
    
    // Test user data
    $userData = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'TestPassword123',
        'newsletter' => true,
        'marketing' => false,
        'updates' => true
    ];
    
    echo "1. Creating user...\n";
    $user = $userFactoryManager->createUser('regular', $userData);
    echo "User created with ID: " . $user->user_id . "\n";
    echo "User email: " . $user->email . "\n";
    echo "User status: " . $user->account_status . "\n";
    
    echo "2. Sending verification email...\n";
    $userRegistrationService->sendVerificationEmail($user);
    echo "Verification email sent\n";
    
    // Refresh user from database to get the token
    $user->refresh();
    echo "3. User token: " . ($user->email_verification_token ?? 'NULL') . "\n";
    
    if ($user->email_verification_token) {
        echo "4. Testing verification...\n";
        $success = $userRegistrationService->verifyEmail($user->email_verification_token);
        echo "Verification result: " . ($success ? 'SUCCESS' : 'FAILED') . "\n";
        
        // Refresh user again to see updated status
        $user->refresh();
        echo "5. User status after verification: " . $user->account_status . "\n";
        echo "6. Email verified at: " . ($user->email_verified_at ?? 'NULL') . "\n";
        echo "7. Token after verification: " . ($user->email_verification_token ?? 'NULL') . "\n";
    } else {
        echo "ERROR: No verification token generated!\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Test completed.\n";
