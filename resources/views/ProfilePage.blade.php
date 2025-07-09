@extends('Layout.master')

@section('title', 'Profile - TravelFree')

@push('styles')
    <link href="css/ProfilePage.css" rel="stylesheet">
@endpush

@section('content')
<div class="profile-container">
  <div class="avatar-section">
    <img id="avatar-preview" src="{{ asset('images/profile_pic.jpg') }}" alt="Avatar">
    <button class="create-btn" onclick="document.getElementById('avatar-upload').click()">Upload Avatar</button>
  </div>

  <div class="card">
    <h2>Account Security</h2>
    <div class="card-row">
      <div class="card-item">
        <span class="label">Link Email</span>
        <div class="value"></div>
        <button class="create-btn small">Update</button>
      </div>
      <div class="card-item">
        <span class="label">Add Phone Number</span>
        <button class="create-btn small">Add</button>
      </div>
    </div>
    <div class="card-row">
      <div class="card-item">
        <span class="label">Password</span>
        <div class="value">Set a password to protect your account</div>
        <button class="create-btn small">Set</button>
      </div>
    </div>
  </div>

  <div class="card">
    <h2>Personal Info</h2>
    <div class="card-row">
      <div class="input-group">
        <label>Gender</label>
        <input type="text" placeholder="Gender">
      </div>
      <div class="input-group">
        <label>Date of Birth</label>
        <input type="date">
      </div>
    </div>
    <div class="card-row">
      <div class="input-group">
        <label>First Name</label>
        <input type="text" placeholder="First Name">
      </div>
      <div class="input-group">
        <label>Last Name</label>
        <input type="text" placeholder="Last Name">
      </div>
    </div>
    <button class="create-btn">Save Changes</button>
  </div>

  <button class="logout-btn">Log Out</button>
</div>
@endsection