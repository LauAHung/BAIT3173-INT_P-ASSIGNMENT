@extends('Layout.master')

@section('title', 'Verify OTP')

@section('content')
<div class="profile-container" style="max-width:480px;margin:40px auto;">
  <div class="card">
    <h2>Verify OTP</h2>
    <form method="POST" action="{{ route('password.verify-otp.post') }}">
      @csrf
      <input type="hidden" name="email" value="{{ $email }}" />
      <div class="form-group">
        <label>6-digit OTP</label>
        <input type="text" name="otp" maxlength="6" pattern="\d{6}" required placeholder="Enter 6-digit code" />
        @error('otp')
          <span class="error-message">{{ $message }}</span>
        @enderror
      </div>
      <button type="submit" class="create-btn">Verify</button>
    </form>
  </div>
</div>
@endsection


