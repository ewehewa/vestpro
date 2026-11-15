<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>@yield('subject', config('app.name'))</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f4;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td align="center" style="padding:20px 0;">
        <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:8px;overflow:hidden;">
          <tr style="background-color:#2563eb;">
            <td align="center" style="padding:20px;">
              <span style="color:#ffffff;font-size:20px;font-weight:bold;">{{ config('app.name') }}</span>
            </td>
          </tr>
          <tr>
            <td style="padding:30px;">
              @yield('content')
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:15px;background-color:#f0f0f0;font-size:13px;color:#888;">
              &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
