@extends('Layout.master_admin')

@section('title', 'ADMIN - 2FA Challenge')
@section('page-title', 'Two-Factor Authentication')

@push('styles')
  <link href="{{ asset('css/AdminPage/TwoFactor.css') }}" rel="stylesheet">
@endpush

@section('content')
  <style>
    .twofa-wrap { min-height: 100vh; display: flex; align-items: center; justify-content: center; }
    .twofa-card { width: 100%; max-width: 420px; background: #ffffff; color: #111827; border-radius: 14px; box-shadow: 0 18px 40px rgba(0,0,0,0.18); overflow: hidden; border: none; }
    .twofa-head { padding: 16px 20px; border-bottom: 1px solid #eeeeee; display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 18px; color:#111827; }
    .twofa-body { padding: 22px; color:#111827; }
    .twofa-label { display:block; font-weight: 600; margin-bottom: 6px; }
    .twofa-input { width: 100%; padding: 12px 14px; border: 1px solid #e5e7eb; background:#ffffff; color:#111827; border-radius: 10px; font-size: 16px; letter-spacing: .35em; text-align: center; box-sizing: border-box; font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; font-variant-numeric: tabular-nums; }
    .twofa-input::placeholder { color:#9aa4b2; }
    .twofa-input:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102,126,234,0.20); }
    .twofa-btn { width: 100%; margin-top: 14px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: 0; border-radius: 10px; padding: 12px 16px; font-weight: 800; cursor: pointer; transition: transform .12s ease, box-shadow .12s ease; box-shadow: 0 10px 20px rgba(118,75,162,.25); }
    .twofa-btn:disabled { opacity: .6; cursor: not-allowed; }
    .twofa-btn:hover { transform: translateY(-1px); box-shadow: 0 10px 20px rgba(118,75,162,0.25); }
    .twofa-tip { color: #374151; font-size: 14px; margin-bottom: 14px; }
    .bubble { position: fixed; top: 20px; right: 20px; z-index: 1000; display:none; }
    .bubble-content { padding: 12px 16px; border-radius: 10px; color: #fff; box-shadow: 0 8px 24px rgba(0,0,0,0.2); animation: slideIn .24s ease; }
    .bubble-success { background: #06b6d4; }
    .bubble-error { background: linear-gradient(135deg, #ff4757 0%, #ff3742 100%); }
    @keyframes slideIn { from { transform: translateX(12px); opacity: .0;} to { transform: translateX(0); opacity: 1;} }
    .twofa-footnote { margin-top: 10px; font-size: 12px; color: #6b7280; text-align: center; }
  </style>

  <div class="admin-2fa-page">
  <div class="twofa-wrap">
    <div class="twofa-card">
      <div class="twofa-head">Two‑Factor Authentication</div>
      <div class="twofa-body">
        <div id="message-container" class="bubble"><div id="message-content" class="bubble-content"></div></div>

        <p class="twofa-tip">Please open your authenticator app and enter the current 6‑digit code. You must pass this challenge to access the admin panel.</p>
        <form method="POST" action="{{ route('admin.2fa.verify') }}" id="challenge-form">
          @csrf
          <label class="twofa-label">6‑digit code</label>
          <input class="twofa-input" id="twofa-code" type="text" name="code" pattern="\d{6}" maxlength="6" inputmode="numeric" autocomplete="one-time-code" placeholder="" required>
          @error('code')
            <div class="twofa-footnote">{{ $message }}</div>
            <script>(function(){ showBubble('Invalid code. Please try again.', 'error'); })();</script>
          @enderror
          <button class="twofa-btn" id="twofa-submit" type="submit" disabled>Verify</button>
        </form>
        <div class="twofa-footnote">Having trouble? Ensure your phone time is set to automatic.</div>
      </div>
    </div>
  </div>
  </div>

  <script>
    function showBubble(message, type) {
      var container = document.getElementById('message-container');
      var content = document.getElementById('message-content');
      content.textContent = message;
      content.className = 'bubble-content ' + (type === 'success' ? 'bubble-success' : 'bubble-error');
      container.style.display = 'block';
      setTimeout(function(){ container.style.display = 'none'; }, 4000);
    }

    // Hide master admin top navigation/title for 2FA standalone pages
    (function(){
      try {
        var navCandidates = document.querySelectorAll('body > ul, body > ol');
        navCandidates.forEach(function(el){ el.style.display = 'none'; });
        var titleNodes = Array.prototype.slice.call(document.body.childNodes || []);
        titleNodes.forEach(function(n){ if (n.nodeType === 3 && (n.nodeValue||'').trim() === 'ADMIN') { n.textContent=''; } });
      } catch(e) {}
    })();

    // Enable button only when exactly 6 digits are entered
    (function(){
      var input = document.getElementById('twofa-code');
      var btn = document.getElementById('twofa-submit');
      function sync(){
        var v = (input.value || '').replace(/\D/g,'').slice(0,6);
        if (v !== input.value) input.value = v;
        btn.disabled = v.length !== 6;
      }
      input.addEventListener('input', sync);
      sync();
    })();

    @if(!empty($just_success))
      showBubble(@json($success_message ?? 'Success'), 'success');
      setTimeout(function(){ window.location.href = @json($redirect_to ?? url('/dashboard')); }, 3000);
    @endif
  </script>
@endsection


