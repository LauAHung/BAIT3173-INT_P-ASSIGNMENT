<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Password Reset OTP</title>
</head>
<body style="font-family:Arial,Helvetica,sans-serif;line-height:1.6;color:#111;">
  <h2>Hello {{ $name }},</h2>
  <p>Your password reset OTP is:</p>
  <p style="font-size:24px;font-weight:700;letter-spacing:6px;">{{ $otp }}</p>
  <p>This code expires in 10 minutes. If you did not request this, you can safely ignore this email.</p>
  <p>Thanks,<br>TravelFree Team</p>
</body>
</html>


