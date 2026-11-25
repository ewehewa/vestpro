<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Email Verification Code</title>
</head>
<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4;">

  <div style="max-width: 600px; margin: 30px auto; background-color: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);">
    <div style="text-align: center; margin-bottom: 24px;">
      <img src="{{ isset($message) && is_file(public_path('assets/img/logo-icon.png')) ? $message->embed(public_path('assets/img/logo-icon.png')) : asset('assets/img/logo-icon.png') }}" width="40" height="40" alt="" style="display: inline-block; vertical-align: middle; border: 0;">
      <span style="display: inline-block; vertical-align: middle; margin-left: 8px; text-align: left; font-family: Montserrat, Arial, Helvetica, sans-serif; line-height: 1;"><span style="display: block; color: #1FAF1F; font-size: 22px; font-weight: 800; letter-spacing: 0.5px;">STRATEGIC</span><span style="display: block; color: #A8841F; font-size: 10px; font-weight: 600; letter-spacing: 3px; padding-top: 4px;">INVEST PRO</span></span>
    </div>
    
    <h2 style="color: #333333; font-weight: 600; margin-bottom: 20px;">Hi there,</h2>

    <p style="font-size: 16px; color: #555555; margin-bottom: 20px;">
      Thank you for signing up with Strategic Investment Pro! Please use the code below to verify your email address:
    </p>

    <div style="text-align: center; margin: 30px 0;">
      <span style="display: inline-block; background-color: #34E834; color: #000000; font-size: 28px; font-weight: bold; padding: 12px 24px; border-radius: 6px; letter-spacing: 4px;">
        {{ $otp }}
      </span>
    </div>

    <p style="font-size: 16px; color: #555555; margin-bottom: 30px;">
      Enter this code in the app to complete your registration.
    </p>

    <p style="font-size: 14px; color: #999999; text-align: center;">
      If you didn't sign up for this account, you can safely ignore this email.
    </p>
  </div>

</body>
</html>
