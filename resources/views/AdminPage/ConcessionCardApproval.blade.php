@extends('Layout.master_admin')

@section('title', 'ADMIN - Concession Card Approval')
@section('page-title', 'Concession Card Approval')

@push('styles')
    <link href="{{ asset('css/AdminPage/ConcessionCardApproval.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon primary">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar primary" style="width: 100%"></div>
                </div>
            </div>
            <div class="stat-value" id="total-applications">0</div>
            <div class="stat-label">Total Applications</div>
            <div class="stat-period">All Time</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar warning" style="width: 100%"></div>
                </div>
            </div>
            <div class="stat-value" id="pending-applications">0</div>
            <div class="stat-label">Pending Review</div>
            <div class="stat-period">Awaiting Approval</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar success" style="width: 100%"></div>
                </div>
            </div>
            <div class="stat-value" id="approved-applications">0</div>
            <div class="stat-label">Approved</div>
            <div class="stat-period">Successfully Processed</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-progress">
                    <div class="stat-progress-bar danger" style="width: 100%"></div>
                </div>
            </div>
            <div class="stat-value" id="rejected-applications">0</div>
            <div class="stat-label">Rejected</div>
            <div class="stat-period">Not Approved</div>
        </div>
    </div>

    <!-- Concession Card Management Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Concession Card Applications</h3>
            <div class="card-actions">
                <button class="btn btn-primary" onclick="exportApplications()">
                    <i class="fas fa-download"></i>
                    Export Applications
                </button>
                <button class="btn btn-secondary" onclick="refreshApplications()">
                    <i class="fas fa-sync"></i>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters-section">
            <div class="filter-group">
                <input type="text" id="search-applicant" placeholder="Search by name, IC, or passport" class="filter-input">
                <select id="filter-type" class="filter-select">
                    <option value="">All Types</option>
                    <option value="oku">OKU</option>
                    <option value="senior">Senior Citizen</option>
                    <option value="student">Student</option>
                </select>
                <select id="filter-status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
                <button class="btn btn-primary" onclick="filterApplications()">
                    <i class="fas fa-search"></i>
                    Filter
                </button>
                <button class="btn btn-secondary" onclick="resetFilters()">
                    <i class="fas fa-undo"></i>
                    Reset
                </button>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="table-container">
            <table class="table" id="applications-table">
                <thead>
                    <tr>
                        <th>Application ID</th>
                        <th>Applicant Name</th>
                        <th>Type</th>
                        <th>IC Number</th>
                        <th>Passport Number</th>
                        <th>Status</th>
                        <th>Applied Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="applications-tbody">
                    <!-- Applications will be loaded here via JavaScript -->
                </tbody>
            </table>
            
            <!-- Loading State -->
            <div id="loading-state" class="loading-state" style="display: none;">
                <div class="loading-spinner"></div>
                <p>Loading applications...</p>
            </div>
            
            <!-- Empty State -->
            <div id="empty-state" class="empty-state" style="display: none;">
                <i class="fas fa-file-alt"></i>
                <h3>No applications found</h3>
                <p>Try adjusting your search criteria or filters.</p>
            </div>
        </div>
    </div>

    <!-- Application Details Modal -->
    <div id="application-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title">Application Details</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Application details will be loaded here -->
            </div>
            <div class="modal-footer" id="modal-footer">
                <!-- Action buttons will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div id="photo-modal" class="photo-modal" style="display: none;">
        <button class="close-btn" onclick="closePhotoModal()">&times;</button>
        <img id="photo-modal-image" src="" alt="Student ID Photo">
    </div>

    <!-- Success/Error Messages -->
    <div id="message-container" style="display: none;" class="message-container">
        <div id="message-content" class="message-content">
            <span id="message-text"></span>
            <button onclick="closeMessage()" class="close-btn">&times;</button>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/AdminConcessionApproval.js') }}" defer></script>
@endpush
