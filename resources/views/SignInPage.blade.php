@extends('Layout.master')

@section('title', 'Sign In - TravelFree')

@push('styles')
    <link href="css/SignInPage.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
@endpush

@section('content')
  <div class="container">
    <div class="left">
      <div class="form-wrapper">
        <h1>Sign in to your account</h1>
        <p>Don't have an account? <a href="{{ route('signup') }}">Sign up</a></p>

        {{-- Success messages are handled by the success modal component --}}

        <x-error_Modal />
        <!-- Include SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- JavaScript to trigger SweetAlert2 -->
        @if (session('info'))
        <div id="flash-info" data-message="{{ session('info') }}"></div>
        <script>
            (function(){
                var el = document.getElementById('flash-info');
                if (el && window.Swal) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Information',
                        text: el.dataset.message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                    el.remove();
                }
            })();
        </script>
        @endif

        <form method="POST" action="{{ route('login.handle') }}">
            @csrf
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="create-btn">Log In</button>
        </form>

        <div class="or">Or log in with</div>
        <div class="oauth-btns">
            <a href="{{ route('google.login') }}">
              <button>
                <img src="{{ asset('images/google_logo.png') }}" alt="Google Logo" id="google_logo">Google
              </button>
            </a>
            <a href="{{ route('facebook.redirect') }}">
              <button>
                <img src="{{ asset('images/facebook_logo.png') }}" alt="Facebook Logo" id="facebook_logo">Facebook
              </button>
            </a>
        </div>

        <div class="forgot-password">
            <a href="#" id="forgot-link">Forgot your password?</a>
        </div>
      </div>
    </div>

    <div class="right">
      <div class="left-inner">
        <a href="{{ route('HomePage') }}" class="back-btn">Back to website</a>
        <p>Capturing Moments,<br />Creating Memories</p>
      </div>
    </div>
  </div>

  <x-success_Modal />
  
  <!-- Forgot Password Modal -->
  <div id="forgotModal" class="forgot-modal" style="display:none;">
    <div class="modal-content modern" role="dialog" aria-modal="true">
      <div class="modal-header">
        <h2 style="display:flex;align-items:center;gap:10px;">
          <img src="{{ asset('images/warning.gif') }}" alt="Warning" style="width:40px;height:40px;"/>
          Forgot Password
        </h2>
        <button type="button" class="modal-close" onclick="hideForgotModal()">&times;</button>
      </div>
      <div class="modal-body">
        <p style="margin-bottom:16px;color:#111;">If you wish to change it to a new password, you will need to verify your identity.</p>
        <form method="POST" action="{{ route('password.email') }}">
          @csrf
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter your email" value="{{ old('email') }}"/>
            @error('email') <span class="error-message">{{ $message }}</span> @enderror
          </div>
          <div class="modal-actions">
            <button type="button" class="btn btn-secondary" onclick="hideForgotModal()">Cancel</button>
            <button type="submit" class="btn btn-primary">Verify Now</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <style>
  /* Use unique class to avoid global modal CSS conflicts */
  #forgotModal.forgot-modal { position: fixed; left:0; top:0; right:0; bottom:0; background: rgba(0,0,0,0.5); z-index: 100000; display: none !important; align-items: center; justify-content: center; }
  #forgotModal.forgot-modal.open { display: flex !important; }
  #forgotModal .modal-content.modern {
    position: relative;
    margin: 0 auto;
    background:#fff;
    color:#111;
    width: 92%;
    max-width: 520px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,.25);
    overflow:hidden;
    z-index: 100001;
  }
  .modal-header { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #eee; }
  .modal-body { padding:20px; }
  .modal-actions { display:flex; justify-content:flex-end; gap:12px; padding-top:8px; }
  .modal-close { background:transparent; border:none; font-size:28px; line-height:1; cursor:pointer; color:#666; }
  .modal-close:hover { color:#000; }
  .btn { display:inline-flex; align-items:center; gap:8px; padding:10px 16px; border:none; border-radius:8px; font-weight:600; cursor:pointer; }
  .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color:#fff; }
  .btn-secondary { background:#f1f3f5; color:#111; border:1px solid #e9ecef; }
  .error-message { color:#ff4757; font-size:12px; margin-top:6px; display:block; }
  </style>

  <script>
  function showForgotModal(){
    var m = document.getElementById('forgotModal');
    if(m){ m.classList.add('open'); }
  }
  function hideForgotModal(){
    var m = document.getElementById('forgotModal');
    if(m){ m.classList.remove('open'); }
  }
  window.addEventListener('click', function(e){
    var m = document.getElementById('forgotModal');
    if(e.target === m){ hideForgotModal(); }
  });
  // Bind click handler robustly
  document.addEventListener('DOMContentLoaded', function(){
    var link = document.getElementById('forgot-link');
    if(link){
      link.addEventListener('click', function(ev){ ev.preventDefault(); showForgotModal(); });
    }
  });
  </script>
@endsection
