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
         <div class="card-item">
           <span class="label">TravelFree Wallet</span>
           <div class="value wallet-value">RM{{ number_format($user->wallet_balance ?? 0, 2) }}</div>
           <button class="create-btn small" onclick="showTopupModal()">Top Up</button>
         </div>
       </div>
    @else
      <div class="card-row">
        <div class="card-item">
          <span class="label">Password</span>
          <div class="value">Set a password to protect your account</div>
          <button class="create-btn small" onclick="showChangePasswordForm()">Change</button>
        </div>
        <div class="card-item">
          <span class="label">TravelFree Wallet</span>
          <div class="value wallet-value">RM{{ number_format($user->wallet_balance ?? 0, 2) }}</div>
          <button class="create-btn small" onclick="showTopupModal()">Top Up</button>
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

  <!-- Topup Wallet Modal -->
  <div id="topupModal" style="display: none;" class="modal">
    <div class="modal-content">
      <span class="close" onclick="hideTopupModal()">&times;</span>
      <h2>Top Up TravelFree Wallet</h2>
      <div class="alert alert-info">
        <strong>Current Balance:</strong> RM{{ number_format($user->wallet_balance ?? 0, 2) }}
      </div>
      <form action="{{ route('wallet.topup') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Amount (RM)</label>
          <input type="number" name="amount" min="1" max="10000" step="0.01" placeholder="Enter amount" required>
          <small class="form-text">Minimum: RM1.00, Maximum: RM10,000.00</small>
        </div>
        <button type="submit" class="create-btn">Proceed to Payment</button>
      </form>
    </div>
  </div>

  <form action="{{ route('logout') }}" method="POST" style="display: inline;">
    @csrf
    <button type="submit" class="logout-btn">Log Out</button>
  </form>
</div>

<script>
// Simple modal functions - define them globally
function showChangePasswordForm() {
    console.log('showChangePasswordForm called');
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.style.display = 'block';
        console.log('Change password modal displayed');
        console.log('Modal element:', modal);
        console.log('Modal content:', modal.querySelector('.modal-content'));
    } else {
        console.log('Change password modal not found');
    }
}

function hideChangePasswordForm() {
    const modal = document.getElementById('changePasswordModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function showSetPasswordForm() {
    console.log('showSetPasswordForm called');
    const modal = document.getElementById('setPasswordModal');
    if (modal) {
        modal.style.display = 'block';
        console.log('Set password modal displayed');
    } else {
        console.log('Set password modal not found');
    }
}

function hideSetPasswordForm() {
    const modal = document.getElementById('setPasswordModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function showEmailUpdateForm() {
    console.log('showEmailUpdateForm called');
    const modal = document.getElementById('updateEmailModal');
    if (modal) {
        modal.style.display = 'block';
        console.log('Email update modal displayed');
        console.log('Modal element:', modal);
        console.log('Modal content:', modal.querySelector('.modal-content'));
    } else {
        console.log('Email update modal not found');
    }
}

function hideEmailUpdateForm() {
    const modal = document.getElementById('updateEmailModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function showTopupModal() {
    console.log('showTopupModal called');
    const modal = document.getElementById('topupModal');
    if (modal) {
        modal.style.display = 'block';
        console.log('Topup modal displayed');
        console.log('Modal element:', modal);
        console.log('Modal content:', modal.querySelector('.modal-content'));
    } else {
        console.log('Topup modal not found');
    }
}

function hideTopupModal() {
    const modal = document.getElementById('topupModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    var passwordModal = document.getElementById('changePasswordModal');
    var setPasswordModal = document.getElementById('setPasswordModal');
    var emailModal = document.getElementById('updateEmailModal');
    var topupModal = document.getElementById('topupModal');
    
    if (event.target == passwordModal) {
        passwordModal.style.display = 'none';
    }
    if (event.target == setPasswordModal) {
        setPasswordModal.style.display = 'none';
    }
    if (event.target == emailModal) {
        emailModal.style.display = 'none';
    }
    if (event.target == topupModal) {
        topupModal.style.display = 'none';
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
    z-index: 99999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(2px);
}

.modal-content {
    background-color: white;
    margin: 10% auto;
    padding: 30px;
    border-radius: 10px;
    width: 90%;
    max-width: 500px;
    position: relative;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    transform: translateY(-20px);
    animation: modalSlideIn 0.3s ease-out forwards;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
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

.form-text {
    color: #6c757d;
    font-size: 12px;
    margin-top: 5px;
    display: block;
}

/* Wallet specific styles - only for wallet balance */
.card-item .wallet-value {
    font-size: 18px;
    font-weight: bold;
    color: #28a745;
}
</style>
@endsection