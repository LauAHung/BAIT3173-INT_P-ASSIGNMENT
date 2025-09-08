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
                <input type="text" id="search-applicant" placeholder="Search by name or IC" class="filter-input">
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
<script>
let applications = [];
let filteredApplications = [];

document.addEventListener('DOMContentLoaded', function() {
    loadApplications();
});

async function loadApplications() {
    try {
        showLoading();
        const response = await fetch('/api/concession/applications');
        const data = await response.json();
        
        if (data.success) {
            applications = data.applications;
            filteredApplications = [...applications];
            renderApplications();
            updateStats();
        } else {
            showMessage('Failed to load applications', 'error');
        }
    } catch (error) {
        console.error('Error loading applications:', error);
        showMessage('Error loading applications', 'error');
    } finally {
        hideLoading();
    }
}

function renderApplications() {
    const tbody = document.getElementById('applications-tbody');
    const emptyState = document.getElementById('empty-state');
    
    if (filteredApplications.length === 0) {
        tbody.innerHTML = '';
        emptyState.style.display = 'block';
        return;
    }
    
    emptyState.style.display = 'none';
    
    tbody.innerHTML = filteredApplications.map(app => `
        <tr data-application-id="${app.id}">
            <td><span class="id-value">${app.id}</span></td>
            <td>${app.fullName}</td>
            <td>
                <span class="type-badge ${app.type}">${getTypeLabel(app.type)}</span>
            </td>
            <td>${app.ic}</td>
            <td>
                <span class="status-badge ${app.status}">${getStatusLabel(app.status)}</span>
            </td>
            <td><span class="time-value">${formatDate(app.applicationDate)}</span></td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-info btn-sm" onclick="viewApplication('${app.id}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${app.status === 'pending' ? `
                        <button class="btn btn-success btn-sm" onclick="approveApplication('${app.id}')">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" onclick="rejectApplication('${app.id}')">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : ''}
                </div>
            </td>
        </tr>
    `).join('');
}

function updateStats() {
    const stats = {
        total: applications.length,
        pending: applications.filter(app => app.status === 'pending').length,
        approved: applications.filter(app => app.status === 'approved').length,
        rejected: applications.filter(app => app.status === 'rejected').length
    };
    
    document.getElementById('total-applications').textContent = stats.total;
    document.getElementById('pending-applications').textContent = stats.pending;
    document.getElementById('approved-applications').textContent = stats.approved;
    document.getElementById('rejected-applications').textContent = stats.rejected;
}

function filterApplications() {
    const searchTerm = document.getElementById('search-applicant').value.toLowerCase();
    const typeFilter = document.getElementById('filter-type').value;
    const statusFilter = document.getElementById('filter-status').value;
    
    filteredApplications = applications.filter(app => {
        const matchesSearch = app.fullName.toLowerCase().includes(searchTerm) || 
                            app.ic.includes(searchTerm);
        const matchesType = !typeFilter || app.type === typeFilter;
        const matchesStatus = !statusFilter || app.status === statusFilter;
        
        return matchesSearch && matchesType && matchesStatus;
    });
    
    renderApplications();
}

function resetFilters() {
    document.getElementById('search-applicant').value = '';
    document.getElementById('filter-type').value = '';
    document.getElementById('filter-status').value = '';
    filteredApplications = [...applications];
    renderApplications();
}

async function viewApplication(applicationId) {
    try {
        const response = await fetch(`/api/concession/applications/${applicationId}`);
        const data = await response.json();
        
        if (data.success) {
            showApplicationModal(data.application);
        } else {
            showMessage('Failed to load application details', 'error');
        }
    } catch (error) {
        console.error('Error viewing application:', error);
        showMessage('Error loading application details', 'error');
    }
}

function showApplicationModal(application) {
    const modal = document.getElementById('application-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalBody = document.getElementById('modal-body');
    const modalFooter = document.getElementById('modal-footer');
    
    modalTitle.textContent = `Application ${application.id}`;
    
    modalBody.innerHTML = `
        <div class="application-details">
            <div class="detail-section">
                <h4>Basic Information</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Full Name:</label>
                        <span>${application.fullName}</span>
                    </div>
                    <div class="detail-item">
                        <label>IC Number:</label>
                        <span>${application.ic}</span>
                    </div>
                    <div class="detail-item">
                        <label>Application Type:</label>
                        <span class="type-badge ${application.type}">${getTypeLabel(application.type)}</span>
                    </div>
                    <div class="detail-item">
                        <label>Status:</label>
                        <span class="status-badge ${application.status}">${getStatusLabel(application.status)}</span>
                    </div>
                    <div class="detail-item">
                        <label>Applied Date:</label>
                        <span>${formatDate(application.applicationDate)}</span>
                    </div>
                </div>
            </div>
            
            ${getTypeSpecificDetails(application)}
        </div>
    `;
    
    modalFooter.innerHTML = application.status === 'pending' ? `
        <button class="btn btn-success" onclick="approveApplication('${application.id}')">
            <i class="fas fa-check"></i> Approve
        </button>
        <button class="btn btn-danger" onclick="rejectApplication('${application.id}')">
            <i class="fas fa-times"></i> Reject
        </button>
        <button class="btn btn-secondary" onclick="closeModal()">Close</button>
    ` : `
        <button class="btn btn-secondary" onclick="closeModal()">Close</button>
    `;
    
    modal.style.display = 'block';
}

async function approveApplication(applicationId) {
    if (!confirm('Are you sure you want to approve this application?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/concession/applications/${applicationId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                notes: prompt('Add approval notes (optional):') || ''
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage('Application approved successfully', 'success');
            loadApplications();
            closeModal();
        } else {
            showMessage(data.message || 'Failed to approve application', 'error');
        }
    } catch (error) {
        console.error('Error approving application:', error);
        showMessage('Error approving application', 'error');
    }
}

async function rejectApplication(applicationId) {
    if (!confirm('Are you sure you want to reject this application?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/concession/applications/${applicationId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                notes: prompt('Add rejection reason (optional):') || ''
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showMessage('Application rejected successfully', 'success');
            loadApplications();
            closeModal();
        } else {
            showMessage(data.message || 'Failed to reject application', 'error');
        }
    } catch (error) {
        console.error('Error rejecting application:', error);
        showMessage('Error rejecting application', 'error');
    }
}

function closeModal() {
    document.getElementById('application-modal').style.display = 'none';
}

function refreshApplications() {
    loadApplications();
}

function exportApplications() {
    // TODO: Implement export functionality
    showMessage('Export functionality coming soon', 'info');
}

// Helper functions
function getTypeLabel(type) {
    const labels = {
        'oku': 'OKU',
        'senior': 'Senior Citizen',
        'student': 'Student'
    };
    return labels[type] || type;
}

function getStatusLabel(status) {
    const labels = {
        'pending': 'Pending',
        'approved': 'Approved',
        'rejected': 'Rejected'
    };
    return labels[status] || status;
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('en-MY', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getTypeSpecificDetails(application) {
    let details = '';
    
    if (application.type === 'oku') {
        details = `
            <div class="detail-section">
                <h4>OKU Details</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>OKU Card Number:</label>
                        <span>${application.okuCardNumber || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <label>Disability Info:</label>
                        <span>${application.disability || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <label>Passport Number:</label>
                        <span>${application.passportNumber || 'N/A'}</span>
                    </div>
                </div>
            </div>
        `;
    } else if (application.type === 'senior') {
        details = `
            <div class="detail-section">
                <h4>Senior Citizen Details</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Age:</label>
                        <span>${application.age || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <label>Gender:</label>
                        <span>${application.gender || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <label>Citizenship:</label>
                        <span>${application.citizenship || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <label>Date of Birth:</label>
                        <span>${application.dateOfBirth || 'N/A'}</span>
                    </div>
                </div>
            </div>
        `;
    } else if (application.type === 'student') {
        details = `
            <div class="detail-section">
                <h4>Student Details</h4>
                <div class="detail-grid">
                    <div class="detail-item">
                        <label>Matrix Number:</label>
                        <span>${application.matrixNumber || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <label>School Name:</label>
                        <span>${application.schoolName || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <label>Education Level:</label>
                        <span>${application.educationLevel || 'N/A'}</span>
                    </div>
                    <div class="detail-item">
                        <label>Citizenship:</label>
                        <span>${application.studentCitizenship || 'N/A'}</span>
                    </div>
                    ${application.photoUrl ? `
                        <div class="detail-item">
                            <label>Student ID Photo:</label>
                            <div class="photo-container">
                                <img src="${application.photoUrl}" alt="Student ID Photo" class="student-photo" onclick="showPhotoModal('${application.photoUrl}')" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <span style="display:none;">Photo not available</span>
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    return details;
}

function showLoading() {
    document.getElementById('loading-state').style.display = 'block';
    document.getElementById('applications-table').style.display = 'none';
}

function hideLoading() {
    document.getElementById('loading-state').style.display = 'none';
    document.getElementById('applications-table').style.display = 'table';
}

function showMessage(message, type = 'info') {
    const container = document.getElementById('message-container');
    const content = document.getElementById('message-content');
    const text = document.getElementById('message-text');
    
    text.textContent = message;
    content.className = `message-content ${type}`;
    container.style.display = 'block';
    
    setTimeout(() => {
        container.style.display = 'none';
    }, 5000);
}

function closeMessage() {
    document.getElementById('message-container').style.display = 'none';
}

function showPhotoModal(photoUrl) {
    const modal = document.getElementById('photo-modal');
    const image = document.getElementById('photo-modal-image');
    image.src = photoUrl;
    modal.style.display = 'flex';
}

function closePhotoModal() {
    document.getElementById('photo-modal').style.display = 'none';
}

// Close photo modal when clicking outside the image
document.addEventListener('click', function(e) {
    const photoModal = document.getElementById('photo-modal');
    if (e.target === photoModal) {
        closePhotoModal();
    }
});
</script>
@endpush
