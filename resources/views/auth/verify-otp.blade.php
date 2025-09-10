@extends('Layout.master')

@section('title', 'Verify OTP')

@section('content')
<div class="auth-modal" style="display:block;background:transparent;">
  <div class="auth-modal-content" style="margin:3% auto;width:min(420px,86%);max-width:420px;aspect-ratio:3/4;max-height:80vh;overflow:auto;">
    <span class="auth-modal-close" onclick="window.location='{{ route('signin') }}'" style="color:#aaa;position:absolute;right:12px;top:8px;font-size:28px;cursor:pointer;">&times;</span>
    <h2 class="text-xl font-semibold text-purple-600 mb-4" style="text-align:center;margin-bottom:6px;">Verify OTP</h2>
    <img src="{{ asset('images/warning.gif') }}" alt="Verify GIF" style="display:block;margin:0 auto;width:200px;height:200px;">
    <p class="text-sm text-gray-700 mb-6" style="text-align:center;margin-bottom:8px;font-size:16px;line-height:1.4;">We sent a 6-digit code to {{ $email }}. Please enter it below.</p>
    <form method="POST" action="{{ route('password.verify-otp.post') }}" style="padding:0 8px;">
      @csrf
      <input type="hidden" name="email" value="{{ $email }}" />
      <div class="form-group" style="margin-bottom:10px;">
        <label style="display:block;margin-bottom:6px;">6-digit OTP</label>
        <input type="text" name="otp" maxlength="6" pattern="\d{6}" required placeholder="Enter 6-digit code" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;background:#ffffff;color:#111;" />
        @error('otp')
          <span class="error-message" style="color:#ff4757;font-size:12px;margin-top:6px;display:block;">{{ $message }}</span>
        @enderror
      </div>
      <div style="display:flex;gap:10px;justify-content:center;margin-top:30px;">
        <a href="{{ route('password.request') }}" class="modal-btn login-btn" style="background:#f1f3f5;color:#111;border:1px solid #e9ecef;padding:10px 16px;border-radius:8px;font-weight:600;">Back</a>
        <button type="submit" class="modal-btn register-btn" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:10px 16px;border:none;border-radius:8px;font-weight:600;">Verify Now</button>
      </div>
    </form>
  </div>
</div>
@endsection


