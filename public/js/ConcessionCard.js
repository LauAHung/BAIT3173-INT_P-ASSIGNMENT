let applications = JSON.parse(localStorage.getItem('concessionApplications') || '[]');
let currentApplicationType = null;
let statusCurrentPage = 0;

class ApplicationHandler {
    constructor() {
        this.nextHandler = null;
    }
    setNext(handler) {
        this.nextHandler = handler;
        return handler;
    }
    handle(application) {
        if (this.canHandle(application)) {
            return this.processApplication(application);
        }
        return this.nextHandler ? this.nextHandler.handle(application) : false;
    }
    canHandle() { throw new Error('canHandle must be implemented'); }
    processApplication() { throw new Error('processApplication must be implemented'); }
}

class OKUApplicationHandler extends ApplicationHandler {
    canHandle(application) { return application.type === 'oku'; }
    processApplication(application) {
        if (!application.ic && !application.passportNumber) {
            return { valid: false, message: 'Either IC Number or Passport Number is required' };
        }
        if (application.ic && (application.ic.length !== 12 || !/^\d+$/.test(application.ic))) {
            return { valid: false, message: 'IC number must be exactly 12 digits and contain only numbers' };
        }
        if (!application.okuCardNumber || application.okuCardNumber.length < 8) {
            return { valid: false, message: 'OKU Card Number must be at least 8 characters' };
        }
        if (!application.disabilityType) {
            return { valid: false, message: 'Disability Type is required' };
        }
        if (application.disabilityType === 'other' && !application.otherDisability) {
            return { valid: false, message: 'Other Disability Information is required when selecting Other' };
        }
        return { valid: true };
    }
}

class SeniorCitizenApplicationHandler extends ApplicationHandler {
    canHandle(application) { return application.type === 'senior'; }
    processApplication(application) {
        if (!application.ic && !application.passportNumber) {
            return { valid: false, message: 'Either IC Number or Passport Number is required' };
        }
        if (application.ic && (application.ic.length !== 12 || !/^\d+$/.test(application.ic))) {
            return { valid: false, message: 'IC number must be exactly 12 digits and contain only numbers' };
        }
        if (!application.age || application.age < 60) {
            return { valid: false, message: 'Age must be 60 or above' };
        }
        if (!application.citizenship) {
            return { valid: false, message: 'Citizenship is required' };
        }
        return { valid: true };
    }
}

class StudentApplicationHandler extends ApplicationHandler {
    canHandle(application) { return application.type === 'student'; }
    processApplication(application) {
        if (!application.ic && !application.passportNumber) {
            return { valid: false, message: 'Either IC Number or Passport Number is required' };
        }
        if (application.ic && (application.ic.length !== 12 || !/^\d+$/.test(application.ic))) {
            return { valid: false, message: 'IC number must be exactly 12 digits and contain only numbers' };
        }
        if (!application.matrixNumber || application.matrixNumber.length < 4) {
            return { valid: false, message: 'Matrix number must be at least 4 characters' };
        }
        if (!application.schoolName) {
            return { valid: false, message: 'School name is required' };
        }
        return { valid: true };
    }
}

const okuHandler = new OKUApplicationHandler();
const seniorHandler = new SeniorCitizenApplicationHandler();
const studentHandler = new StudentApplicationHandler();
okuHandler.setNext(seniorHandler).setNext(studentHandler);

const screens = {
    main: document.getElementById('mainScreen'),
    form: document.getElementById('formScreen'),
    status: document.getElementById('statusScreen'),
    admin: document.getElementById('adminScreen')
};

function showScreen(screenName) {
    Object.values(screens).forEach(screen => screen.classList.remove('active'));
    screens[screenName].classList.add('active');
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.concession-card .btn-primary').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const card = button.closest('.concession-card');
            selectConcessionType(card.dataset.type);
        });
    });

    document.getElementById('adminBackBtn').addEventListener('click', () => showScreen('main'));
    document.getElementById('backBtn').addEventListener('click', () => showScreen('main'));
    document.getElementById('cancelBtn').addEventListener('click', () => showScreen('main'));
    document.getElementById('statusBackBtn').addEventListener('click', () => showScreen('main'));

    document.getElementById('applicationForm').addEventListener('submit', handleFormSubmission);

    const fileInput = document.getElementById('studentIdPhoto');
    const fileUpload = document.getElementById('fileUpload');
    if (fileInput && fileUpload) {
        fileInput.addEventListener('change', () => {
            fileUpload.querySelector('p').textContent = fileInput.files.length > 0
                ? `Uploaded: ${fileInput.files[0].name}`
                : 'Click to upload student ID photo';
        });
    }

    const disabilityTypeSelect = document.getElementById('disabilityType');
    const otherDisabilityContainer = document.getElementById('otherDisabilityContainer');
    if (disabilityTypeSelect && otherDisabilityContainer) {
        disabilityTypeSelect.addEventListener('change', (e) => {
            otherDisabilityContainer.style.display = e.target.value === 'other' ? 'flex' : 'none';
            if (e.target.value !== 'other') {
                document.getElementById('otherDisability').value = '';
            }
        });
    }

    const closeViewBtn = document.getElementById('closeView');
    if (closeViewBtn) {
        closeViewBtn.addEventListener('click', () => {
            const viewModal = document.getElementById('viewModal');
            if (viewModal) {
                viewModal.classList.remove('active');
            }
        });
    }

    const viewModal = document.getElementById('viewModal');
    if (viewModal) {
        viewModal.addEventListener('click', (e) => {
            if (e.target === viewModal) {
                viewModal.classList.remove('active');
            }
        });
    }

    updateAdminStats();
    loadApplicationsTable();
});

function selectConcessionType(type) {
    currentApplicationType = type;
    showScreen('form');
    document.querySelectorAll('.conditional-fields').forEach(field => field.classList.remove('active'));
    document.getElementById(`${type}Fields`).classList.add('active');
    document.getElementById('formTitle').textContent = `${type.toUpperCase()} Concession Application`;
    document.getElementById('applicationType').value = type;
    document.getElementById('applicationForm').reset();
    const otherDisabilityContainer = document.getElementById('otherDisabilityContainer');
    if (otherDisabilityContainer) {
        otherDisabilityContainer.style.display = 'none';
    }
}

function handleFormSubmission(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('_token', document.querySelector('input[name="_token"]').value);
    const application = {
        id: 'APP' + Date.now(),
        type: formData.get('type'),
        fullName: formData.get('fullName'),
        ic: formData.get('ic'),
        passportNumber: formData.get('passportNumber'),
        status: 'pending',
        applicationDate: new Date().toISOString()
    };

    if (application.type === 'oku') {
        application.okuCardNumber = formData.get('okuCardNumber');
        application.disabilityType = formData.get('disabilityType');
        if (application.disabilityType === 'other') {
            application.otherDisability = formData.get('otherDisability');
        }
    } else if (application.type === 'senior') {
        application.age = parseInt(formData.get('age')) || null;
        application.citizenship = formData.get('citizenship');
        application.gender = formData.get('gender');
        application.dateOfBirth = formData.get('dateOfBirth');
    } else if (application.type === 'student') {
        application.matrixNumber = formData.get('matrixNumber');
        application.schoolName = formData.get('schoolName');
        application.studentCitizenship = formData.get('studentCitizenship');
        application.educationLevel = formData.get('educationLevel');
        const photo = formData.get('studentIdPhoto');
        if (photo && photo.size > 0) application.photoName = photo.name;
    }

    const result = okuHandler.handle(application);
    if (!result.valid) {
        alert(result.message);
        return;
    }

    fetch('/concession/submit', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                applications.push(data.application);
                localStorage.setItem('concessionApplications', JSON.stringify(applications));
                showApplicationStatus(data.application);
            } else {
                let errorMsg = data.message || 'Submission failed';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('\n');
                }
                alert(errorMsg);
            }
        })
        .catch(error => {
            console.error('Error submitting application:', error);
            alert('Failed to submit application');
            applications.push(application);
            localStorage.setItem('concessionApplications', JSON.stringify(applications));
            showApplicationStatus(application);
        });
}

function showApplicationStatus(app) {
    showScreen('status');
    statusCurrentPage = 0;
    renderStatusTable();
}

function renderStatusTable() {
    const pageSize = 10;
    const sortedApplications = [...applications].sort((a, b) => new Date(b.applicationDate) - new Date(a.applicationDate));
    const total = sortedApplications.length;
    const maxPage = Math.ceil(total / pageSize) - 1;
    statusCurrentPage = Math.max(0, Math.min(statusCurrentPage, maxPage));

    const start = statusCurrentPage * pageSize;
    const end = start + pageSize;
    const paginatedApps = sortedApplications.slice(start, end);

    const statusContent = document.getElementById('statusContent');
    statusContent.innerHTML = `
        <h2>Your Applications (${total})</h2>
        <div class="applications-table">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Date & Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${paginatedApps.map(app => `
                        <tr>
                            <td>${app.fullName}</td>
                            <td><span class="status-badge ${app.type}">${app.type.toUpperCase()}</span></td>
                            <td><span class="status-badge ${app.status}">${app.status.toUpperCase()}</td>
                            <td>${new Date(app.applicationDate).toLocaleString()}</td>
                            <td>
                                <button class="action-btn view" onclick="viewApplication('${app.id}')">View</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        <div class="pagination">
            ${statusCurrentPage > 0 ? `<button class="btn" onclick="changeStatusPage(-1)">← Previous</button>` : ''}
            <span>Page ${statusCurrentPage + 1} of ${maxPage + 1}</span>
            ${statusCurrentPage < maxPage ? `<button class="btn" onclick="changeStatusPage(1)">Next →</button>` : ''}
        </div>
    `;
}

function changeStatusPage(delta) {
    statusCurrentPage += delta;
    renderStatusTable();
}

function updateAdminStats() {
    document.getElementById('totalApps').textContent = applications.length;
    document.getElementById('pendingApps').textContent = applications.filter(a => a.status === 'pending').length;
    document.getElementById('approvedApps').textContent = applications.filter(a => a.status === 'approved').length;
    document.getElementById('rejectedApps').textContent = applications.filter(a => a.status === 'rejected').length;
}

function loadApplicationsTable() {
    const pageSize = 10;
    const sortedApplications = [...applications].sort((a, b) => new Date(b.applicationDate) - new Date(a.applicationDate));
    const total = sortedApplications.length;
    const maxPage = Math.ceil(total / pageSize) - 1;
    let adminCurrentPage = Math.max(0, Math.min(statusCurrentPage, maxPage));

    const start = adminCurrentPage * pageSize;
    const end = start + pageSize;
    const paginatedApps = sortedApplications.slice(start, end);

    const tbody = document.querySelector('#applicationsTable tbody');
    tbody.innerHTML = paginatedApps.map(app => `
        <tr>
            <td>${app.fullName}</td>
            <td><span class="status-badge ${app.type}">${app.type.toUpperCase()}</span></td>
            <td><span class="status-badge ${app.status}">${app.status.toUpperCase()}</td>
            <td>${new Date(app.applicationDate).toLocaleString()}</td>
            <td class="action-buttons">
                <button class="action-btn view" onclick="viewApplication('${app.id}')">View</button>
                ${app.status === 'pending' ? `
                    <button class="action-btn approve" onclick="approveApplication('${app.id}')">Approve</button>
                    <button class="action-btn reject" onclick="rejectApplication('${app.id}')">Reject</button>
                    <button class="action-btn withdraw hidden" onclick="withdrawApplication('${app.id}')">Withdraw</button>
                ` : `
                    <button class="action-btn approve hidden" onclick="approveApplication('${app.id}')">Approve</button>
                    <button class="action-btn reject hidden" onclick="rejectApplication('${app.id}')">Reject</button>
                    <button class="action-btn withdraw" onclick="withdrawApplication('${app.id}')">Withdraw</button>
                `}
            </td>
        </tr>
    `).join('');

    const paginationContainer = document.querySelector('.applications-table .pagination');
    if (paginationContainer) {
        paginationContainer.innerHTML = `
            ${adminCurrentPage > 0 ? `<button class="btn" onclick="changeAdminPage(-1)">← Previous</button>` : ''}
            <span>Page ${adminCurrentPage + 1} of ${maxPage + 1}</span>
            ${adminCurrentPage < maxPage ? `<button class="btn" onclick="changeAdminPage(1)">Next →</button>` : ''}
        `;
    }
}

function changeAdminPage(delta) {
    statusCurrentPage += delta;
    loadApplicationsTable();
}

function viewApplication(id) {
    try {
        const app = applications.find(a => a.id === id);
        if (!app) {
            console.error('Application not found for ID:', id);
            alert('Application not found');
            return;
        }

        const modal = document.getElementById('viewModal');
        const modalContent = document.getElementById('applicationDetails');
        
        if (!modal || !modalContent) {
            console.error('Modal or modal content element not found');
            alert('Error: Modal not found');
            return;
        }

        let detailsTable = `
            <h3>Application Details</h3>
            <table class="details-table">
                <tr><th>Field</th><th>Value</th></tr>
                <tr><td>Name</td><td>${app.fullName || '-'}</td></tr>
                <tr><td>IC Number</td><td>${app.ic || '-'}</td></tr>
                <tr><td>Passport Number</td><td>${app.passportNumber || '-'}</td></tr>
                <tr><td>Concession Type</td><td>${app.type.toUpperCase()}</td></tr>
                <tr><td>Status</td><td>${app.status.toUpperCase()}</td></tr>
                <tr><td>Date & Time</td><td>${new Date(app.applicationDate).toLocaleString()}</td></tr>
        `;

        if (app.type === 'oku') {
            detailsTable += `
                <tr><td>OKU Card Number</td><td>${app.okuCardNumber || '-'}</td></tr>
                <tr><td>Disability Type</td><td>${app.disabilityType || '-'}</td></tr>
                ${app.disabilityType === 'other' ? `<tr><td>Other Disability</td><td>${app.otherDisability || '-'}</td></tr>` : ''}
            `;
        } else if (app.type === 'senior') {
            detailsTable += `
                <tr><td>Age</td><td>${app.age || '-'}</td></tr>
                <tr><td>Citizenship</td><td>${app.citizenship || '-'}</td></tr>
                <tr><td>Gender</td><td>${app.gender || '-'}</td></tr>
                <tr><td>Date of Birth</td><td>${app.dateOfBirth || '-'}</td></tr>
            `;
        } else if (app.type === 'student') {
            detailsTable += `
                <tr><td>Matrix Number</td><td>${app.matrixNumber || '-'}</td></tr>
                <tr><td>School Name</td><td>${app.schoolName || '-'}</td></tr>
                <tr><td>Citizenship</td><td>${app.studentCitizenship || '-'}</td></tr>
                <tr><td>Education Level</td><td>${app.educationLevel || '-'}</td></tr>
                <tr><td>Student ID Photo</td><td>${app.photoName || '-'}</td></tr>
            `;
        }

        detailsTable += '</table>';
        modalContent.innerHTML = detailsTable;
        modal.classList.add('active');
    } catch (error) {
        console.error('Error in viewApplication:', error);
        alert('Failed to display application details');
    }
}

function approveApplication(id) {
    const app = applications.find(a => a.id === id);
    if (app) {
        app.status = 'approved';
        localStorage.setItem('concessionApplications', JSON.stringify(applications));
        updateAdminStats();
        loadApplicationsTable();
    }
}

function rejectApplication(id) {
    const app = applications.find(a => a.id === id);
    if (app) {
        app.status = 'rejected';
        localStorage.setItem('concessionApplications', JSON.stringify(applications));
        updateAdminStats();
        loadApplicationsTable();
    }
}

function withdrawApplication(id) {
    const app = applications.find(a => a.id === id);
    if (app) {
        app.status = 'pending';
        localStorage.setItem('concessionApplications', JSON.stringify(applications));
        updateAdminStats();
        loadApplicationsTable();
    }
}