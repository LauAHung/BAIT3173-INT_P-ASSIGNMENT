let applications = [];
let filteredApplications = [];

document.addEventListener('DOMContentLoaded', async function() {
	await loadApplications();
});

async function loadApplications() {
	try {
		showLoading();
		const response = await fetch('/api/concession/applications', {
			method: 'GET',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			},
			credentials: 'same-origin'
		});
		
		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		
		const data = await response.json();
		if (data.status === 'success') {
			applications = data.data || [];
			filteredApplications = [...applications];
			renderApplications();
			updateStats();
		} else {
			showMessage('Failed to load applications: ' + (data.message || 'Unknown error'), 'error');
		}
	} catch (error) {
		console.error('Error loading applications:', error);
		showMessage('Error loading applications: ' + error.message, 'error');
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
				<span class="type-badge ${app.type}" style="white-space: nowrap;">${getTypeLabel(app.type)}</span>
			</td>
			<td>${formatIdOrPassport(getIcNumber(app))}</td>
			<td>${formatIdOrPassport(app.passportNumber)}</td>
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
		const icNumber = getIcNumber(app);
		const matchesSearch = app.fullName.toLowerCase().includes(searchTerm) ||
			(icNumber && icNumber.includes(searchTerm)) ||
			(app.passportNumber && app.passportNumber.includes(searchTerm));
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
						<span>${formatIdOrPassport(getIcNumber(application))}</span>
					</div>
					<div class="detail-item">
						<label>Passport Number:</label>
						<span>${formatIdOrPassport(application.passportNumber)}</span>
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
	if (!confirm('Are you sure you want to approve this application?')) return;
	try {
		const response = await fetch(`/api/concession/applications/${applicationId}/approve`, {
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			},
			credentials: 'same-origin',
			body: JSON.stringify({ notes: prompt('Add approval notes (optional):') || '' })
		});
		
		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		
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
		showMessage('Error approving application: ' + error.message, 'error');
	}
}

async function rejectApplication(applicationId) {
	if (!confirm('Are you sure you want to reject this application?')) return;
	try {
		const response = await fetch(`/api/concession/applications/${applicationId}/reject`, {
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
			},
			credentials: 'same-origin',
			body: JSON.stringify({ notes: prompt('Add rejection reason (optional):') || '' })
		});
		
		if (!response.ok) {
			throw new Error(`HTTP error! status: ${response.status}`);
		}
		
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
		showMessage('Error rejecting application: ' + error.message, 'error');
	}
}

function closeModal() {
	document.getElementById('application-modal').style.display = 'none';
}

function refreshApplications() { loadApplications(); }
async function exportApplications() {
    try {
        showMessage('Preparing export...', 'info');
        // Step 1: issue token (requires admin.recent + throttle)
        const issueRes = await fetch('/admin/api/concession/export/token');
        const issueData = await issueRes.json();
        if (!issueData.success) {
            showMessage(issueData.message || 'Failed to issue export token', 'error');
            return;
        }
        const token = issueData.download_token;
        // Step 2: download using token
        const dlRes = await fetch(`/admin/api/concession/export/download?token=${encodeURIComponent(token)}`);
        const dlData = await dlRes.json();
        if (!dlData.success) {
            showMessage(dlData.message || 'Export failed', 'error');
            return;
        }
        // Create a download file in browser
        const filename = dlData.filename || 'concession_export.csv';
        if (dlData.format === 'json') {
            const blob = new Blob([JSON.stringify(dlData.content, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
        } else {
            const blob = new Blob([dlData.content], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url; a.download = filename; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
        }
        showMessage('Export completed', 'success');
    } catch (e) {
        console.error('Export error', e);
        showMessage('Export error', 'error');
    }
}

// Helper functions shared with blade template
function getTypeLabel(type) {
	const labels = { 'oku': 'OKU', 'senior': 'Senior', 'student': 'Student' };
	return labels[type] || type;
}
function getStatusLabel(status) {
	const labels = { 'pending': 'Pending', 'approved': 'Approved', 'rejected': 'Rejected' };
	return labels[status] || status;
}
function formatDate(dateString) {
	return new Date(dateString).toLocaleDateString('en-MY', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}
function formatIdOrPassport(value) {
	if (!value || value === 'null' || value === null) return '<span class="not-applicable">Not Applicable</span>';
	return value;
}
function getIcNumber(application) {
	if (application.type === 'student') return application.studentIc || application.ic;
	if (application.type === 'senior') return application.seniorIc || application.ic;
	return application.ic;
}
function getTypeSpecificDetails(application) {
	let details = '';
	if (application.type === 'oku') {
		details = `
			<div class="detail-section">
				<h4>OKU Details</h4>
				<div class="detail-grid">
					<div class="detail-item"><label>OKU Card Number:</label><span>${application.okuCardNumber || 'N/A'}</span></div>
					<div class="detail-item"><label>Disability Info:</label><span>${application.disability || 'N/A'}</span></div>
					${application.photoUrl ? `
					<div class="detail-item"><label>OKU Card Photo:</label><div class="photo-container"><img src="${application.photoUrl}" alt="OKU Card Photo" class="student-photo" onclick="showPhotoModal('${application.photoUrl}')" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"><span style="display:none;">Photo not available</span></div></div>` : ''}
				</div>
			</div>`;
	} else if (application.type === 'senior') {
		details = `
			<div class="detail-section"><h4>Senior Citizen Details</h4><div class="detail-grid">
				<div class="detail-item"><label>Age:</label><span>${application.age || 'N/A'}</span></div>
				<div class="detail-item"><label>Gender:</label><span>${application.gender || 'N/A'}</span></div>
				<div class="detail-item"><label>Citizenship:</label><span>${application.citizenship || 'N/A'}</span></div>
				<div class="detail-item"><label>Date of Birth:</label><span>${application.dateOfBirth || 'N/A'}</span></div>
				${application.photoUrl ? `<div class="detail-item"><label>IC Photo:</label><div class="photo-container"><img src="${application.photoUrl}" alt="IC Photo" class="student-photo" onclick="showPhotoModal('${application.photoUrl}')" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"><span style="display:none;">Photo not available</span></div></div>` : ''}
			</div></div>`;
	} else if (application.type === 'student') {
		details = `
			<div class="detail-section"><h4>Student Details</h4><div class="detail-grid">
				<div class="detail-item"><label>Matrix Number:</label><span>${application.matrixNumber || 'N/A'}</span></div>
				<div class="detail-item"><label>School Name:</label><span>${application.schoolName || 'N/A'}</span></div>
				<div class="detail-item"><label>Education Level:</label><span>${application.educationLevel || 'N/A'}</span></div>
				<div class="detail-item"><label>Citizenship:</label><span>${application.studentCitizenship || 'N/A'}</span></div>
				${application.photoUrl ? `<div class="detail-item"><label>Student ID Photo:</label><div class="photo-container"><img src="${application.photoUrl}" alt="Student ID Photo" class="student-photo" onclick="showPhotoModal('${application.photoUrl}')" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';"><span style="display:none;">Photo not available</span></div></div>` : ''}
			</div></div>`;
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
	setTimeout(() => { container.style.display = 'none'; }, 5000);
}
function closeMessage() { document.getElementById('message-container').style.display = 'none'; }
function showPhotoModal(photoUrl) { const modal = document.getElementById('photo-modal'); const image = document.getElementById('photo-modal-image'); image.src = photoUrl; modal.style.display = 'flex'; }
function closePhotoModal() { document.getElementById('photo-modal').style.display = 'none'; }
document.addEventListener('click', function(e) { const photoModal = document.getElementById('photo-modal'); if (e.target === photoModal) { closePhotoModal(); } });



