@extends('Layout.master')

@section('title', 'Forgot Password')

@section('content')
<div class="profile-container" style="max-width:480px;margin:40px auto;">
  <div class="card">
    <h2>Forgot Password</h2>
    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required placeholder="Enter your email" />
        @error('email')
          <span class="error-message">{{ $message }}</span>
        @enderror
      </div>
      <button type="submit" class="create-btn">Send OTP</button>
    </form>
  </div>
</div>
@endsection


