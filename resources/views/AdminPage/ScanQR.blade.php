@extends('Layout.master_admin')

@section('title', 'Admin - Scan QR')
@section('page-title', 'Scan QR')

@push('styles')
    <link href="css/AdminPage/ScanQR.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
<div class="scan-container">
    <!-- Left Side - QR Scanner -->
    <div class="scanner-section">
        <div class="scanner-header">
            <h2><i class="fas fa-qrcode"></i> QR Code Scanner</h2>
            <p>Scan passenger QR code to view journey details</p>
        </div>
        
        <div class="scanner-area">
            <div class="scanner-frame">
                <div class="scanner-overlay">
                    <div class="corner top-left"></div>
                    <div class="corner top-right"></div>
                    <div class="corner bottom-left"></div>
                    <div class="corner bottom-right"></div>
                </div>
                <video id="scanner-video" autoplay></video>
                <div class="scanner-placeholder" id="scanner-placeholder">
                    <i class="fas fa-camera"></i>
                    <p>Camera will activate when you click "Start Scanner"</p>
                </div>
            </div>
            
            <div class="scanner-controls">
                <button id="start-scanner" class="btn btn-primary">
                    <i class="fas fa-play"></i> Start Scanner
                </button>
                <button id="stop-scanner" class="btn btn-secondary" disabled>
                    <i class="fas fa-stop"></i> Stop Scanner
                </button>
            </div>
        </div>
    </div>

    <!-- Right Side - Passenger Information -->
    <div class="info-section">
        <div class="info-header">
            <h2><i class="fas fa-user"></i> Passenger Information</h2>
            <div class="status-indicator" id="status-indicator">
                <span class="status-dot"></span>
                <span class="status-text">No QR Code Scanned</span>
            </div>
        </div>
        
        <div class="passenger-info" id="passenger-info">
            <div class="info-card">
                <div class="info-group">
                    <label>Passenger Name</label>
                    <p id="passenger-name">-</p>
                </div>
                <div class="info-row" style="display:flex; gap:12px;">
                    <div class="info-group" style="flex:1;">
                        <label>Train Service</label>
                        <p id="train-service">-</p>
                    </div>
                    <div class="info-group" style="flex:1;">
                        <label>Train No</label>
                        <p id="train-no">-</p>
                    </div>
                </div>
                
                <div class="info-group">
                    <label>Journey ID</label>
                    <p id="journey-id">-</p>
                </div>
                
                <div class="info-row" style="display:flex; gap:12px;">
                    <div class="info-group" style="flex:1;">
                        <label>From</label>
                        <p id="depart-location">-</p>
                    </div>
                    <div class="info-group" style="flex:1;">
                        <label>To</label>
                        <p id="to-location">-</p>
                    </div>
                </div>
                
                <div class="info-row" style="display:flex; gap:12px;">
                    <div class="info-group" style="flex:1;">
                        <label>Date</label>
                        <p id="journey-date">-</p>
                    </div>
                    <div class="info-group" style="flex:1;">
                        <label>Time</label>
                        <p id="journey-time">-</p>
                    </div>
                </div>
                
                <div class="info-group">
                    <label>Status</label>
                    <div class="status-badge" id="journey-status">
                        <span class="status-text">-</span>
                    </div>
                </div>
            </div>
            
            <div class="action-buttons">
                <button id="checkin-btn" class="btn btn-success" disabled>
                    <i class="fas fa-sign-in-alt"></i> Check In
                </button>
                <button id="checkout-btn" class="btn btn-warning" disabled>
                    <i class="fas fa-sign-out-alt"></i> Check Out
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script src="{{ asset('js/AdminPage/ScanQR.js') }}"></script>
@endsection