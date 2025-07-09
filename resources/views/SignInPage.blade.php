@extends('Layout.master')

@section('title', 'Sign In')

@push('styles')
    <link href="css/SignInPage.css" rel="stylesheet">
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
        <a href="#">Already have an account? Log in</a>

        <div class="form-group">
          <input type="text" placeholder="First name" >
          <input type="text" placeholder="Last name">
        </div>
        <div class="form-group">
          <input type="email" placeholder="Email">
        </div>
        <div class="form-group">
          <input type="password" placeholder="Enter your password">
        </div>
        <div class="checkbox">
          <input type="checkbox" checked>
          <label>I agree to the <a href="#">Terms & Conditions</a></label>
        </div>
        <button class="create-btn">Create account</button>
        <div class="or">Or register with</div>
        <div class="oauth-btns">
          <button><img src="{{ asset('images/google_logo.png') }}" alt="Google Logo" id="google_logo">Google</button>
          <button><img src="{{ asset('images/facebook_logo.png') }}" alt="Facebook Logo" id="facebook_logo">Facebook</button>
        </div>
      </div>
    </div>
  </div>
@endsection