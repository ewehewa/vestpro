@extends('email.layout')

@section('subject', 'Deposit Approved')

@section('content')
  <h2 style="color:#333;margin-top:0;">Hi {{ $deposit->user->name }},</h2>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Your deposit of <strong>${{ number_format($deposit->amount, 2) }}</strong> has been approved successfully.
  </p>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Payment Method: {{ $deposit->payment_mode }}
  </p>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Thank you for investing with us.
  </p>
  <p style="margin-top:40px;color:#555;">
    Regards,<br><strong>Strategic Investment Pro</strong>
  </p>
@endsection
