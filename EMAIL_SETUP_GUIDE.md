# Email Setup Guide

## Overview
The User Module now includes real email functionality for:
- Email verification during registration
- Password reset emails
- Welcome emails

## Email Configuration

### 1. Create .env file
Create a `.env` file in your project root with the following email settings:

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="TravelFree"
```

### 2. Gmail Setup (Recommended)
To use Gmail for sending emails:

1. **Enable 2-Factor Authentication** on your Gmail account
2. **Generate an App Password**:
   - Go to Google Account settings
   - Security → 2-Step Verification → App passwords
   - Generate a new app password for "Mail"
3. **Use the app password** in your `.env` file (not your regular Gmail password)

### 3. Alternative Email Providers

#### Mailgun
```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-secret
```

#### SendGrid
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
```

#### Amazon SES
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-aws-access-key
AWS_SECRET_ACCESS_KEY=your-aws-secret-key
AWS_DEFAULT_REGION=us-east-1
```

## Testing Email Functionality

### 1. Test Registration with Email
```bash
# Register a new user
# The system will automatically send a verification email
```

### 2. Test Password Reset
```bash
# Go to /signin
# Click "Forgot your password"
# Enter your email
# Check your email for the reset link
```

### 3. Manual Email Test
You can test email functionality using Laravel's tinker:

```bash
php artisan tinker
```

```php
// Test email sending
Mail::raw('Test email from Laravel', function($message) {
    $message->to('your-email@example.com')
            ->subject('Test Email');
});
```

## Email Templates

The system includes two email templates:

1. **Email Verification** (`resources/views/emails/verification.blade.php`)
   - Sent when users register
   - Contains verification link
   - Expires in 24 hours

2. **Password Reset** (`resources/views/emails/password-reset.blade.php`)
   - Sent when users request password reset
   - Contains reset link
   - Expires in 60 minutes

## Troubleshooting

### Common Issues:

1. **"Connection refused" error**
   - Check your SMTP settings
   - Verify port and encryption settings
   - Ensure firewall allows SMTP traffic

2. **"Authentication failed" error**
   - Verify username and password
   - For Gmail, use app password, not regular password
   - Check if 2FA is enabled

3. **Emails going to spam**
   - Configure SPF and DKIM records
   - Use a reputable email provider
   - Set proper "From" address

4. **No emails received**
   - Check spam folder
   - Verify email address is correct
   - Check mail server logs

### Debug Mode
To see email errors, set in `.env`:
```env
APP_DEBUG=true
MAIL_LOG_CHANNEL=mail
```

## Security Notes

1. **Never commit your .env file** to version control
2. **Use app passwords** for Gmail, not regular passwords
3. **Verify email addresses** before sending
4. **Rate limit** email sending to prevent abuse
5. **Monitor email delivery** and bounce rates

## Production Setup

For production:

1. **Use a reliable email service** (SendGrid, Mailgun, Amazon SES)
2. **Set up proper DNS records** (SPF, DKIM, DMARC)
3. **Monitor email metrics** (delivery, bounce, spam rates)
4. **Implement email queuing** for better performance
5. **Set up email logging** for debugging

## Email Queue (Optional)

For better performance, you can queue emails:

1. **Set up a queue driver** in `.env`:
```env
QUEUE_CONNECTION=database
```

2. **Create the jobs table**:
```bash
php artisan queue:table
php artisan migrate
```

3. **Start the queue worker**:
```bash
php artisan queue:work
```

4. **Make emails queued** by implementing `ShouldQueue` in the mail classes.

## Support

If you encounter issues:
1. Check the Laravel logs in `storage/logs/laravel.log`
2. Verify your email configuration
3. Test with a simple email first
4. Check your email provider's documentation 