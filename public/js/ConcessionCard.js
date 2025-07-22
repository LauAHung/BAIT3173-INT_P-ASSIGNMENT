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