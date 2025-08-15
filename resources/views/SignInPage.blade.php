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

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- Include SweetAlert2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <!-- JavaScript to trigger SweetAlert2 -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                @if (session('info'))
                    Swal.fire({
                        icon: 'info',
                        title: 'Information',
                        text: "{{ session('info') }}",
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                @endif
            });
        </script>

        <form method="POST" action="{{ route('login.handle') }}">
            @csrf
            <div class="form-group">
                <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="checkbox">
                <input type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <label>Remember me</label>
            </div>
            <button type="submit" class="create-btn">Log In</button>
        </form>

        <div class="or">Or log in with</div>
        <div class="oauth-btns">
            <a href="{{ route('google.login') }}" class="oauth-btn">
                <img src="{{ asset('images/google_logo.png') }}" alt="Google Logo" id="google_logo">Google
            </a>
            <a href="{{ route('facebook.redirect') }}" class="oauth-btn">
                <img src="{{ asset('images/facebook_logo.png') }}" alt="Facebook Logo" id="facebook_logo">Facebook
            </a>
        </div>

        <div class="forgot-password">
            <a href="{{ route('password.request') }}">Forgot your password?</a>
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

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <x-error-modal />
  <x-success-modal />
@endsection
