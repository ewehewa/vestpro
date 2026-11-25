@extends('email.layout')

@section('subject', ($transaction->isCredit() ? 'Credit' : 'Debit') . ' Alert')

@section('content')
  @php
    $isCredit = $transaction->isCredit();
    $accent = $isCredit ? '#22B822' : '#DC2626';
    $currency = $user->preferred_currency ?? 'USD';
  @endphp

  <h2 style="color:#333;margin-top:0;">Hi {{ $user->name }},</h2>

  <p style="color:#555;font-size:16px;line-height:1.6;">
    Your account has been <strong>{{ $isCredit ? 'credited' : 'debited' }}</strong>. Here are the details:
  </p>

  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:24px 0;border:1px solid #eeeeee;border-radius:8px;">
    <tr>
      <td align="center" style="padding:20px;border-bottom:1px solid #eeeeee;">
        <div style="font-size:13px;letter-spacing:2px;text-transform:uppercase;color:{{ $accent }};font-weight:bold;">
          {{ $isCredit ? 'Credit Alert' : 'Debit Alert' }}
        </div>
        <div style="font-size:30px;font-weight:bold;color:{{ $accent }};margin-top:6px;">
          {{ $isCredit ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
        </div>
      </td>
    </tr>
    <tr>
      <td style="padding:16px 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-size:14px;color:#555;">
          <tr>
            <td style="padding:6px 0;color:#888;">Type</td>
            <td align="right" style="padding:6px 0;">{{ $transaction->categoryLabel() }}</td>
          </tr>
          <tr>
            <td style="padding:6px 0;color:#888;">Description</td>
            <td align="right" style="padding:6px 0;">{{ $transaction->description }}</td>
          </tr>
          <tr>
            <td style="padding:6px 0;color:#888;">Date</td>
            <td align="right" style="padding:6px 0;">{{ $transaction->created_at->format('M j, Y g:i A') }} (UTC)</td>
          </tr>
          <tr>
            <td style="padding:6px 0;color:#888;">Reference</td>
            <td align="right" style="padding:6px 0;">{{ $transaction->referenceCode() }}</td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" style="padding:18px 20px;background-color:#f7f7f7;border-top:1px solid #eeeeee;">
        <div style="font-size:13px;color:#888;">Available Balance</div>
        <div style="font-size:22px;font-weight:bold;color:#000000;margin-top:4px;">
          ${{ number_format($transaction->balance_after, 2) }}
        </div>
        @if ($currency !== 'USD')
          <div style="font-size:13px;color:#888;margin-top:2px;">&asymp; {{ money($transaction->balance_after, $currency) }}</div>
        @endif
      </td>
    </tr>
  </table>

  <p style="color:#888;font-size:13px;line-height:1.6;">
    If you did not expect this transaction, please contact our support team immediately.
  </p>

  <p style="margin-top:30px;color:#555;">
    Regards,<br><strong>Strategic Investment Pro</strong>
  </p>
@endsection
