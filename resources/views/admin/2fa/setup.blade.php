@extends('Layout.master_admin')

@section('title', 'ADMIN - 2FA Setup')
@section('page-title', 'Two-Factor Authentication Setup')

@push('styles')
  <link href="{{ asset('css/AdminPage/TwoFactor.css') }}" rel="stylesheet">
@endpush

@section('content')
  <style>
    .set-wrap { min-height: 100vh; display:flex; align-items:center; justify-content:center; }
    .set-card { width:100%; max-width: 720px; background:#ffffff; color:#111827; border-radius:14px; box-shadow:0 18px 40px rgba(0,0,0,.18); overflow:hidden; border:none; }
    .set-head { padding:16px 20px; border-bottom:1px solid #eeeeee; font-weight:800; font-size:18px; display:flex; align-items:center; gap:10px; color:#111827; }
    .set-body { padding:22px; color:#111827; }
    .set-grid { display:flex; gap:24px; align-items:center; flex-wrap:wrap; }
    .set-qr { width:200px; height:200px; border-radius:12px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,.15); }
    .set-meta code { background:#f5f7fb; color:#111827; padding:4px 6px; border-radius:6px; }
    .set-tip { color:#4b5563; margin-bottom:10px; }
    .set-form { margin-top:18px; display:grid; gap:10px; max-width:360px; }
    .set-label { font-weight:700; }
    .set-input { padding:12px 14px; border:1px solid #e5e7eb; background:#ffffff; color:#111827; border-radius:10px; font-size:16px; letter-spacing:4px; text-align:center; }
    .set-input::placeholder { color:#9aa4b2; }
    .set-input:focus { outline:none; border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,.20); }
    .set-btn { background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); color:#fff; border:0; border-radius:10px; padding:12px 16px; font-weight:800; cursor:pointer; transition:transform .12s ease, box-shadow .12s ease; box-shadow:0 10px 20px rgba(118,75,162,.25); }
    .set-btn:hover { transform: translateY(-1px); box-shadow:0 10px 20px rgba(118,75,162,.25); }
    .set-footnote { margin-top:10px; font-size:12px; color:#6b7280; }
  </style>

  <div class="admin-2fa-page">
  <div class="set-wrap">
    <div class="set-card">
      <div class="set-head">Enable Two‑Factor (Google Authenticator)</div>
      <div class="set-body">
        <p class="set-tip">1) Install Google Authenticator or Authy on your phone.</p>
        <p class="set-tip">2) Scan the QR (or enter secret manually) and then enter the 6‑digit code below.</p>

        <div class="set-grid">
          <div class="set-qr">
            <img alt="QR" src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($otpauth) }}" width="200" height="200">
          </div>
          <div class="set-meta" style="flex:1; min-width:240px;">
            <div><strong>Secret:</strong> <code>{{ $secret }}</code></div>
            <div style="margin-top:6px;"><strong>OTPAuth:</strong> <code style="word-break:break-all;">{{ $otpauth }}</code></div>
            <form class="set-form" method="POST" action="{{ route('admin.2fa.enable') }}">
              @csrf
              <label class="set-label">6‑digit code</label>
              <input class="set-input" type="text" name="code" pattern="\d{6}" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="_ _ _ _ _ _" required>
              @error('code')<div class="set-footnote">{{ $message }}</div>@enderror
              <button class="set-btn" type="submit">Enable 2FA</button>
              <div class="set-footnote">Tip: If codes fail, ensure your phone time is set to automatic.</div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script>
    (function(){
      try {
        var navCandidates = document.querySelectorAll('body > ul, body > ol');
        navCandidates.forEach(function(el){ el.style.display = 'none'; });
        var titleNodes = Array.prototype.slice.call(document.body.childNodes || []);
        titleNodes.forEach(function(n){ if (n.nodeType === 3 && (n.nodeValue||'').trim() === 'ADMIN') { n.textContent=''; } });
      } catch(e) {}
    })();
  </script>
@endsection


