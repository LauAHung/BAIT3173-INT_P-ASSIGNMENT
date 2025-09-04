<div id="verifyOtpModal" class="auth-modal" style="display: none;">
  <div class="auth-modal-content">
    <span class="auth-modal-close" onclick="hideVerifyOtpModal()">&times;</span>
    <h2 class="text-xl font-semibold text-purple-600 mb-4" style="text-align:center;margin-bottom:6px;">Verify OTP</h2>
    <img src="{{ asset('images/warning.gif') }}" alt="Verify GIF" style="display:block;margin:0 auto;width:200px;height:200px;">
    <p class="text-sm text-gray-700 mb-6" style="text-align:center;margin-bottom:8px;font-size:16px;line-height:1.4;">We sent a 6-digit code to {{ session('otp_email') }}. Please enter it below.</p>
    <form method="POST" action="{{ route('password.verify-otp.post') }}" style="padding:0 8px;">
      @csrf
      <input type="hidden" name="email" value="{{ session('otp_email') }}" />
      <div class="form-group" style="margin-bottom:10px;">
        <label style="display:block;margin-bottom:6px;">6-digit OTP</label>
        <input type="text" name="otp" maxlength="6" pattern="\d{6}" required placeholder="Enter 6-digit code" style="width:100%;padding:10px;border:1px solid {{ $errors->has('otp') ? '#ff6b6b' : '#ddd' }};border-radius:8px;background:#ffffff;color:#111;" />
        @error('otp')
          <span class="error-message" style="color:#ff4757;font-size:12px;margin-top:6px;display:block;">Invalid OTP code</span>
        @enderror
      </div>
      <div style="display:flex;gap:10px;justify-content:center;margin-top:30px;">
        <button type="button" class="modal-btn login-btn" onclick="hideVerifyOtpModal()" style="background:#f1f3f5;color:#111;border:1px solid #e9ecef;padding:10px 16px;border-radius:8px;font-weight:600;">Cancel</button>
        <button type="submit" class="modal-btn register-btn" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:10px 16px;border:none;border-radius:8px;font-weight:600;">Verify Now</button>
      </div>
    </form>
  </div>
</div>

<script>
function showVerifyOtpModal(){
  const m = document.getElementById('verifyOtpModal');
  if(!m) return;
  const c = m.querySelector('.auth-modal-content');
  m.classList.remove('auth-modal-fade-in','auth-modal-fade-out');
  c.classList.remove('auth-modal-content-slide-in','auth-modal-content-slide-out');
  m.style.display = 'block';
  m.offsetHeight;
  m.classList.add('auth-modal-fade-in');
  c.classList.add('auth-modal-content-slide-in');
}
function hideVerifyOtpModal(){
  const m = document.getElementById('verifyOtpModal');
  if(!m) return;
  const c = m.querySelector('.auth-modal-content');
  m.classList.add('auth-modal-fade-out');
  c.classList.add('auth-modal-content-slide-out');
  setTimeout(()=>{
    m.style.display = 'none';
    m.classList.remove('auth-modal-fade-in','auth-modal-fade-out');
    c.classList.remove('auth-modal-content-slide-in','auth-modal-content-slide-out');
  },300);
}
</script>

