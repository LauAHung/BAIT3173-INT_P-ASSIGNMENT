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
            <a href="#" id="forgot-link" onclick="showForgotPasswordModal(); return false;">Forgot your password?</a>
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

  @if(!session('show_otp_modal') && !session('show_reset_modal'))
    <x-success_Modal />
  @endif
  
  @include('components.forgot_password_modal')
  @if(session('show_otp_modal'))
    @include('components.verify_otp_modal')
  @endif
  @if(session('show_reset_modal'))
    @include('components.reset_password_modal')
  @endif

  <style>
  /* Using shared component styles; no extra CSS needed here */
  </style>

  <script>
  // Auto-open OTP or Reset modals based on session flags
  document.addEventListener('DOMContentLoaded', function(){
    @if(session('show_otp_modal'))
      if (typeof showVerifyOtpModal === 'function') { showVerifyOtpModal(); }
    @endif
    @if(session('show_reset_modal'))
      if (typeof showResetPasswordModal === 'function') { showResetPasswordModal(); }
    @endif
  });
  </script>
@endsection
