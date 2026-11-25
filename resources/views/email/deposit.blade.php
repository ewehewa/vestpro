@extends('email.layout')

@section('subject', 'Deposit Submitted')

@section('content')
  <h2 style="color:#333;margin-top:0;">Hello {{ $deposit->user->name }},</h2>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Your deposit of <strong>${{ number_format($deposit->amount, 2) }}</strong> using <strong>{{ $deposit->payment_mode }}</strong> has been received and is pending approval.
  </p>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    We'll notify you once it's reviewed.
  </p>
  <p style="margin-top:40px;color:#555;">
    Thank you,<br><strong>Strategic Investment Pro Team</strong>
  </p>
@endsection
