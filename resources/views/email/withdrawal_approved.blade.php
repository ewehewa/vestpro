@extends('email.layout')

@section('subject', 'Withdrawal Approved')

@section('content')
  <h2 style="color:#333;margin-top:0;">Hi {{ $withdrawal->user->name }},</h2>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Your withdrawal request of <strong>${{ number_format($withdrawal->amount, 2) }}</strong> via {{ strtoupper($withdrawal->receiving_mode) }} has been approved and is being processed.
  </p>
  <p style="margin-top:40px;color:#555;">
    Regards,<br><strong>{{ config('app.name') }}</strong>
  </p>
@endsection
