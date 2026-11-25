@extends('email.layout')

@section('subject', 'Investment Successful')

@section('content')
  <h2 style="color:#333;margin-top:0;">Investment Confirmed</h2>
  <p style="color:#555;font-size:16px;line-height:1.6;">Hello {{ $investment->user->name }},</p>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Thank you for investing with us. Here are the details of your investment:
  </p>
  <ul style="color:#555;font-size:16px;line-height:1.8;">
    <li><strong>Plan:</strong> {{ $investment->plan->name }}</li>
    <li><strong>Amount:</strong> ${{ number_format($investment->amount, 2) }}</li>
    <li><strong>Duration:</strong> {{ $investment->plan->duration }} days</li>
  </ul>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    You will start receiving your returns based on the plan's schedule.
  </p>
  <p style="margin-top:40px;color:#555;">
    Regards,<br><strong>Strategic Investment Pro</strong>
  </p>
@endsection
