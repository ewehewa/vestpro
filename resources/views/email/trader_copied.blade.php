@extends('email.layout')

@section('subject', 'You Are Now Copying a Trader')

@section('content')
  <h2 style="color:#333;margin-top:0;">Hi {{ $user->name }},</h2>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    You are now copying <strong>{{ $trader->name }}</strong>. Their trades will automatically be mirrored on your account going forward.
  </p>
  <p style="color:#555;font-size:16px;line-height:1.6;">
    Avg. Return: <strong>{{ $trader->average_return }}%</strong> &nbsp;|&nbsp; Win Rate: <strong>{{ $trader->win_rate }}%</strong>
  </p>
  <p style="margin-top:40px;color:#555;">
    Regards,<br><strong>{{ config('app.name') }}</strong>
  </p>
@endsection
