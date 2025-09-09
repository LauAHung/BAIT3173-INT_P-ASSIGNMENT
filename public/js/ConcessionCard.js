let applications = JSON.parse(localStorage.getItem('concessionApplications') || '[]');
let currentApplicationType = null;
let statusCurrentPage = 0;

// Autocomplete data
const nationalityData = [
    { code: 'MY', name: 'Malaysia', group: 'Asia' },
    { code: 'SG', name: 'Singapore', group: 'Asia' },
    { code: 'TH', name: 'Thailand', group: 'Asia' },
    { code: 'ID', name: 'Indonesia', group: 'Asia' },
    { code: 'PH', name: 'Philippines', group: 'Asia' },
    { code: 'VN', name: 'Vietnam', group: 'Asia' },
    { code: 'CN', name: 'China', group: 'Asia' },
    { code: 'IN', name: 'India', group: 'Asia' },
    { code: 'JP', name: 'Japan', group: 'Asia' },
    { code: 'KR', name: 'South Korea', group: 'Asia' },
    { code: 'AU', name: 'Australia', group: 'Oceania' },
    { code: 'NZ', name: 'New Zealand', group: 'Oceania' },
    { code: 'US', name: 'United States', group: 'North America' },
    { code: 'CA', name: 'Canada', group: 'North America' },
    { code: 'GB', name: 'United Kingdom', group: 'Europe' },
    { code: 'DE', name: 'Germany', group: 'Europe' },
    { code: 'FR', name: 'France', group: 'Europe' },
    { code: 'IT', name: 'Italy', group: 'Europe' },
    { code: 'ES', name: 'Spain', group: 'Europe' },
    { code: 'NL', name: 'Netherlands', group: 'Europe' },
    { code: 'OTHER', name: 'Other', group: 'Other' }
];

// School data (will be loaded from JSON)
let schoolData = {
    primary: [],
    secondary: [],
    college: [],
    university: []
};

// Initialize school data with university data
schoolData.university = [
    // Public Universities (IPTA)
    { code: 'UNISZA', name: 'Universiti Sultan Zainal Abidin', group: 'Public Universities (IPTA)' },
    { code: 'UIAM', name: 'Universiti Islam Antarabangsa Malaysia', group: 'Public Universities (IPTA)' },
    { code: 'UKM', name: 'Universiti Kebangsaan Malaysia', group: 'Public Universities (IPTA)' },
    { code: 'UM', name: 'Universiti Malaya', group: 'Public Universities (IPTA)' },
    { code: 'UMK', name: 'Universiti Malaysia Kelantan', group: 'Public Universities (IPTA)' },
    { code: 'UMP', name: 'Universiti Malaysia Pahang', group: 'Public Universities (IPTA)' },
    { code: 'UNIMAP', name: 'Universiti Malaysia Perlis', group: 'Public Universities (IPTA)' },
    { code: 'UMS', name: 'Universiti Malaysia Sabah', group: 'Public Universities (IPTA)' },
    { code: 'UNIMAS', name: 'Universiti Malaysia Sarawak', group: 'Public Universities (IPTA)' },
    { code: 'UMT', name: 'Universiti Malaysia Terengganu', group: 'Public Universities (IPTA)' },
    { code: 'UPSI', name: 'Universiti Pendidikan Sultan Idris', group: 'Public Universities (IPTA)' },
    { code: 'UPNM', name: 'Universiti Pertahanan Nasional Malaysia', group: 'Public Universities (IPTA)' },
    { code: 'UPM', name: 'Universiti Putra Malaysia', group: 'Public Universities (IPTA)' },
    { code: 'USIM', name: 'Universiti Sains Islam Malaysia', group: 'Public Universities (IPTA)' },
    { code: 'USM', name: 'Universiti Sains Malaysia', group: 'Public Universities (IPTA)' },
    { code: 'UTEM', name: 'Universiti Teknikal Malaysia Melaka', group: 'Public Universities (IPTA)' },
    { code: 'UITM', name: 'Universiti Teknologi MARA', group: 'Public Universities (IPTA)' },
    { code: 'UTM', name: 'Universiti Teknologi Malaysia', group: 'Public Universities (IPTA)' },
    { code: 'UTHM', name: 'Universiti Tun Hussein Onn Malaysia', group: 'Public Universities (IPTA)' },
    { code: 'UUM', name: 'Universiti Utara Malaysia', group: 'Public Universities (IPTA)' },
    
    // Private Universities (IPTS)
    { code: 'UNITEM', name: 'Universiti Terbuka Malaysia', group: 'Private Universities (IPTS)' },
    { code: 'MEDIU', name: 'Universiti Antarabangsa Al-Madinah', group: 'Private Universities (IPTS)' },
    { code: 'AIU', name: 'Universiti Antarabangsa Al-Bukhary', group: 'Private Universities (IPTS)' },
    { code: 'UNISEL', name: 'Universiti Selangor', group: 'Private Universities (IPTS)' },
    { code: 'UNIKL', name: 'Universiti Kuala Lumpur', group: 'Private Universities (IPTS)' },
    { code: 'IMU', name: 'Universiti Perubatan Antarabangsa', group: 'Private Universities (IPTS)' },
    { code: 'MUST', name: 'Universiti Sains & Teknologi Malaysia', group: 'Private Universities (IPTS)' },
    { code: 'LUCT', name: 'Universiti Teknologi Kreatif Limkokwing', group: 'Private Universities (IPTS)' },
    { code: 'UTP', name: 'Universiti Teknologi Petronas', group: 'Private Universities (IPTS)' },
    { code: 'UNITEN', name: 'Universiti Tenaga Nasional', group: 'Private Universities (IPTS)' },
    { code: 'MMU', name: 'Universiti Multimedia', group: 'Private Universities (IPTS)' },
    { code: 'WOU', name: 'Universiti Terbuka Wawasan', group: 'Private Universities (IPTS)' },
    { code: 'UNIRAZAK', name: 'Universiti Tun Abdul Razak', group: 'Private Universities (IPTS)' },
    { code: 'UTAR', name: 'Universiti Tunku Abdul Rahman', group: 'Private Universities (IPTS)' },
    { code: 'AIMST', name: 'Universiti Perubatan, Sains & Teknologi Asia', group: 'Private Universities (IPTS)' },
    { code: 'AeU', name: 'Asia e University', group: 'Private Universities (IPTS)' },
    { code: 'MSU', name: 'Universiti Sains & Pengurusan', group: 'Private Universities (IPTS)' },
    { code: 'INCEIF', name: 'International Centre for Education in Islamic Finance', group: 'Private Universities (IPTS)' },
    { code: 'UCSI', name: 'Universiti UCSI', group: 'Private Universities (IPTS)' },
    { code: 'QIUP', name: 'Quest International University Perak', group: 'Private Universities (IPTS)' },
    { code: 'IIU', name: 'INTI International University', group: 'Private Universities (IPTS)' },
    { code: 'TU', name: 'Taylor\'s University', group: 'Private Universities (IPTS)' },
    { code: 'MIU', name: 'Manipal International University', group: 'Private Universities (IPTS)' },
    { code: 'SU', name: 'Sunway University', group: 'Private Universities (IPTS)' },
    { code: 'PU', name: 'Perdana University', group: 'Private Universities (IPTS)' },
    { code: 'RUI', name: 'Raffles University Iskandar', group: 'Private Universities (IPTS)' },
    { code: 'UIM', name: 'Universiti Islam Malaysia', group: 'Private Universities (IPTS)' },
    { code: 'MAHSA', name: 'MAHSA University', group: 'Private Universities (IPTS)' },
    { code: 'UNITAR', name: 'UNITAR International University', group: 'Private Universities (IPTS)' },
    { code: 'APU', name: 'Asia Pacific University of Technology & Innovation', group: 'Private Universities (IPTS)' },
    { code: 'TAR UMT', name: 'Tunku Abdul Rahman University of Management and Technology', group: 'Private Universities (IPTS)' },
    { code: 'BUME', name: 'Binary University of Management and Entrepreneurship', group: 'Private Universities (IPTS)' },
    { code: 'HU', name: 'HELP University', group: 'Private Universities (IPTS)' },
    { code: 'IUKL', name: 'Universiti Infrastruktur Kuala Lumpur', group: 'Private Universities (IPTS)' },
    { code: 'SEGI', name: 'Universiti SEGI', group: 'Private Universities (IPTS)' },
    { code: 'CITY', name: 'City University', group: 'Private Universities (IPTS)' },
    
    // University Colleges
    { code: 'KLMUC', name: 'Kolej Universiti Kuala Lumpur Metropolitan', group: 'University Colleges' },
    { code: 'KUIN', name: 'Kolej Universiti Islam Insaniah', group: 'University Colleges' },
    { code: 'CUCMS', name: 'Kolej Universiti Sains Perubatan Cyberjaya', group: 'University Colleges' },
    { code: 'IUCTT', name: 'Kolej Universiti Teknologi Antarabangsa Twintech', group: 'University Colleges' },
    { code: 'TATI', name: 'Kolej Universiti TATI', group: 'University Colleges' },
    { code: 'KUIS', name: 'Kolej Universiti Islam Antarabangsa Selangor', group: 'University Colleges' },
    { code: 'NUC', name: 'Kolej Universiti Nilai', group: 'University Colleges' },
    { code: 'SYUC', name: 'Kolej Universiti Sunway', group: 'University Colleges' },
    { code: 'BUCH', name: 'Kolej Universiti Berjaya', group: 'University Colleges' },
    { code: 'MASTERSKILLS', name: 'Kolej Universiti Sains Kesihatan Masterskills', group: 'University Colleges' },
    { code: 'IUCN', name: 'International University College of Nursing', group: 'University Colleges' },
    { code: 'KUL', name: 'Kolej Universiti Linton', group: 'University Colleges' },
    { code: 'KDU', name: 'KDU University College', group: 'University Colleges' },
    { code: 'AUCMS', name: 'Allianze University College of Medical Sciences', group: 'University Colleges' },
    { code: 'KUS', name: 'Kolej Universiti Shahputra', group: 'University Colleges' },
    { code: 'LINCOLN', name: 'Kolej Universiti Lincoln', group: 'University Colleges' },
    { code: 'VUC', name: 'Veritas University College', group: 'University Colleges' },
    { code: 'UCTS', name: 'Kolej Universiti Teknologi Sarawak', group: 'University Colleges' },
    { code: 'KDU PENANG', name: 'KDU Penang University College', group: 'University Colleges' },
    
    // Foreign University Branches
    { code: 'MUSM', name: 'Monash University Malaysia', group: 'Foreign University Branches' },
    { code: 'SWINBURNE', name: 'Swinburne University of Technology Sarawak', group: 'Foreign University Branches' },
    { code: 'UNIM', name: 'Universiti Nottingham Malaysia', group: 'Foreign University Branches' },
    { code: 'CURTIN', name: 'Universiti Curtin Sarawak', group: 'Foreign University Branches' },
    { code: 'NUMED', name: 'Newcastle University Medicine Malaysia', group: 'Foreign University Branches' },
    { code: 'HWU', name: 'Heriot-Watt University Malaysia', group: 'Foreign University Branches' },
    
    { code: 'OTHER', name: 'Other (Please specify in comments)', group: 'Other' }
];

// College data (same as university for now)
schoolData.college = schoolData.university;

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
        if (!application.fullName) {
            return { valid: false, message: 'Full name is required' };
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
        if (!application.fullName) {
            return { valid: false, message: 'Full name is required' };
        }
        if (!application.age || application.age < 60) {
            return { valid: false, message: 'Age must be 60 or above' };
        }
        if (!application.citizenship) {
            return { valid: false, message: 'Citizenship is required' };
        }
        if (!application.gender) {
            return { valid: false, message: 'Gender is required' };
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
        if (!application.fullName) {
            return { valid: false, message: 'Full name is required' };
        }
        if (!application.matrixNumber || application.matrixNumber.length < 4) {
            return { valid: false, message: 'Matrix number must be at least 4 characters' };
        }
        if (!application.schoolName) {
            return { valid: false, message: 'School name is required' };
        }
        if (!application.studentCitizenship) {
            return { valid: false, message: 'Citizenship is required' };
        }
        if (!application.educationLevel) {
            return { valid: false, message: 'Education level is required' };
        }
        if (!application.ic || application.ic.length !== 12) {
            return { valid: false, message: 'IC number must be 12 digits' };
        }
        // Check if photo is uploaded
        const photoInput = document.getElementById('studentIdPhoto');
        if (!photoInput || !photoInput.files || photoInput.files.length === 0) {
            return { valid: false, message: 'Student ID photo is required' };
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
    console.log('DOM loaded, initializing...'); // Debug log
    
    // Attach event listener to "Apply Now" buttons (only for main screen cards)
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

    const form = document.getElementById('applicationForm');
    if (form) {
        console.log('Form found, adding submit listener'); // Debug log
        form.addEventListener('submit', handleFormSubmission);
    } else {
        console.error('Application form not found!'); // Debug log
    }

    const fileInput = document.getElementById('studentIdPhoto');
    const fileUpload = document.getElementById('fileUpload');
    if (fileInput && fileUpload) {
        fileInput.addEventListener('change', () => {
            fileUpload.querySelector('p').textContent = fileInput.files.length > 0
                ? `Uploaded: ${fileInput.files[0].name}`
                : 'Click to upload student ID photo';
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

    // Initialize autocomplete
    loadSchoolData();
    
    // Initialize nationality autocomplete
    new Autocomplete('citizenship', 'citizenshipDropdown', nationalityData);
    new Autocomplete('studentCitizenship', 'studentCitizenshipDropdown', nationalityData);
    
    // Initialize school autocomplete
    window.schoolAutocomplete = new Autocomplete('schoolName', 'schoolNameDropdown', schoolData.university);
    
    // Add event listener for education level change
    const educationLevelSelect = document.getElementById('educationLevel');
    if (educationLevelSelect) {
        educationLevelSelect.addEventListener('change', updateSchoolAutocomplete);
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
    
    // Set required attributes based on type
    setRequiredFields(type);
}

function setRequiredFields(type) {
    // Clear all required attributes first
    document.querySelectorAll('input[required], select[required]').forEach(field => {
        field.removeAttribute('required');
    });
    
    // Set required attributes based on type
    if (type === 'oku') {
        document.getElementById('okuCardNumber').setAttribute('required', 'required');
        document.getElementById('disability').setAttribute('required', 'required');
        document.getElementById('citizenship').setAttribute('required', 'required');
    } else if (type === 'senior') {
        document.getElementById('age').setAttribute('required', 'required');
        document.getElementById('citizenship').setAttribute('required', 'required');
        document.getElementById('gender').setAttribute('required', 'required');
    } else if (type === 'student') {
        document.getElementById('matrixNumber').setAttribute('required', 'required');
        document.getElementById('studentCitizenship').setAttribute('required', 'required');
        document.getElementById('educationLevel').setAttribute('required', 'required');
        document.getElementById('schoolName').setAttribute('required', 'required');
        document.getElementById('studentIdPhoto').setAttribute('required', 'required');
    }
    
    // Always required fields
    document.getElementById('fullName').setAttribute('required', 'required');
    document.getElementById('ic').setAttribute('required', 'required');
}

function handleFormSubmission(e) {
    e.preventDefault();
    console.log('Form submission started'); // Debug log
    
    // Manually validate autocomplete fields based on current type
    let validationFailed = false;
    const tempFormData = new FormData(e.target);
    const currentType = tempFormData.get('type');
    
    // Get required fields for current type
    let requiredFields = [];
    if (currentType === 'oku') {
        requiredFields = ['citizenship'];
    } else if (currentType === 'senior') {
        requiredFields = ['citizenship'];
    } else if (currentType === 'student') {
        requiredFields = ['studentCitizenship', 'schoolName'];
    }
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (field && field.hasAttribute('required') && field.value.trim() === '') {
            console.log('Autocomplete field validation failed:', fieldName); // Debug log
            field.setCustomValidity('This field is required');
            validationFailed = true;
        } else if (field) {
            field.setCustomValidity('');
        }
    });
    
    // Check if form is valid first
    if (!e.target.checkValidity() || validationFailed) {
        console.log('Form validation failed'); // Debug log
        e.target.reportValidity();
        return;
    }
    
    const formData = new FormData(e.target);
    formData.append('_token', document.querySelector('input[name="_token"]').value); // Add CSRF token for backend
    
    console.log('Form data collected:', {
        type: formData.get('type'),
        fullName: formData.get('fullName'),
        ic: formData.get('ic'),
        citizenship: formData.get('citizenship'),
        studentCitizenship: formData.get('studentCitizenship'),
        schoolName: formData.get('schoolName')
    }); // Debug log
    
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
    console.log('Validation result:', result); // Debug log
    
    if (!result.valid) {
        console.log('Validation failed:', result.message); // Debug log
        alert(result.message);
        return;
    }

    console.log('Validation passed, submitting to backend...'); // Debug log
    
    // Submit to backend via AJAX
    fetch('/concession/submit', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            console.log('Response received:', response.status); // Debug log
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data); // Debug log
            if (data.success) {
                console.log('Application submitted successfully'); // Debug log
                applications.push(data.application);
                localStorage.setItem('concessionApplications', JSON.stringify(applications));
                showApplicationStatus(data.application);
            } else {
                let errorMsg = data.message || 'Submission failed';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('\n');
                }
                console.log('Submission failed:', errorMsg); // Debug log
                alert(errorMsg);
            }
        })
        .catch(error => {
            console.error('Error submitting application:', error);
            alert('Failed to submit application: ' + error.message);
            // Fallback to localStorage if backend fails
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

// Autocomplete functionality
class Autocomplete {
    constructor(inputId, dropdownId, data) {
        this.input = document.getElementById(inputId);
        this.dropdown = document.getElementById(dropdownId);
        this.data = data;
        this.selectedIndex = -1;
        this.filteredData = [];
        
        this.init();
    }
    
    init() {
        this.input.addEventListener('input', (e) => this.handleInput(e));
        this.input.addEventListener('keydown', (e) => this.handleKeydown(e));
        
        // Use a small delay for blur to allow mousedown events to fire first
        this.input.addEventListener('blur', () => {
            setTimeout(() => this.hideDropdown(), 200);
        });
        
        this.input.addEventListener('focus', () => this.showDropdown());
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.hideDropdown();
            }
        });
    }
    
    handleInput(e) {
        const query = e.target.value.toLowerCase();
        this.filteredData = this.data.filter(item => 
            item.name.toLowerCase().includes(query) || 
            item.code.toLowerCase().includes(query)
        );
        
        this.selectedIndex = -1;
        this.renderDropdown();
        this.showDropdown();
    }
    
    handleKeydown(e) {
        if (!this.dropdown.style.display || this.dropdown.style.display === 'none') return;
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                this.selectedIndex = Math.min(this.selectedIndex + 1, this.filteredData.length - 1);
                this.updateHighlight();
                break;
            case 'ArrowUp':
                e.preventDefault();
                this.selectedIndex = Math.max(this.selectedIndex - 1, -1);
                this.updateHighlight();
                break;
            case 'Enter':
                e.preventDefault();
                if (this.selectedIndex >= 0) {
                    this.selectItem(this.filteredData[this.selectedIndex]);
                }
                break;
            case 'Escape':
                this.hideDropdown();
                break;
        }
    }
    
    renderDropdown() {
        if (this.filteredData.length === 0) {
            this.dropdown.innerHTML = '<div class="autocomplete-item">No results found</div>';
            return;
        }
        
        this.dropdown.innerHTML = this.filteredData.map((item, index) => `
            <div class="autocomplete-item ${index === this.selectedIndex ? 'highlighted' : ''}" 
                 data-index="${index}">
                <span class="item-code">${item.code}</span>
                <span class="item-name">${item.name}</span>
                <div class="item-group">${item.group}</div>
            </div>
        `).join('');
        
        // Add click listeners
        this.dropdown.querySelectorAll('.autocomplete-item').forEach((item, index) => {
            item.addEventListener('mousedown', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('Clicked item at index:', index, this.filteredData[index]); // Debug log
                this.selectItem(this.filteredData[index]);
            });
        });
    }
    
    updateHighlight() {
        this.dropdown.querySelectorAll('.autocomplete-item').forEach((item, index) => {
            item.classList.toggle('highlighted', index === this.selectedIndex);
        });
    }
    
    selectItem(item) {
        console.log('Selecting item:', item); // Debug log
        console.log('Input element before:', this.input.value); // Debug log
        
        // Set the value
        this.input.value = item.name;
        this.input.setAttribute('data-code', item.code);
        
        console.log('Input element after:', this.input.value); // Debug log
        
        // Hide dropdown first
        this.hideDropdown();
        
        // Trigger events after a short delay to ensure DOM is updated
        setTimeout(() => {
            // Clear any custom validity message
            this.input.setCustomValidity('');
            
            // Trigger input event to ensure form validation works
            this.input.dispatchEvent(new Event('input', { bubbles: true }));
            this.input.dispatchEvent(new Event('change', { bubbles: true }));
            
            // Also trigger focus and blur to ensure validation
            this.input.focus();
            this.input.blur();
            
            console.log('Events triggered for:', this.input.name, 'value:', this.input.value); // Debug log
        }, 50);
    }
    
    showDropdown() {
        if (this.filteredData.length > 0) {
            this.dropdown.style.display = 'block';
        } else if (this.input.value === '') {
            // Show all items when input is empty and focused
            this.filteredData = this.data;
            this.renderDropdown();
            this.dropdown.style.display = 'block';
        }
    }
    
    hideDropdown() {
        this.dropdown.style.display = 'none';
        this.selectedIndex = -1;
    }
}

// Function to get school data based on education level
function getSchoolDataByLevel(level) {
    switch(level) {
        case 'primary':
            return schoolData.primary;
        case 'secondary':
            return schoolData.secondary;
        case 'college':
            return schoolData.college;
        case 'university':
            return schoolData.university;
        default:
            return [];
    }
}

// Function to update school autocomplete data
function updateSchoolAutocomplete() {
    const educationLevel = document.getElementById('educationLevel').value;
    const schoolInput = document.getElementById('schoolName');
    const schoolDropdown = document.getElementById('schoolNameDropdown');
    
    // Clear current value
    schoolInput.value = '';
    
    // Update autocomplete data
    const newData = getSchoolDataByLevel(educationLevel);
    
    // Recreate autocomplete instance
    if (window.schoolAutocomplete) {
        window.schoolAutocomplete.data = newData;
        window.schoolAutocomplete.filteredData = [];
        window.schoolAutocomplete.hideDropdown();
    }
}

// Load school data from JSON file
async function loadSchoolData() {
    try {
        const response = await fetch('/schools_data.json');
        const data = await response.json();
        
        // Convert KPM school data to autocomplete format
        schoolData.primary = data.primary.map(school => ({
            code: school.name.substring(0, 10), // Use first 10 chars as code
            name: school.name,
            group: `${school.state} - ${school.district}`,
            state: school.state,
            district: school.district
        }));
        
        schoolData.secondary = data.secondary.map(school => ({
            code: school.name.substring(0, 10), // Use first 10 chars as code
            name: school.name,
            group: `${school.state} - ${school.district}`,
            state: school.state,
            district: school.district
        }));
        
        console.log('School data loaded:', schoolData);
    } catch (error) {
        console.error('Error loading school data:', error);
    }
}

// Autocomplete initialization moved to main DOMContentLoaded event

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