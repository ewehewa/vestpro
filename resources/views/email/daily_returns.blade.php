@extends('email.layout')

@section('subject', 'Investment Returns Credited')

@section('content')
  @php $currency = $user->preferred_currency ?? 'USD'; @endphp

  <h2 style="color:#333;margin-top:0;">Hi {{ $user->name }},</h2>

  <p style="color:#555;font-size:16px;line-height:1.6;">
    Your investment returns have been credited to your account.
  </p>

  <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin:24px 0;border:1px solid #eeeeee;border-radius:8px;">
    <tr>
      <td align="center" style="padding:20px;border-bottom:1px solid #eeeeee;">
        <div style="font-size:13px;letter-spacing:2px;text-transform:uppercase;color:#22B822;font-weight:bold;">Credit Alert</div>
        <div style="font-size:30px;font-weight:bold;color:#22B822;margin-top:6px;">+${{ number_format($total, 2) }}</div>
      </td>
    </tr>
    <tr>
      <td style="padding:12px 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-size:14px;color:#555;">
          @foreach ($items as $item)
            <tr>
              <td style="padding:8px 0;border-bottom:1px solid #f2f2f2;">
                <strong style="color:#333;">{{ $item['plan'] }}</strong><br>
                <span style="font-size:12px;color:#888;">
                  @if ($item['kind'] === 'principal')
                    Plan completed: capital returned
                  @else
                    Daily return, day {{ $item['day'] }} of {{ $item['days'] }}
                  @endif
                </span>
              </td>
              <td align="right" style="padding:8px 0;border-bottom:1px solid #f2f2f2;color:#22B822;font-weight:bold;white-space:nowrap;">
                +${{ number_format($item['amount'], 2) }}
              </td>
            </tr>
          @endforeach
        </table>
      </td>
    </tr>
    <tr>
      <td align="center" style="padding:18px 20px;background-color:#f7f7f7;border-top:1px solid #eeeeee;">
        <div style="font-size:13px;color:#888;">Available Balance</div>
        <div style="font-size:22px;font-weight:bold;color:#000000;margin-top:4px;">${{ number_format($balance, 2) }}</div>
        @if ($currency !== 'USD')
          <div style="font-size:13px;color:#888;margin-top:2px;">&asymp; {{ money($balance, $currency) }}</div>
        @endif
      </td>
    </tr>
  </table>

  <p style="margin-top:30px;color:#555;">
    Regards,<br><strong>Strategic Investment Pro</strong>
  </p>
@endsection
