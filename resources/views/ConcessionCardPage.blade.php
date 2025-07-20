@extends('Layout.master')

@section('title', 'Home Page')

@push('styles')
    <link href="css/ConcessionCard.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
@endpush

@section('content')
    <main>
        <section class="first-section">
            <div class="preface">
                <h1>
                    Special Benefit
                </h1><br><br><br>
                <p>
                    At Travel Free, we believe that travel should be accessible, affordable, and inclusive for everyone. 
                    Our Concession Card system is designed to make train journeys more affordable for students, 
                    senior citizens, persons with disabilities, and other eligible groups across the nation.
                </p><br><br>
                <p>
                    This webpage is your gateway to apply for, renew, or manage your Travel Free Concession Card. 
                    Whether you're commuting daily for school, work, or leisure, our aim is to provide a seamless ticketing 
                    experience with special discounts and privileges for those who need it most.
                </p>
            </div>
            <div class="img_preface">
                <img src="../images/concession_card.png">
            </div>
        </section>

        <section class="second-section">
            <div class="container">
        <main class="main-content">
            <!-- Main Selection Screen -->
            <div id="mainScreen" class="screen active">
                <div class="hero-section">
                    <p class="hero-subtitle" style="margin-top:20px">Choose your concession type and enjoy 30% discount on your next purchase</p>
                </div>

                <div class="card-grid">
                    <div class="concession-card" data-type="oku">
                        <div class="card-icon oku">
                            <img src="../images/oku_icon.png">
                        </div>
                        <h3>OKU (Orang Kurang Upaya)</h3>
                        <p>For persons with disabilities holding valid OKU cards</p>
                        <ul class="requirements">
                            <li>Valid IC/Passport</li>
                            <li>Original JKM (OKU) Card</li>
                            <li>Disability Information</li>
                        </ul>
                        <button class="btn btn-primary">Apply Now</button>
                    </div>

                    <div class="concession-card" data-type="senior">
                        <div class="card-icon senior">
                            <img src="../images/senior_icon.png">
                        </div>
                        <h3>Senior Citizen</h3>
                        <p>For citizens aged 60 and above</p>
                        <ul class="requirements">
                            <li>Age 60+</li>
                            <li>Valid IC</li>
                            <li>Malaysian Citizenship</li>
                        </ul>
                        <button class="btn btn-primary">Apply Now</button>
                    </div>

                    <div class="concession-card" data-type="student">
                        <div class="card-icon student">
                            <img src="../images/student_icon.png">
                        </div>
                        <h3>Student</h3>
                        <p>For current students in educational institutions</p>
                        <ul class="requirements">
                            <li>Valid Student ID</li>
                            <li>Matrix Number</li>
                            <li>School Verification</li>
                        </ul>
                        <button class="btn btn-primary">Apply Now</button>
                    </div>
                </div>

                <div class="benefits-section">
                    <h3>Benefits of Concession Card</h3>
                    <div class="benefits-grid">
                        <div class="benefit">
                            <div class="benefit-number">30%</div>
                            <p>Discount on purchases</p>
                        </div>
                        <div class="benefit">
                            <div class="benefit-number">Fast</div>
                            <p>Quick approval process</p>
                        </div>
                        <div class="benefit">
                            <div class="benefit-number">Digital</div>
                            <p>No physical card needed</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Form Screen -->
            <div id="formScreen" class="screen">
                <div class="form-container">
                    <div class="form-header">
                        <button id="backBtn" class="btn btn-ghost">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 12H5m7 7-7-7 7-7"/>
                            </svg>
                            Back
                        </button>
                        <div class="form-title">
                            <div class="form-icon">
                                <img src="../images/application_icon.png">
                            </div>
                            <h2 id="formTitle">Application Form</h2>
                        </div>
                    </div>

                    <form id="applicationForm" enctype="multipart/form-data">
                        <input type="hidden" id="applicationType" name="type">
                        
                        <!-- Common Fields -->
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullName">Full Name *</label>
                                <input type="text" id="fullName" name="fullName" required>
                                <span class="error-message"></span>
                            </div>
                            <div class="form-group">
                                <label for="ic">IC Number *</label>
                                <input type="text" id="ic" name="ic" maxlength="12" required>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <!-- OKU Specific Fields -->
                        <div id="okuFields" class="conditional-fields">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="passportNumber">Passport Number (Optional)</label>
                                    <input type="text" id="passportNumber" name="passportNumber">
                                </div>
                                <div class="form-group">
                                    <label for="okuCardNumber">OKU Card Number *</label>
                                    <input type="text" id="okuCardNumber" name="okuCardNumber">
                                    <span class="error-message"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="disability">Disability Information *</label>
                                <textarea id="disability" name="disability" rows="3"></textarea>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <!-- Senior Citizen Specific Fields -->
                        <div id="seniorFields" class="conditional-fields">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="age">Age *</label>
                                    <input type="number" id="age" name="age" min="60">
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender *</label>
                                    <select id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="citizenship">Citizenship *</label>
                                    <input type="text" id="citizenship" name="citizenship">
                                    <span class="error-message"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dateOfBirth">Date of Birth</label>
                                <input type="date" id="dateOfBirth" name="dateOfBirth">
                            </div>
                        </div>

                        <!-- Student Specific Fields -->
                        <div id="studentFields" class="conditional-fields">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="matrixNumber">Matrix Number *</label>
                                    <input type="text" id="matrixNumber" name="matrixNumber">
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="studentCitizenship">Citizenship *</label>
                                    <input type="text" id="studentCitizenship" name="studentCitizenship">
                                    <span class="error-message"></span>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="educationLevel">Education Level *</label>
                                    <select id="educationLevel" name="educationLevel">
                                        <option value="">Select Education Level</option>
                                        <option value="primary">Primary School</option>
                                        <option value="secondary">Secondary School</option>
                                        <option value="college">College</option>
                                        <option value="university">University</option>
                                    </select>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="schoolName">School Name *</label>
                                    <input type="text" id="schoolName" name="schoolName">
                                    <span class="error-message"></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="studentIdPhoto">Student ID Photo *</label>
                                <div class="file-upload" id="fileUpload">
                                    <input type="file" id="studentIdPhoto" name="studentIdPhoto" accept="image/*">
                                    <div class="file-upload-content">
                                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <polyline points="7,10 12,15 17,10"/>
                                            <line x1="12" y1="15" x2="12" y2="3"/>
                                        </svg>
                                        <p>Click to upload student ID photo</p>
                                    </div>
                                </div>
                                <span class="error-message"></span>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" id="cancelBtn" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">Submit Application</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Status Screen -->
            <div id="statusScreen" class="screen">
                <div class="status-container">
                    <button id="statusBackBtn" class="btn btn-ghost">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 12H5m7 7-7-7 7-7"/>
                        </svg>
                        Back to Main Menu
                    </button>
                    
                    <div id="statusContent" class="status-content">
                        <!-- Status content will be populated by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Admin Screen -->
            <div id="adminScreen" class="screen">
                <div class="admin-container">
                    <div class="admin-header">
                        <div>
                            <h2>Admin Dashboard</h2>
                            <p>Review and manage concession card applications</p>
                        </div>
                        <button id="adminBackBtn" class="btn btn-secondary">Back to Main</button>
                    </div>

                    <div class="admin-stats">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14,2 14,8 20,8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10,9 9,9 8,9"/>
                                </svg>
                            </div>
                            <div>
                                <p>Total Applications</p>
                                <span id="totalApps">0</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon pending">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12,6 12,12 16,14"/>
                                </svg>
                            </div>
                            <div>
                                <p>Pending Review</p>
                                <span id="pendingApps">0</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon approved">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22,4 12,14.01 9,11.01"/>
                                </svg>
                            </div>
                            <div>
                                <p>Approved</p>
                                <span id="approvedApps">0</span>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon rejected">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" y1="9" x2="9" y2="15"/>
                                    <line x1="9" y1="9" x2="15" y2="15"/>
                                </svg>
                            </div>
                            <div>
                                <p>Rejected</p>
                                <span id="rejectedApps">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="applications-table">
                        <h3>Applications</h3>
                        <div class="table-container">
                            <table id="applicationsTable">
                                <thead>
                                    <tr>
                                        <th>Applicant</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Applications will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modals -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <h3>Reject Application</h3>
            <p>Please provide a reason for rejection:</p>
            <textarea id="rejectionReason" rows="4" placeholder="Enter rejection reason..."></textarea>
            <div class="modal-actions">
                <button id="cancelReject" class="btn btn-secondary">Cancel</button>
                <button id="confirmReject" class="btn btn-danger">Confirm Reject</button>
            </div>
        </div>
    </div>

    <div id="viewModal" class="modal">
        <div class="modal-content">
            <h3>Application Details</h3>
            <div id="applicationDetails"></div>
            <div class="modal-actions">
                <button id="closeView" class="btn btn-secondary">Close</button>
            </div>
        </div>
    </div>

    <script>
        let applications = JSON.parse(localStorage.getItem('concessionApplications') || '[]');
let currentApplicationType = null;

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
        if (!application.okuCardNumber || application.okuCardNumber.length < 8) {
            return { valid: false, message: 'OKU Card Number must be at least 8 characters' };
        }
        if (!application.disability) {
            return { valid: false, message: 'Disability information is required' };
        }
        if (!application.ic || application.ic.length !== 12) {
            return { valid: false, message: 'IC number must be 12 digits' };
        }
        return { valid: true };
    }
}

class SeniorCitizenApplicationHandler extends ApplicationHandler {
    canHandle(application) { return application.type === 'senior'; }
    processApplication(application) {
        if (!application.age || application.age < 60) {
            return { valid: false, message: 'Age must be 60 or above' };
        }
        if (!application.citizenship) {
            return { valid: false, message: 'Citizenship is required' };
        }
        if (!application.ic || application.ic.length !== 12) {
            return { valid: false, message: 'IC number must be 12 digits' };
        }
        return { valid: true };
    }
}

class StudentApplicationHandler extends ApplicationHandler {
    canHandle(application) { return application.type === 'student'; }
    processApplication(application) {
        if (!application.matrixNumber || application.matrixNumber.length < 4) {
            return { valid: false, message: 'Matrix number must be at least 4 characters' };
        }
        if (!application.schoolName) {
            return { valid: false, message: 'School name is required' };
        }
        if (!application.ic || application.ic.length !== 12) {
            return { valid: false, message: 'IC number must be 12 digits' };
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
    document.querySelectorAll('.concession-card').forEach(card => {
        card.addEventListener('click', () => selectConcessionType(card.dataset.type));
    });

    document.getElementById('adminBackBtn').addEventListener('click', () => showScreen('main'));
    document.getElementById('backBtn').addEventListener('click', () => showScreen('main'));
    document.getElementById('cancelBtn').addEventListener('click', () => showScreen('main'));
    document.getElementById('statusBackBtn').addEventListener('click', () => showScreen('main'));

    document.getElementById('applicationForm').addEventListener('submit', handleFormSubmission);

    const fileInput = document.getElementById('studentIdPhoto');
    const fileUpload = document.getElementById('fileUpload');
    fileInput.addEventListener('change', () => {
        fileUpload.querySelector('p').textContent = fileInput.files.length > 0
            ? `Uploaded: ${fileInput.files[0].name}`
            : 'Click to upload student ID photo';
    });

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
}

function handleFormSubmission(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const application = {
        id: 'APP' + Date.now(),
        type: formData.get('type'),
        fullName: formData.get('fullName'),
        ic: formData.get('ic'),
        status: 'pending',
        applicationDate: new Date().toISOString()
    };

    if (application.type === 'oku') {
        application.okuCardNumber = formData.get('okuCardNumber');
        application.disability = formData.get('disability');
    } else if (application.type === 'senior') {
        application.age = parseInt(formData.get('age'));
        application.citizenship = formData.get('citizenship');
    } else if (application.type === 'student') {
        application.matrixNumber = formData.get('matrixNumber');
        application.schoolName = formData.get('schoolName');
        const photo = formData.get('studentIdPhoto');
        if (photo && photo.size > 0) application.photoName = photo.name;
    }

    const result = okuHandler.handle(application);
    if (!result.valid) {
        alert(result.message);
        return;
    }

    applications.push(application);
    localStorage.setItem('concessionApplications', JSON.stringify(applications));
    showApplicationStatus(application);
}

function showApplicationStatus(app) {
    showScreen('status');
    const statusContent = document.getElementById('statusContent');
    statusContent.innerHTML = `
        <h2>${app.status.toUpperCase()}</h2>
        <p>Name: ${app.fullName}</p>
        <p>Type: ${app.type.toUpperCase()}</p>
        <p>Status: ${app.status.toUpperCase()}</p>
        <p>Date: ${new Date(app.applicationDate).toLocaleDateString()}</p>
    `;
}

function updateAdminStats() {
    document.getElementById('totalApps').textContent = applications.length;
    document.getElementById('pendingApps').textContent = applications.filter(a => a.status === 'pending').length;
    document.getElementById('approvedApps').textContent = applications.filter(a => a.status === 'approved').length;
    document.getElementById('rejectedApps').textContent = applications.filter(a => a.status === 'rejected').length;
}

function loadApplicationsTable() {
    const tbody = document.querySelector('#applicationsTable tbody');
    tbody.innerHTML = applications.map(app => `
        <tr>
            <td>${app.fullName}</td>
            <td><span class="status-badge ${app.type}">${app.type.toUpperCase()}</span></td>
            <td><span class="status-badge ${app.status}">${app.status.toUpperCase()}</span></td>
            <td>
                <button class="action-btn view" onclick="viewApplication('${app.id}')">View</button>
                ${app.status === 'pending' ? `
                    <button class="action-btn approve" onclick="approveApplication('${app.id}')">Approve</button>
                    <button class="action-btn reject" onclick="rejectApplication('${app.id}')">Reject</button>
                ` : ''}
            </td>
        </tr>
    `).join('');
}

function viewApplication(id) {
    const app = applications.find(a => a.id === id);
    if (app) alert(JSON.stringify(app, null, 2));
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
    </script>
        </section>
    </main>
@endsection