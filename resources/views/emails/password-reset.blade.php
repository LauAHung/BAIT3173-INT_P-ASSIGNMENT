<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #DC2626;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .button {
            display: inline-block;
            background-color: #DC2626;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
        }
        .warning {
            background-color: #FEF3C7;
            border: 1px solid #F59E0B;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Password Reset Request</h1>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->first_name }}!</h2>
        
        <p>You are receiving this email because we received a password reset request for your account.</p>
        
        <div style="text-align: center;">
            <a href="{{ $resetUrl }}" class="button">Reset Password</a>
        </div>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #DC2626;">{{ $resetUrl }}</p>
        
        <div class="warning">
            <strong>Important:</strong> This password reset link will expire in 60 minutes. If you did not request a password reset, no further action is required.
        </div>
        
        <p>If you didn't request a password reset, please ignore this email or contact our support team if you have concerns.</p>
    </div>
    
    <div class="footer">
        <p>This email was sent to {{ $user->email }}. If you have any questions, please contact our support team.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'TravelFree') }}. All rights reserved.</p>
    </div>
</body>
</html> 