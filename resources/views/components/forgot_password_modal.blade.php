<!-- resources/views/components/forgot_password_modal.blade.php -->

<div id="forgotPasswordModal" class="auth-modal" style="display: none;">
    <div class="auth-modal-content">
        <span class="auth-modal-close" onclick="hideForgotPasswordModal()">&times;</span>
        <h2 class="text-xl font-semibold text-purple-600 mb-4" style="text-align:center;margin-bottom:6px;">Forgot Password</h2>

        <img src="{{ asset('images/warning.gif') }}" alt="Warning GIF" class="w-30 h-32 mx-auto mb-4" style="display:block;margin:0 auto;width:200px;height:200px;">

        <p class="text-sm text-gray-700 mb-6" style="text-align:center;margin-bottom:8px;font-size:16px;line-height:1.4;">
            If you wish to change it to a new password, you will need to verify your identity.
        </p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group" style="margin-bottom:10px;">
                <label style="display:block;margin-bottom:6px;">Email</label>
                <input type="email" name="email" required placeholder="Enter your email" value="{{ old('email') }}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;">
                @error('email') <span class="error-message" style="color:#ff4757;font-size:12px;margin-top:6px;display:block;">{{ $message }}</span> @enderror
            </div>
            <div class="modal-buttons" style="display:flex;gap:10px;justify-content:center;margin-top:30px;">
                <button type="button" onclick="hideForgotPasswordModal()" class="modal-btn login-btn" style="background:#f1f3f5;color:#111;border:1px solid #e9ecef;padding:10px 16px;border-radius:8px;font-weight:600;">Cancel</button>
                <button type="submit" class="modal-btn register-btn" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);color:#fff;padding:10px 16px;border:none;border-radius:8px;font-weight:600;">Verify Now</button>
            </div>
        </form>
    </div>
</div>

<script>
function showForgotPasswordModal() {
    const modal = document.getElementById('forgotPasswordModal');
    const modalContent = modal.querySelector('.auth-modal-content');
    modal.classList.remove('auth-modal-fade-in', 'auth-modal-fade-out');
    modalContent.classList.remove('auth-modal-content-slide-in', 'auth-modal-content-slide-out');
    modal.style.display = 'block';
    modal.offsetHeight; // reflow
    modal.classList.add('auth-modal-fade-in');
    modalContent.classList.add('auth-modal-content-slide-in');
}

function hideForgotPasswordModal() {
    const modal = document.getElementById('forgotPasswordModal');
    const modalContent = modal.querySelector('.auth-modal-content');
    modal.classList.add('auth-modal-fade-out');
    modalContent.classList.add('auth-modal-content-slide-out');
    setTimeout(() => {
        modal.style.display = 'none';
        modal.classList.remove('auth-modal-fade-in', 'auth-modal-fade-out');
        modalContent.classList.remove('auth-modal-content-slide-in', 'auth-modal-content-slide-out');
    }, 300);
}

window.addEventListener('click', function(event) {
    var modal = document.getElementById('forgotPasswordModal');
    if (event.target == modal) {
        hideForgotPasswordModal();
    }
});
</script>

<style>
 .auth-modal {
     display: none;
     position: fixed;
     z-index: 100000;
     left: 0;
     top: 0;
     width: 100%;
     height: 100%;
     background-color: rgba(0,0,0,0);
     transition: background-color 0.3s ease;
 }
 .auth-modal-content {
     background-color: white;
     margin: 3% auto;
     padding: 24px;
     border-radius: 14px;
     width: min(420px, 86%);
     max-width: 420px;
     aspect-ratio: 3 / 4;
     max-height: 80vh;
     overflow: auto;
     display: flex;
     flex-direction: column;
     justify-content: center;
     position: relative;
     text-align: center;
     box-shadow: 0 12px 28px rgba(0,0,0,0.25);
     transform: translateY(-100px);
     opacity: 0;
     transition: transform 0.35s ease, opacity 0.35s ease;
 }

 .auth-modal-fade-in {
     background-color: rgba(0,0,0,0.5) !important; 
    }

 .auth-modal-content-slide-in {
     transform: translateY(0) !important; opacity: 1 !important; 
    }
    
 .auth-modal-fade-out {
     background-color: rgba(0,0,0,0) !important; 
    }
 .auth-modal-content-slide-out {
     transform: translateY(-120px) !important; opacity: 0 !important; 
    }
 #forgotPasswordModal .auth-modal-content input[type="email"] {
     background-color:rgb(218, 218, 218) !important;
     color: #111;
 }
 .auth-modal-close {
     color:#aaa; 
     position:absolute; 
     right:12px; 
     top:8px; 
     font-size:28px; 
     cursor:pointer; 
    }
 .auth-modal-close:hover {
     color:#000; 
    }
</style>


