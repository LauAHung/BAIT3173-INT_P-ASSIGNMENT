@extends('Layout.master')

@section('title', 'Reset Password')

@section('content')
<div class="profile-container" style="max-width:480px;margin:40px auto;">
  <div class="card">
    <h2>Reset Password</h2>
    <form method="POST" action="{{ route('password.update.otp') }}">
      @csrf
      <input type="hidden" name="email" value="{{ $email }}" />
      <div class="form-group">
        <label>New Password</label>
        <input type="password" name="password" required minlength="8" />
        @error('password')
          <span class="error-message">{{ $message }}</span>
        @enderror
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="password_confirmation" required minlength="8" />
      </div>
      <button type="submit" class="create-btn">Reset Password</button>
    </form>
  </div>
</div>
@endsection


