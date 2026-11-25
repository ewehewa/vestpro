<x-admin>
  <style>
    .rs-wrap { max-width: 960px; margin: 30px auto; padding: 0 12px; }
    .rs-card { background: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 24px; margin-bottom: 20px; }
    .rs-card h5 { font-weight: 700; color: #111; margin-bottom: 4px; }
    .rs-card .rs-sub { color: #6b7280; font-size: 14px; margin-bottom: 18px; }
    .rs-stat { background: #f9fafb; border: 1px solid #eef0f3; border-radius: 10px; padding: 14px 16px; height: 100%; }
    .rs-stat .label { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }
    .rs-stat .value { font-size: 18px; font-weight: 700; color: #111; margin-top: 4px; }
    .rs-stat .hint { font-size: 12px; color: #6b7280; margin-top: 2px; }
    .rs-ok { color: #16a34a; }
    .rs-warn { color: #d97706; }
    .rs-off { color: #dc2626; }
    .form-label { font-weight: 600; color: #333; margin-bottom: 6px; }
    .form-control, .form-select { border-radius: 8px; border: 1px solid #ddd; padding: 10px 14px; font-size: 14px; }
    .btn-brand { background: #34E834; border: 1px solid #34E834; color: #000; font-weight: 600; border-radius: 8px; padding: 10px 20px; }
    .btn-brand:hover { background: #36F936; border-color: #36F936; color: #000; }
    .btn-outline-dark { border-radius: 8px; padding: 10px 20px; font-weight: 600; }
    .rs-code { background: #111827; color: #e5e7eb; border-radius: 8px; padding: 12px 14px; font-family: Consolas, monospace; font-size: 13px; overflow-x: auto; white-space: nowrap; }
    .form-switch .form-check-input { width: 2.6em; height: 1.4em; cursor: pointer; }
    .form-switch .form-check-input:checked { background-color: #34E834; border-color: #34E834; }
    .rs-alert { background: #fff7ed; border: 1px solid #fdba74; border-left: 5px solid #ea580c; border-radius: 10px; padding: 18px 20px; margin-bottom: 24px; color: #7c2d12; font-size: 14px; }
    .rs-alert-title { font-weight: 700; font-size: 15px; margin-bottom: 8px; color: #9a3412; }
    .rs-alert-steps { color: #7c2d12; }
    .rs-alert-foot { margin-top: 10px; font-size: 12px; color: #9a3412; opacity: .85; }
    .rs-confirm { display: none; margin-top: 12px; padding: 12px 14px; border: 1px solid #fde68a; background: #fffbeb; border-radius: 8px; font-size: 14px; }
  </style>

  <div class="rs-wrap">
    <h3 class="mb-1" style="font-weight:700;">Investment Returns</h3>
    <p class="text-muted mb-4">Choose when daily returns are credited to investors. Every investor receives one email per payout with the amounts credited and their available balance.</p>

    @if (session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    {{-- Only shown when the server's scheduled task isn't checking in --}}
    @unless ($cronRunning)
      <div class="rs-alert">
        <div class="rs-alert-title">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Automatic payouts aren't running: the server's scheduled task is {{ $schedulerLastSeen ? 'no longer running (last seen ' . $schedulerLastSeen->diffForHumans() . ')' : 'missing' }}.
        </div>
        <p class="mb-2">
          Daily returns won't be paid until this is fixed. Missed days are paid automatically once it's running again.
          Send this to your developer or host:
        </p>
        <p class="rs-alert-steps mb-2">
          In cPanel, open <strong>Cron Jobs</strong>, choose <strong>Once Per Minute (* * * * *)</strong>, and add this command:
        </p>
        <div class="d-flex gap-2 align-items-stretch flex-wrap">
          <div class="rs-code flex-grow-1" id="cronCommand">{{ $cronCommand }}</div>
          <button type="button" class="btn btn-outline-dark btn-sm" id="copyCronBtn"><i class="fas fa-copy me-1"></i>Copy</button>
        </div>
        <p class="rs-alert-foot mb-0">This message disappears within a minute of the task starting to run.</p>
      </div>
    @endunless

    {{-- Status --}}
    <div class="row g-3 mb-4">
      <div class="col-6 col-lg-3">
        <div class="rs-stat">
          <div class="label">Payouts</div>
          <div class="value {{ $enabled ? 'rs-ok' : 'rs-off' }}">{{ $enabled ? 'Active' : 'Paused' }}</div>
          <div class="hint">{{ $enabled ? 'Daily at ' . $time . ' (' . $timezone . ')' : 'No returns will be paid' }}</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="rs-stat">
          <div class="label">Next payout</div>
          <div class="value">{{ $nextRun ? $nextRun->format('M j, H:i') : 'None' }}</div>
          <div class="hint">{{ $nextRun ? $nextRun->diffForHumans() : 'Payouts are paused' }}</div>
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="rs-stat">
          <div class="label">Server cron</div>
          @if ($cronRunning)
            <div class="value rs-ok">Running</div>
            <div class="hint">Last check {{ $schedulerLastSeen->diffForHumans() }}</div>
          @elseif ($schedulerLastSeen)
            <div class="value rs-warn">Stopped</div>
            <div class="hint">Last seen {{ $schedulerLastSeen->diffForHumans() }}</div>
          @else
            <div class="value rs-off">Not detected</div>
            <div class="hint">See the warning above</div>
          @endif
        </div>
      </div>
      <div class="col-6 col-lg-3">
        <div class="rs-stat">
          <div class="label">Paid today</div>
          <div class="value">${{ number_format($paidTodayAmount, 2) }}</div>
          <div class="hint">{{ $paidTodayCount }} returns · {{ $activeCount }} active investments</div>
        </div>
      </div>
    </div>

    {{-- Schedule form --}}
    <div class="rs-card">
      <h5>Payout schedule</h5>
      <p class="rs-sub">Returns are credited once a day at this time. If the server misses a day, the missed returns are paid on the next run. A day is never paid twice.</p>

      <form method="POST" action="{{ route('admin.settings.returns.update') }}">
        @csrf
        <div class="form-check form-switch mb-4">
          <input type="hidden" name="enabled" value="0">
          <input class="form-check-input" type="checkbox" role="switch" id="enabled" name="enabled" value="1" @checked(old('enabled', $enabled))>
          <label class="form-check-label ms-2" for="enabled" style="font-weight:600;">Pay daily returns automatically</label>
        </div>

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label" for="time">Payout time (24-hour)</label>
            <input type="time" class="form-control" id="time" name="time" value="{{ old('time', $time) }}" required>
          </div>
          <div class="col-md-8">
            <label class="form-label" for="timezone">Timezone</label>
            <select class="form-select" id="timezone" name="timezone" required>
              @foreach ($timezones as $tz)
                <option value="{{ $tz }}" @selected(old('timezone', $timezone) === $tz)>{{ $tz }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mt-4">
          <button type="submit" class="btn btn-brand"><i class="fas fa-save me-2"></i>Save settings</button>
        </div>
      </form>
    </div>

    {{-- Manual run --}}
    <div class="rs-card">
      <h5>Run payouts now</h5>
      <p class="rs-sub">Pays every return that is due right now without waiting for the scheduled time. Returns that are not due yet are not paid early, and running it again won't pay anything twice.</p>

      <button type="button" class="btn btn-outline-dark" id="runNowBtn"><i class="fas fa-play me-2"></i>Run payouts now</button>

      <div class="rs-confirm" id="runNowConfirm">
        Credit all returns that are due now and email the investors?
        <form method="POST" action="{{ route('admin.settings.returns.run') }}" class="d-inline ms-2">
          @csrf
          <button type="submit" class="btn btn-brand btn-sm">Yes, run now</button>
        </form>
        <button type="button" class="btn btn-link btn-sm text-muted" id="runNowCancel">Cancel</button>
      </div>

      @if (!empty($lastRun))
        <hr>
        <div style="font-size:14px;color:#374151;">
          <strong>Last run:</strong>
          {{ \Illuminate\Support\Carbon::parse($lastRun['ran_at'])->setTimezone($timezone)->format('M j, Y H:i') }} ({{ $timezone }}), {{ $lastRun['trigger'] ?? '' }}
          <br>
          {{ $lastRun['payouts'] ?? 0 }} daily returns, {{ $lastRun['completed'] ?? 0 }} completed plans,
          ${{ number_format($lastRun['amount'] ?? 0, 2) }} paid to {{ $lastRun['users'] ?? 0 }} users.
          @if (($lastRun['errors'] ?? 0) > 0)
            <span class="rs-off">{{ $lastRun['errors'] }} failed; check storage/logs/laravel.log.</span>
          @endif
        </div>
      @endif
    </div>
  </div>

  <script>
    (function () {
      var btn = document.getElementById('runNowBtn');
      var box = document.getElementById('runNowConfirm');
      document.getElementById('runNowCancel').addEventListener('click', function () { box.style.display = 'none'; btn.style.display = ''; });
      btn.addEventListener('click', function () { box.style.display = 'block'; btn.style.display = 'none'; });

      var copyBtn = document.getElementById('copyCronBtn');
      if (copyBtn) {
        copyBtn.addEventListener('click', function () {
          var text = document.getElementById('cronCommand').innerText.trim();
          var done = function () { copyBtn.innerHTML = '<i class="fas fa-check me-1"></i>Copied'; };
          if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text).then(done);
          } else {
            // Fallback for plain http: select the text so the admin can press Ctrl+C
            var range = document.createRange();
            range.selectNodeContents(document.getElementById('cronCommand'));
            var sel = window.getSelection(); sel.removeAllRanges(); sel.addRange(range);
            try { document.execCommand('copy'); done(); } catch (e) {}
          }
        });
      }
    })();
  </script>
</x-admin>
