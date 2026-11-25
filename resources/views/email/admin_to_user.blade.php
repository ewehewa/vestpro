<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ $subjectLine ?? 'Message from Admin' }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" style="padding: 20px 0;">
        <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
          
          <!-- Logo -->
          <tr style="background-color:#000000;">
            <td align="center" style="padding:20px;">
              <table cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                  <td style="padding-right:10px;"><img src="{{ isset($message) && is_file(public_path('assets/img/logo-icon.png')) ? $message->embed(public_path('assets/img/logo-icon.png')) : asset('assets/img/logo-icon.png') }}" width="36" height="36" alt="" style="display:block;border:0;"></td>
                  <td style="font-family:Montserrat,Arial,Helvetica,sans-serif;line-height:1;"><div style="color:#34E834;font-size:22px;font-weight:800;letter-spacing:0.5px;">STRATEGIC</div><div style="color:#E8D27C;font-size:10px;font-weight:600;letter-spacing:3px;padding-top:4px;">INVEST PRO</div></td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Email Body -->
          <tr>
            <td style="padding: 30px;">
              <h2 style="color: #333333; margin-top: 0;">Hello,</h2>
              <p style="color: #555555; font-size: 16px; line-height: 1.6;">
                {!! nl2br(e($messageBody)) !!}
              </p>
              <p style="margin-top: 40px; color: #555;">
                Regards,<br><strong>The Admin Team</strong>
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td align="center" style="padding: 15px; background-color: #f0f0f0; font-size: 13px; color: #888;">
              &copy; {{ date('Y') }} Strategic Investment Pro. All rights reserved.
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
