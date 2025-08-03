<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Your Email</title>
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
            background-color: #4F46E5;
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
            background-color: #4F46E5;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Welcome to {{ config('app.name', 'TravelFree') }}!</h1>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->first_name }}!</h2>
        
        <p>Thank you for registering with us. To complete your registration, please verify your email address by clicking the button below:</p>
        
        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="button">Verify Email Address</a>
        </div>
        
        <p>If the button doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #4F46E5;">{{ $verificationUrl }}</p>
        
        <p>This verification link will expire in 24 hours.</p>
        
        <p>If you didn't create an account, no further action is required.</p>
    </div>
    
    <div class="footer">
        <p>This email was sent to {{ $user->email }}. If you have any questions, please contact our support team.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name', 'TravelFree') }}. All rights reserved.</p>
    </div>
</body>
</html> 