@extends('Layout.master')

@section('title', 'Profile - TravelFree')

@push('styles')
    <link href="css/ProfilePage.css" rel="stylesheet">
@endpush

@section('content')
<div class="profile-container">
  <div class="avatar-section">
    <img id="avatar-preview" src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : asset('images/profile_pic.jpg') }}" alt="Avatar">
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" id="avatar-upload" name="profile_picture" accept="image/*" onchange="this.form.submit()">
    </form>
    <button class="create-btn" onclick="document.getElementById('avatar-upload').click()">Upload Avatar</button>
  </div>

  <div class="card">
    <h2>Account Security</h2>
    <div class="card-row">
      <div class="card-item">
        <span class="label">Linked Email</span>
        <div class="value">{{ $user->email }}</div>
        <button class="create-btn small" onclick="showEmailUpdateForm()">Update</button>
      </div>
      <div class="card-item">
        <span class="label">Add Phone Number</span>
        <button class="create-btn small" onclick="alert('Phone number functionality coming soon!')">Add</button>
      </div>
    </div>
    @if($user->hasSocialLogin())
      <div class="card-row">
        <div class="card-item">
          <span class="label">Password</span>
          <div class="value">No password set ({{ ucfirst($user->social_provider) }} login)</div>
          <button class="create-btn small" onclick="showSetPasswordForm()">Set Password</button>
        </div>
      </div>
    @else
      <div class="card-row">
        <div class="card-item">
          <span class="label">Password</span>
          <div class="value">Set a password to protect your account</div>
          <button class="create-btn small" onclick="showChangePasswordForm()">Change</button>
        </div>
      </div>
    @endif
  </div>

  <div class="card">
    <h2>Personal Info</h2>
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="card-row">
          <div class="input-group">
            <label>Gender</label>
            <select name="gender" class="form-control">
                <option value="">Select Gender</option>
                <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Female</option>
            </select>
          </div>
          <div class="input-group">
            <label>Date of Birth</label>
            <input type="date" name="date_of_birth" value="{{ $user->date_of_birth ?? '' }}">
          </div>
        </div>
        <div class="card-row">
          <div class="input-group">
            <label>First Name</label>
            <input type="text" name="first_name" placeholder="First Name" value="{{ $user->first_name }}" required>
            @error('first_name')
                <span class="error-message">{{ $message }}</span>
            @enderror
          </div>
          <div class="input-group">
            <label>Last Name</label>
            <input type="text" name="last_name" placeholder="Last Name" value="{{ $user->last_name }}" required>
            @error('last_name')
                <span class="error-message">{{ $message }}</span>
            @enderror
          </div>
        </div>
        <button type="submit" class="create-btn">Save Changes</button>
    </form>
  </div>

  <!-- Set Password Modal (for social login users) -->
  <div id="setPasswordModal" style="display: none;" class="modal">
    <div class="modal-content">
      <span class="close" onclick="hideSetPasswordForm()">&times;</span>
      <h2>Set Password</h2>
      <div class="alert alert-info">
        <strong>Note:</strong> You logged in with {{ ucfirst($user->social_provider) }}. Setting a password will allow you to log in with email and password in the future.
      </div>
      <form action="{{ route('profile.change-password.post') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="password" required minlength="8">
          @error('password')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="password_confirmation" required minlength="8">
        </div>
        <button type="submit" class="create-btn">Set Password</button>
      </form>
    </div>
  </div>

  <!-- Change Password Modal -->
  <div id="changePasswordModal" style="display: none;" class="modal">
    <div class="modal-content">
      <span class="close" onclick="hideChangePasswordForm()">&times;</span>
      <h2>Change Password</h2>
      <form action="{{ route('profile.change-password.post') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Current Password</label>
          <input type="password" name="current_password" required>
          @error('current_password')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label>New Password</label>
          <input type="password" name="password" required minlength="8">
          @error('password')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <input type="password" name="password_confirmation" required minlength="8">
        </div>
        <button type="submit" class="create-btn">Change Password</button>
      </form>
    </div>
  </div>

  <!-- Update Email Modal -->
  <div id="updateEmailModal" style="display: none;" class="modal">
    <div class="modal-content">
      <span class="close" onclick="hideEmailUpdateForm()">&times;</span>
      <h2>Update Email</h2>
      <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Current Email</label>
          <input type="email" value="{{ $user->email }}" disabled>
        </div>
        <div class="form-group">
          <label>New Email</label>
          <input type="email" name="email" required>
          @error('email')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        @if($user->hasPassword())
        <div class="form-group">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" required>
          @error('confirm_password')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        @else
        <div class="form-group">
          <div class="alert alert-info">
            <strong>Note:</strong> Since you logged in with {{ ucfirst($user->social_provider) }}, no password confirmation is required.
          </div>
        </div>
        @endif
        <button type="submit" class="create-btn">Update Email</button>
      </form>
    </div>
  </div>

  <form action="{{ route('logout') }}" method="POST" style="display: inline;">
    @csrf
    <button type="submit" class="logout-btn">Log Out</button>
  </form>
</div>

<script>
function showChangePasswordForm() {
    document.getElementById('changePasswordModal').style.display = 'block';
}

function hideChangePasswordForm() {
    document.getElementById('changePasswordModal').style.display = 'none';
}

function showSetPasswordForm() {
    document.getElementById('setPasswordModal').style.display = 'block';
}

function hideSetPasswordForm() {
    document.getElementById('setPasswordModal').style.display = 'none';
}

function showEmailUpdateForm() {
    document.getElementById('updateEmailModal').style.display = 'block';
}

function hideEmailUpdateForm() {
    document.getElementById('updateEmailModal').style.display = 'none';
}

// Close modals when clicking outside
window.onclick = function(event) {
    var passwordModal = document.getElementById('changePasswordModal');
    var setPasswordModal = document.getElementById('setPasswordModal');
    var emailModal = document.getElementById('updateEmailModal');
    
    if (event.target == passwordModal) {
        passwordModal.style.display = 'none';
    }
    if (event.target == setPasswordModal) {
        setPasswordModal.style.display = 'none';
    }
    if (event.target == emailModal) {
        emailModal.style.display = 'none';
    }
}

// Show success messages if any
@if(session('success'))
    alert('{{ session('success') }}');
@endif

// Show error messages if any
@if($errors->any())
    @foreach($errors->all() as $error)
        alert('Error: {{ $error }}');
    @endforeach
@endif
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 500px;
    position: relative;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.error-message {
    color: red;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

.input-group {
    margin-bottom: 15px;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
}

.input-group input, .input-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 4px;
}

.alert-info {
    background-color: #d1ecf1;
    border: 1px solid #bee5eb;
    color: #0c5460;
}

.alert-warning {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
}
</style>
@endsection