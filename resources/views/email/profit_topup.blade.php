@extends('email.layout')

@section('subject', 'Profit Added to Your Account')

@section('content')
  <h2 style="color:#333;margin-top:0;">Hi {{ $user->name }},</h2>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    A profit of <strong>${{ number_format($amount, 2) }}</strong> has been added to your account.
  </p>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Your new balance: <strong>${{ number_format($user->balance, 2) }}</strong>
    @if (($user->preferred_currency ?? 'USD') !== 'USD')
      <span style="font-size:13px;color:#888;">(&asymp; {{ money($user->balance, $user->preferred_currency) }})</span>
    @endif
  </p>
  <p style="margin-top:40px;color:#555;">
    Regards,<br><strong>{{ config('app.name') }}</strong>
  </p>
@endsection
