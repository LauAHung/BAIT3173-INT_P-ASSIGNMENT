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
        <span class="label">Phone Number</span>
        <div class="value">{{ $user->phone ?? 'Not added' }}</div>
        <button class="create-btn small" onclick="showPhoneModal()">{{ $user->phone ? 'Update' : 'Add' }}</button>
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
            @error('date_of_birth')
                <span class="error-message">{{ $message }}</span>
            @enderror
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
    <div class="modal-content modern">
      <div class="modal-header">
        <h2><span class="header-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-6h-1V7a5 5 0 0 0-10 0v4H6a2 2 0 0 0-2 2v7h16v-7a2 2 0 0 0-2-2zm-3 0H9V7a3 3 0 0 1 6 0v4z"/></svg></span> Set Password</h2>
        <button type="button" class="modal-close" onclick="hideSetPasswordForm()">&times;</button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <strong>Note:</strong> You logged in with {{ ucfirst($user->social_provider) }}. Setting a password will allow you to log in with email and password in the future.
        </div>
        <form action="{{ route('profile.change-password.post') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>New Password</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-6h-1V7a5 5 0 0 0-10 0v4H6a2 2 0 0 0-2 2v7h16v-7a2 2 0 0 0-2-2zm-3 0H9V7a3 3 0 0 1 6 0v4z"/></svg>
            <input type="password" name="password" required minlength="8">
          </div>
          @error('password')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-6h-1V7a5 5 0 0 0-10 0v4H6a2 2 0 0 0-2 2v7h16v-7a2 2 0 0 0-2-2zm-3 0H9V7a3 3 0 0 1 6 0v4z"/></svg>
            <input type="password" name="password_confirmation" required minlength="8">
          </div>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="hideSetPasswordForm()">Cancel</button>
          <button type="submit" class="btn btn-primary">Set Password</button>
        </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Change Password Modal -->
  <div id="changePasswordModal" style="display: none;" class="modal">
    <div class="modal-content modern">
      <div class="modal-header">
        <h2><span class="header-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-6h-1V7a5 5 0 0 0-10 0v4H6a2 2 0 0 0-2 2v7h16v-7a2 2 0 0 0-2-2zm-3 0H9V7a3 3 0 0 1 6 0v4z"/></svg></span> Change Password</h2>
        <button type="button" class="modal-close" onclick="hideChangePasswordForm()">&times;</button>
      </div>
      <div class="modal-body">
      <form action="{{ route('profile.change-password.post') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Current Password</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-6h-1V7a5 5 0 0 0-10 0v4H6a2 2 0 0 0-2 2v7h16v-7a2 2 0 0 0-2-2zm-3 0H9V7a3 3 0 0 1 6 0v4z"/></svg>
            <input type="password" name="current_password" required>
          </div>
          @error('current_password')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label>New Password</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-6h-1V7a5 5 0 0 0-10 0v4H6a2 2 0 0 0-2 2v7h16v-7a2 2 0 0 0-2-2zm-3 0H9V7a3 3 0 0 1 6 0v4z"/></svg>
            <input type="password" name="password" required minlength="8">
          </div>
          @error('password')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        <div class="form-group">
          <label>Confirm New Password</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-6h-1V7a5 5 0 0 0-10 0v4H6a2 2 0 0 0-2 2v7h16v-7a2 2 0 0 0-2-2zm-3 0H9V7a3 3 0 0 1 6 0v4z"/></svg>
            <input type="password" name="password_confirmation" required minlength="8">
          </div>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="hideChangePasswordForm()">Cancel</button>
          <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
      </form>
      </div>
    </div>
  </div>

  <!-- Update Email Modal -->
  <div id="updateEmailModal" style="display: none;" class="modal">
    <div class="modal-content modern">
      <div class="modal-header">
        <h2><span class="header-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg></span> Update Email</h2>
        <button type="button" class="modal-close" onclick="hideEmailUpdateForm()">&times;</button>
      </div>
      <div class="modal-body">
      <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Current Email</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
            <input type="email" value="{{ $user->email }}" disabled>
          </div>
        </div>
        <div class="form-group">
          <label>New Email</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg>
            <input type="email" name="email" required>
          </div>
          @error('email')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        @if($user->hasPassword())
        <div class="form-group">
          <label>Confirm Password</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 17a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm6-6h-1V7a5 5 0 0 0-10 0v4H6a2 2 0 0 0-2 2v7h16v-7a2 2 0 0 0-2-2zm-3 0H9V7a3 3 0 0 1 6 0v4z"/></svg>
            <input type="password" name="confirm_password" required>
          </div>
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
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="hideEmailUpdateForm()">Cancel</button>
          <button type="submit" class="btn btn-primary">Update Email</button>
        </div>
      </form>
      </div>
    </div>
  </div>

  <!-- Add/Update Phone Modal -->
  <div id="phoneModal" style="display: none;" class="modal">
    <div class="modal-content modern">
      <div class="modal-header">
        <h2><span class="header-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.85 21 3 13.15 3 3a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.24 1.01l-2.2 2.2z"/></svg></span> {{ $user->phone ? 'Update Phone Number' : 'Add Phone Number' }}</h2>
        <button type="button" class="modal-close" onclick="hidePhoneModal()">&times;</button>
      </div>
      <div class="modal-body">
      <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Phone Number</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.85 21 3 13.15 3 3a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.24 1.01l-2.2 2.2z"/></svg>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="e.g. +60123456789">
          </div>
          @error('phone')
              <span class="error-message">{{ $message }}</span>
          @enderror
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="hidePhoneModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
      </form>
      </div>
    </div>
  </div>

  <!-- Topup Wallet Modal -->
  <div id="topupModal" style="display: none;" class="modal">
    <div class="modal-content modern">
      <div class="modal-header">
        <h2><span class="header-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 7h20v10H2V7zm2 2v2h16V9H4zm0 6h6v-2H4v2z"/></svg></span> Top Up TravelFree Wallet</h2>
        <button type="button" class="modal-close" onclick="hideTopupModal()">&times;</button>
      </div>
      <div class="modal-body">
      <div class="alert alert-info">
        <strong>Current Balance:</strong> RM{{ number_format($user->wallet_balance ?? 0, 2) }}
      </div>
      <form action="{{ route('wallet.topup') }}" method="POST">
        @csrf
        <div class="form-group">
          <label>Amount (RM)</label>
          <div class="input-with-icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 7h20v10H2V7zm2 2v2h16V9H4zm0 6h6v-2H4v2z"/></svg>
            <input type="number" name="amount" min="1" max="10000" step="0.01" placeholder="Enter amount" required>
          </div>
          <small class="form-text">Minimum: RM1.00, Maximum: RM10,000.00</small>
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="hideTopupModal()">Cancel</button>
          <button type="submit" class="btn btn-primary">Proceed to Payment</button>
        </div>
      </form>
      </div>
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

function showPhoneModal() {
    const modal = document.getElementById('phoneModal');
    if (modal) {
        modal.style.display = 'block';
    }
}

function hidePhoneModal() {
    const modal = document.getElementById('phoneModal');
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
    var phoneModal = document.getElementById('phoneModal');
    
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
    if (event.target == phoneModal) {
        phoneModal.style.display = 'none';
    }
}

// Function to update wallet balance display
function updateWalletBalance(newBalance) {
    const walletElements = document.querySelectorAll('.wallet-value');
    walletElements.forEach(element => {
        element.textContent = 'RM' + parseFloat(newBalance).toFixed(2);
    });
}


</script>

@if(session('success'))
<div id="flash-success" data-msg="{{ session('success') }}"></div>
<script>
    (function(){
        var el = document.getElementById('flash-success');
        if (el) {
            alert(el.dataset.msg);
            setTimeout(function() { window.location.reload(); }, 1000);
            el.remove();
        }
    })();
</script>
@endif

@if($errors->any())
<div id="flash-errors" data-errors='{{ json_encode($errors->all()) }}'></div>
<script>
    (function(){
        var el = document.getElementById('flash-errors');
        if (el) {
            try {
                var arr = JSON.parse(el.dataset.errors || "[]");
                arr.forEach(function(msg){ alert('Error: ' + msg); });
            } catch (e) {}
            el.remove();
        }
    })();
</script>
@endif

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

/* Ensure modal texts are black for proper readability */
.modal-content h2,
.modal-content label,
.modal-content .form-text,
.modal-content input,
.modal-content .alert-info,
.modal-content .alert-warning {
    color: #000;
}

.modal-content input::placeholder {
    color: #555;
}

/* Modern modal layout */
.modal-content.modern {
    padding: 0;
    overflow: hidden;
}
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-bottom: 1px solid #eee;
}
.modal-body {
    padding: 20px;
}
.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding: 12px 20px 20px 20px;
}
.modal-close {
    background: transparent;
    border: none;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
    color: #666;
}
.modal-close:hover { color: #000; }

/* Modern buttons */
.btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
.btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #ffffff; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3); }
.btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 16px rgba(102, 126, 234, 0.35); }
.btn-secondary { background: #f1f3f5; color: #111; border: 1px solid #e9ecef; }
.btn-secondary:hover { background: #e9ecef; }

/* Header icon and input icon styles */
.header-icon { display: inline-flex; vertical-align: middle; margin-right: 8px; }
.header-icon svg { width: 22px; height: 22px; fill: currentColor; }
.input-with-icon { position: relative; display: flex; align-items: center; }
.input-with-icon svg { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; fill: #666; pointer-events: none; }
.input-with-icon input { padding-left: 40px; }

/* Ensure icon padding wins over generic input padding */
.input-with-icon input { padding-left: 40px !important; }

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