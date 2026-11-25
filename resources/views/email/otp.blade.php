@extends('email.layout')

@section('subject', 'Your OTP')

@section('content')
  <h2 style="color:#333;margin-top:0;">Hello!</h2>
  <p style="color:#555;font-size:16px;line-height:1.6;">Your one-time password (OTP) is:</p>
  <div style="text-align:center;margin:30px 0;">
    <span style="display:inline-block;background-color:#34E834;color:#000000;font-size:28px;font-weight:bold;padding:12px 24px;border-radius:6px;letter-spacing:4px;">
      {{ $otp }}
    </span>
  </div>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Please use this OTP for withdrawal. Do not share it with anyone.
  </p>
  <p style="margin-top:40px;color:#555;">
    Thanks,<br><strong>The Strategic Investment Pro Team</strong>
  </p>
@endsection
