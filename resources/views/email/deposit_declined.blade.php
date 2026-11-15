@extends('email.layout')

@section('subject', 'Deposit Declined')

@section('content')
  <h2 style="color:#333;margin-top:0;">Hi {{ $deposit->user->name }},</h2>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Your deposit of <strong>${{ number_format($deposit->amount, 2) }}</strong> via {{ strtoupper($deposit->payment_mode) }} was declined.
  </p>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    If you believe this is a mistake, please contact support.
  </p>
  <p style="margin-top:40px;color:#555;">
    Regards,<br><strong>{{ config('app.name') }}</strong>
  </p>
@endsection
