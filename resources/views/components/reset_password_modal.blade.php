<div id="resetPasswordModal" class="auth-modal" style="display: none;">
  <div class="auth-modal-content">
    <span class="auth-modal-close" onclick="hideResetPasswordModal()">&times;</span>
    <h2 class="text-xl font-semibold text-purple-600 mb-4" style="text-align:center;margin-bottom:6px;">Reset Password</h2>
    <img src="{{ asset('images/warning.gif') }}" alt="Reset GIF" style="display:block;margin:0 auto;width:200px;height:200px;">
    <p class="text-sm text-gray-700 mb-6" style="text-align:center;margin-bottom:8px;font-size:16px;line-height:1.4;">Set a new password for {{ session('reset_email') }}.</p>
    <form method="POST" action="{{ route('password.update.otp') }}" style="padding:0 8px;">
      @csrf
      <input type="hidden" name="email" value="{{ session('reset_email') }}" />
      <div class="form-group" style="margin-bottom:10px;">
        <label style="display:block;margin-bottom:6px;color:#000;">New Password</label>
        <input type="password" name="password" required minlength="8" style="width:100%;padding:10px;border:1px solid {{ $errors->has('password') ? '#ff6b6b' : '#ddd' }};border-radius:8px;background:#ffffff;color:#111;" />
        @error('password')
          <span class="error-message" style="color:#ff4757;font-size:12px;margin-top:6px;display:block;">{{ $message }}</span>
        @enderror
      </div>
      <div class="form-group" style="margin-bottom:10px;">
        <label style="display:block;margin-bottom:6px;color:#000;">Confirm Password</label>
        <input type="password" name="password_confirmation" required minlength="8" style="width:100%;padding:10px;border:1px solid {{ $errors->has('password_confirmation') ? '#ff6b6b' : '#ddd' }};border-radius:8px;background:#ffffff;color:#111;" />
        @error('password_confirmation')
          <span class="error-message" style="color:#ff4757;font-size:12px;margin-top:6px;display:block;">{{ $message }}</span>
        @enderror
      </div>
      <div style="display:flex;gap:10px;justify-content:center;margin-top:30px;">
        <button type="button" class="modal-btn login-btn" onclick="hideResetPasswordModal()" style="background:#f1f3f5;color:#111;border:1px solid #e9ecef;padding:10px 16px;border-radius:8px;font-weight:600;">Cancel</button>
        <button type="submit" class="modal-btn register-btn" style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;padding:10px 16px;border:none;border-radius:8px;font-weight:600;">Confirm Reset</button>
      </div>
    </form>
  </div>
</div>

<script>
function showResetPasswordModal(){
  const m = document.getElementById('resetPasswordModal');
  if(!m) return;
  const c = m.querySelector('.auth-modal-content');
  m.classList.remove('auth-modal-fade-in','auth-modal-fade-out');
  c.classList.remove('auth-modal-content-slide-in','auth-modal-content-slide-out');
  m.style.display = 'block';
  m.offsetHeight;
  m.classList.add('auth-modal-fade-in');
  c.classList.add('auth-modal-content-slide-in');
}
function hideResetPasswordModal(){
  const m = document.getElementById('resetPasswordModal');
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

