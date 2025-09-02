@extends('Layout.master')

@section('title', 'Sign Up - TravelFree')

@push('styles')
    <link href="css/SignUpPage.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Mona+Sans:ital,wght@0,200..900;1,200..900&display=swap" rel="stylesheet">
@endpush

@section('content')
  <div class="container">
    <div class="left">
      <div class="left-inner">
        <button>Back to website</button>
        <p>Capturing Moments,<br />Creating Memories</p>
      </div>
    </div>
    <div class="right">
      <div class="form-wrapper">
        <h1>Create an account</h1>
        <p>Already have an account? <a href="{{ route('signin') }}">Log in</a></p>

        <form action="{{ route('signup.handle') }}" method="POST">
          @csrf
          <div class="form-group">
            <input type="text" name="first_name" placeholder="First name" value="{{ old('first_name') }}" required>
            @error('first_name') <div class="error">{{ $message }}</div> @enderror
            <input type="text" name="last_name" placeholder="Last name" value="{{ old('last_name') }}" required>
            @error('last_name') <div class="error">{{ $message }}</div> @enderror
          </div>
          <div class="form-group">
            <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
            @error('email') <div class="error">{{ $message }}</div> @enderror
          </div>
          <div class="form-group">
            <input type="password" name="password" placeholder="Enter your password" required>
            @error('password') <div class="error">{{ $message }}</div> @enderror
          </div>
          <div class="checkbox">
            <input type="checkbox" name="terms" required>
            <label>I agree to the <a href="#">Terms & Conditions</a></label>
          </div>
          <button class="create-btn" type="submit">Create account</button>
        </form>

        <div class="or">Or register with</div>
        <div class="oauth-btns">
          <a href="{{ route('google.login') }}" id="google_login">
            <button>
              <img src="{{ asset('images/google_logo.png') }}" alt="Google Logo" id="google_logo">Google
            </button>
          </a>
          <a href="{{ route('facebook.redirect') }}" id="google_login">
            <button>
              <img src="{{ asset('images/facebook_logo.png') }}" alt="Facebook Logo" id="facebook_logo">Facebook
            </button>
          </a>
        </div>
      </div>
    </div>
  </div>

  <x-error-modal />

@endsection


