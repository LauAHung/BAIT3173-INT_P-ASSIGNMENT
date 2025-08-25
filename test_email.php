<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Mail;
use App\Mail\EmailVerificationMail;
use App\Mail\PasswordResetMail;
use App\Models\User;

echo "Testing Email Functionality...\n\n";

try {
    // Test 1: Check if mail configuration is set
    echo "1. Checking mail configuration...\n";
    $mailConfig = config('mail.default');
    echo "   Mail driver: " . $mailConfig . "\n";
    
    if ($mailConfig === 'log') {
        echo "   ⚠️  Warning: Using 'log' driver - emails will be logged, not sent\n";
        echo "   To send real emails, configure SMTP in your .env file\n\n";
    } else {
        echo "   ✅ Mail driver configured for real email sending\n\n";
    }
    
    // Test 2: Create a test user
    echo "2. Creating test user for email testing...\n";
    $testUser = new User();
    $testUser->first_name = 'Test';
    $testUser->last_name = 'User';
    $testUser->email = 'test@example.com';
    $testUser->user_id = 999;
    echo "   ✅ Test user created\n\n";
    
    // Test 3: Test email verification mail
    echo "3. Testing email verification mail...\n";
    $token = 'test-verification-token-123';
    $verificationMail = new EmailVerificationMail($testUser, $token);
    
    echo "   Subject: " . $verificationMail->envelope()->subject . "\n";
    echo "   To: " . $testUser->email . "\n";
    echo "   ✅ Email verification mail created successfully\n\n";
    
    // Test 4: Test password reset mail
    echo "4. Testing password reset mail...\n";
    $resetToken = 'test-reset-token-456';
    $resetMail = new PasswordResetMail($testUser, $resetToken);
    
    echo "   Subject: " . $resetMail->envelope()->subject . "\n";
    echo "   To: " . $testUser->email . "\n";
    echo "   ✅ Password reset mail created successfully\n\n";
    
    // Test 5: Test actual email sending (if configured)
    if ($mailConfig !== 'log') {
        echo "5. Testing actual email sending...\n";
        echo "   This will attempt to send a test email...\n";
        
        try {
            Mail::raw('This is a test email from your Laravel application.', function($message) {
                $message->to('test@example.com')
                        ->subject('Test Email from Laravel');
            });
            echo "   ✅ Test email sent successfully!\n";
        } catch (Exception $e) {
            echo "   ❌ Failed to send test email: " . $e->getMessage() . "\n";
            echo "   Please check your email configuration in .env file\n";
        }
    } else {
        echo "5. Skipping actual email test (using log driver)\n";
        echo "   To test real email sending, configure SMTP in .env\n";
    }
    
    echo "\n📧 Email Setup Instructions:\n";
    echo "1. Create a .env file in your project root\n";
    echo "2. Add your email configuration (see EMAIL_SETUP_GUIDE.md)\n";
    echo "3. For Gmail, use app password, not regular password\n";
    echo "4. Test with: php artisan tinker\n";
    echo "5. Run: Mail::raw('Test', function($m) { \$m->to('your@email.com')->subject('Test'); });\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} 