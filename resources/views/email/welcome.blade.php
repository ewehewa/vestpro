<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Welcome to Strategic Investment Pro</title>
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 600px;
      margin: 40px auto;
      background: #ffffff;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .header {
      background-color: #000000;
      padding: 30px;
      text-align: center;
      color: #ffffff;
    }
    .header h1 {
      margin: 0;
      font-size: 26px;
    }
    .content {
      padding: 30px;
      color: #444444;
    }
    .content h2 {
      color: #333333;
      font-size: 20px;
      margin-bottom: 15px;
    }
    .content p {
      line-height: 1.7;
      font-size: 16px;
    }
    .cta-button {
      margin: 30px 0;
      text-align: center;
    }
    .cta-button a {
      background-color: #34E834;
      color: #000000;
      padding: 12px 24px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
      display: inline-block;
    }
    .footer {
      text-align: center;
      background: #f0f0f0;
      padding: 20px;
      font-size: 13px;
      color: #777777;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="header">
      <img src="{{ isset($message) && is_file(public_path('assets/img/logo-icon.png')) ? $message->embed(public_path('assets/img/logo-icon.png')) : asset('assets/img/logo-icon.png') }}" width="48" height="48" alt="" style="display:block;margin:0 auto 12px;border:0;">
      <div style="font-family:Montserrat,Arial,Helvetica,sans-serif;line-height:1;margin-bottom:18px;">
        <div style="color:#34E834;font-size:26px;font-weight:800;letter-spacing:0.5px;">STRATEGIC</div>
        <div style="color:#E8D27C;font-size:11px;font-weight:600;letter-spacing:4px;padding-top:5px;">INVEST PRO</div>
      </div>
      <h1>Welcome to Strategic Investment Pro</h1>
    </div>

    <div class="content">
      <h2>Hi {{ $user->name ?? 'Valued User' }},</h2>
      <p>We're excited to have you on board! 🎉</p>
      <p>
        Strategic Investment Pro is your gateway to reliable investment opportunities and secure financial growth. 
        Our platform is designed with transparency, user security, and performance at its core.
      </p>

      <div class="cta-button">
        <a href="{{ $loginUrl }}">Log In to Your Account</a>
      </div>

      <p>If you ever need help, our support team is just a message away.</p>
      <p>Thank you for choosing Strategic Investment Pro.<br><br>Warm regards,<br>The Strategic Investment Pro Team</p>
    </div>

    <div class="footer">
      &copy; {{ date('Y') }} Strategic Investment Pro. All rights reserved.
    </div>
  </div>

</body>
</html>
