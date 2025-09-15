let applications = JSON.parse(localStorage.getItem('concessionApplications') || '[]');
let currentApplicationType = null;
let statusCurrentPage = 0;

// Nationality data for autocomplete
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

// School data structure
let schoolData = {
    primary: [],
    secondary: [],
    college: [],
    university: [
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
        { code: 'TU', name: "Taylor's University", group: 'Private Universities (IPTS)' },
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
        { code: 'SEGI', name: 'Universiti SEGi', group: 'Private Universities (IPTS)' },
        { code: 'CITY', name: 'City University', group: 'Private Universities (IPTS)' },
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
        { code: 'MUSM', name: 'Monash University Malaysia', group: 'Foreign University Branches' },
        { code: 'SWINBURNE', name: 'Swinburne University of Technology Sarawak', group: 'Foreign University Branches' },
        { code: 'UNIM', name: 'Universiti Nottingham Malaysia', group: 'Foreign University Branches' },
        { code: 'CURTIN', name: 'Universiti Curtin Sarawak', group: 'Foreign University Branches' },
        { code: 'NUMED', name: 'Newcastle University Medicine Malaysia', group: 'Foreign University Branches' },
        { code: 'HWU', name: 'Heriot-Watt University Malaysia', group: 'Foreign University Branches' },
        { code: 'OTHER', name: 'Other (Please specify in comments)', group: 'Other' }
    ]
};

// Set college data same as university
schoolData.college = schoolData.university;

// Application Handler base class
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
        return this.nextHandler ? this.nextHandler.handle(application) : { valid: false, message: 'Invalid application type' };
    }
    canHandle() { throw new Error('canHandle must be implemented'); }
    processApplication() { throw new Error('processApplication must be implemented'); }
}

// OKU Application Handler
class OKUApplicationHandler extends ApplicationHandler {
    canHandle(application) {
        return application.type === 'oku';
    }
    processApplication(application) {
        console.log('Validating OKU application:', application);
        if (!application.fullName) return { valid: false, message: 'Full name is required' };
        if (!application.ic || !/^\d{12}$/.test(application.ic)) return { valid: false, message: 'IC number must be exactly 12 digits' };
        if (!application.okuCardNumber || application.okuCardNumber.length < 8) return { valid: false, message: 'OKU Card Number must be at least 8 characters' };
        if (!application.disabilityType) return { valid: false, message: 'Disability Type is required' };
        if (application.disabilityType === 'other' && !application.otherDisability) return { valid: false, message: 'Other Disability Information is required' };
        if (!application.photoName) return { valid: false, message: 'OKU Card Photo is required' };
        return { valid: true };
    }
}

// Senior Citizen Application Handler
class SeniorCitizenApplicationHandler extends ApplicationHandler {
    canHandle(application) {
        return application.type === 'senior';
    }
    processApplication(application) {
        console.log('Validating Senior application:', application);
        if (!application.fullName) return { valid: false, message: 'Full name is required' };
        if (!application.ic || !/^\d{12}$/.test(application.ic)) return { valid: false, message: 'IC number must be exactly 12 digits' };
        if (!application.age || application.age < 59) return { valid: false, message: 'Age must be 60 or above' };
        if (!application.gender) return { valid: false, message: 'Gender is required' };
        if (!application.photoName) return { valid: false, message: 'IC Photo is required' };
        return { valid: true };
    }
}

// Student Application Handler
class StudentApplicationHandler extends ApplicationHandler {
    canHandle(application) {
        return application.type === 'student';
    }
    processApplication(application) {
        console.log('Validating Student application:', application);
        if (!application.fullName) return { valid: false, message: 'Full name is required' };
        if (!application.studentCitizenship) return { valid: false, message: 'Citizenship is required' };
        if (!application.educationLevel) return { valid: false, message: 'Education level is required' };
        if (!application.schoolName) return { valid: false, message: 'School name is required' };
        if (!application.matrixNumber || application.matrixNumber.length < 4) return { valid: false, message: 'Matrix number must be at least 4 characters' };
        if (application.studentCitizenship.toLowerCase() === 'malaysia') {
            if (!application.ic || !/^\d{12}$/.test(application.ic)) return { valid: false, message: 'IC number must be exactly 12 digits for Malaysian citizens' };
        } else {
            if (!application.passportNumber || application.passportNumber.length < 6) return { valid: false, message: 'Passport number must be at least 6 characters for non-Malaysian citizens' };
        }
        if (!application.photoName) return { valid: false, message: 'Student ID photo is required' };
        return { valid: true };
    }
}

// Chain of responsibility setup
const okuHandler = new OKUApplicationHandler();
const seniorHandler = new SeniorCitizenApplicationHandler();
const studentHandler = new StudentApplicationHandler();
okuHandler.setNext(seniorHandler).setNext(studentHandler);

// Screen management
const screens = {
    main: document.getElementById('mainScreen'),
    form: document.getElementById('formScreen'),
    status: document.getElementById('statusScreen'),
    admin: document.getElementById('adminScreen')
};

// Function to show specific screen
function showScreen(screenName) {
    console.log('Showing screen:', screenName);
    if (!screens[screenName]) {
        console.error('Screen not found:', screenName);
        return;
    }
    Object.values(screens).forEach(screen => screen?.classList.remove('active'));
    screens[screenName].classList.add('active');
}

// DOM Content Loaded event handler
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, initializing application...');
    const isLoggedIn = document.querySelector('meta[name="user-authenticated"]')?.getAttribute('content') === 'true';

    // Initialize event listeners
    const initializeEventListeners = () => {
        console.log('Initializing event listeners...');
        
        // Apply buttons for concession cards
        document.querySelectorAll('.concession-card .btn-primary').forEach(button => {
            button.removeEventListener('click', handleApplyButtonClick);
            button.addEventListener('click', handleApplyButtonClick);
        });

        // Form submission
        const form = document.getElementById('applicationForm');
        if (form) {
            console.log('Application form found, attaching submit handler');
            form.removeEventListener('submit', handleFormSubmission);
            form.addEventListener('submit', handleFormSubmission);
        } else {
            console.error('Application form not found');
        }

        // Navigation buttons
        const adminBackBtn = document.getElementById('adminBackBtn');
        if (adminBackBtn) {
            adminBackBtn.removeEventListener('click', handleBackButton);
            adminBackBtn.addEventListener('click', handleBackButton);
        }

        const backBtn = document.getElementById('backBtn');
        if (backBtn) {
            backBtn.removeEventListener('click', handleBackButton);
            backBtn.addEventListener('click', handleBackButton);
        }

        const cancelBtn = document.getElementById('cancelBtn');
        if (cancelBtn) {
            cancelBtn.removeEventListener('click', handleBackButton);
            cancelBtn.addEventListener('click', handleBackButton);
        }

        const statusBackBtn = document.getElementById('statusBackBtn');
        if (statusBackBtn) {
            statusBackBtn.removeEventListener('click', handleBackButton);
            statusBackBtn.addEventListener('click', handleBackButton);
        }

        // Modal controls
        const closeViewBtn = document.getElementById('closeView');
        if (closeViewBtn) {
            closeViewBtn.removeEventListener('click', handleCloseView);
            closeViewBtn.addEventListener('click', handleCloseView);
        }

        const viewModal = document.getElementById('viewModal');
        if (viewModal) {
            viewModal.removeEventListener('click', handleModalClick);
            viewModal.addEventListener('click', handleModalClick);
        }
    };

    const handleApplyButtonClick = (e) => {
        e.preventDefault();
        console.log('Apply button clicked');
        if (!isLoggedIn) {
            console.log('User not logged in, showing login prompt');
            Swal.fire({
                title: 'Login Required',
                text: 'You need to be logged in to apply for a concession card.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Login',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Redirecting to login page');
                    window.location.href = '/signin';
                }
            });
            return;
        }
        const card = e.target.closest('.concession-card');
        if (card?.dataset.type) {
            console.log('Concession type selected:', card.dataset.type);
            selectConcessionType(card.dataset.type);
        }
    };

    const handleBackButton = () => {
        console.log('Back button clicked, returning to main screen');
        showScreen('main');
    };

    const handleCloseView = () => {
        console.log('Closing view modal');
        document.getElementById('viewModal')?.classList.remove('active');
    };

    const handleModalClick = (e) => {
        if (e.target === document.getElementById('viewModal')) {
            console.log('Clicked outside modal, closing');
            e.target.classList.remove('active');
        }
    };

    // Initialize file uploads
    initializeFileUploads();

    // Initialize autocomplete
    const studentCitizenshipInput = document.getElementById('studentCitizenship');
    const studentCitizenshipDropdown = document.getElementById('studentCitizenshipDropdown');
    if (studentCitizenshipInput && studentCitizenshipDropdown) {
        console.log('Initializing student citizenship autocomplete');
        window.studentCitizenshipAutocomplete = new Autocomplete('studentCitizenship', 'studentCitizenshipDropdown', nationalityData);
    } else {
        console.error('Student citizenship autocomplete elements not found');
    }

    const schoolNameInput = document.getElementById('schoolName');
    const schoolNameDropdown = document.getElementById('schoolNameDropdown');
    if (schoolNameInput && schoolNameDropdown) {
        console.log('Initializing school name autocomplete');
        window.schoolAutocomplete = new Autocomplete('schoolName', 'schoolNameDropdown', schoolData.university);
    } else {
        console.error('School name autocomplete elements not found');
    }

    // Education level change handler
    const educationLevelSelect = document.getElementById('educationLevel');
    if (educationLevelSelect) {
        educationLevelSelect.removeEventListener('change', updateSchoolAutocomplete);
        educationLevelSelect.addEventListener('change', updateSchoolAutocomplete);
        console.log('Education level change handler attached');
    }

    // Disability type change handler
    const disabilityTypeSelect = document.getElementById('disabilityType');
    if (disabilityTypeSelect) {
        disabilityTypeSelect.removeEventListener('change', toggleOtherDisabilityField);
        disabilityTypeSelect.addEventListener('change', toggleOtherDisabilityField);
        console.log('Disability type change handler attached');
    }

    // Student citizenship change handler
    if (studentCitizenshipInput) {
        studentCitizenshipInput.removeEventListener('change', toggleStudentIdFields);
        studentCitizenshipInput.addEventListener('change', toggleStudentIdFields);
        console.log('Student citizenship change handler attached');
    }

    // Senior IC input handler
    const seniorIcInput = document.getElementById('seniorIc');
    if (seniorIcInput) {
        seniorIcInput.removeEventListener('input', calculateAgeAndGender);
        seniorIcInput.addEventListener('input', calculateAgeAndGender);
        console.log('Senior IC input handler attached');
    }

    // Load user applications if logged in
    if (isLoggedIn && document.getElementById('userApplicationsContent')) {
        console.log('User is logged in, loading applications');
        loadUserApplications();
    }

    // Initialize event listeners
    initializeEventListeners();

    // Load school data
    loadSchoolData();
});

// File upload initialization
function initializeFileUploads() {
    console.log('Initializing file upload handlers...');

    // Student ID Photo
    const studentInput = document.getElementById('studentIdPhoto');
    const studentContainer = document.getElementById('studentFileUpload');
    if (studentInput && studentContainer) {
        console.log('Setting up student ID photo upload');
        studentContainer.onclick = null;
        studentInput.onchange = null;
        studentContainer.onclick = () => {
            console.log('Student file upload clicked');
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none';
            document.body.appendChild(fileInput);
            fileInput.onchange = (event) => {
                console.log('Student file selected:', event.target.files[0]?.name);
                if (event.target.files.length > 0) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(event.target.files[0]);
                    studentInput.files = dataTransfer.files;
                    const p = studentContainer.querySelector('p');
                    if (p) p.textContent = `Uploaded: ${event.target.files[0].name}`;
                    studentInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                document.body.removeChild(fileInput);
            };
            fileInput.click();
        };
        studentInput.onchange = (e) => {
            console.log('Student file input changed:', e.target.files[0]?.name);
            const p = studentContainer.querySelector('p');
            if (p) {
                p.textContent = e.target.files.length > 0 
                    ? `Uploaded: ${e.target.files[0].name}`
                    : 'Click to upload student ID photo';
            }
        };
    } else {
        console.error('Student file upload elements not found');
    }

    // Senior IC Photo
    const seniorInput = document.getElementById('seniorIcPhoto');
    const seniorContainer = document.getElementById('seniorFileUpload');
    if (seniorInput && seniorContainer) {
        console.log('Setting up senior IC photo upload');
        seniorContainer.onclick = null;
        seniorInput.onchange = null;
        seniorContainer.onclick = () => {
            console.log('Senior file upload clicked');
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none';
            document.body.appendChild(fileInput);
            fileInput.onchange = (event) => {
                console.log('Senior file selected:', event.target.files[0]?.name);
                if (event.target.files.length > 0) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(event.target.files[0]);
                    seniorInput.files = dataTransfer.files;
                    const p = seniorContainer.querySelector('p');
                    if (p) p.textContent = `Uploaded: ${event.target.files[0].name}`;
                    seniorInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                document.body.removeChild(fileInput);
            };
            fileInput.click();
        };
        seniorInput.onchange = (e) => {
            console.log('Senior file input changed:', e.target.files[0]?.name);
            const p = seniorContainer.querySelector('p');
            if (p) {
                p.textContent = e.target.files.length > 0 
                    ? `Uploaded: ${e.target.files[0].name}`
                    : 'Click to upload IC photo';
            }
        };
    } else {
        console.error('Senior file upload elements not found');
    }

    // OKU Card Photo
    const okuInput = document.getElementById('okuCardPhoto');
    const okuContainer = document.getElementById('okuFileUpload');
    if (okuInput && okuContainer) {
        console.log('Setting up OKU card photo upload');
        okuContainer.onclick = null;
        okuInput.onchange = null;
        okuContainer.onclick = () => {
            console.log('OKU file upload clicked');
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.style.display = 'none';
            document.body.appendChild(fileInput);
            fileInput.onchange = (event) => {
                console.log('OKU file selected:', event.target.files[0]?.name);
                if (event.target.files.length > 0) {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(event.target.files[0]);
                    okuInput.files = dataTransfer.files;
                    const p = okuContainer.querySelector('p');
                    if (p) p.textContent = `Uploaded: ${event.target.files[0].name}`;
                    okuInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                document.body.removeChild(fileInput);
            };
            fileInput.click();
        };
        okuInput.onchange = (e) => {
            console.log('OKU file input changed:', e.target.files[0]?.name);
            const p = okuContainer.querySelector('p');
            if (p) {
                p.textContent = e.target.files.length > 0 
                    ? `Uploaded: ${e.target.files[0].name}`
                    : 'Click to upload OKU card photo';
            }
        };
    } else {
        console.error('OKU file upload elements not found');
    }
}

// Select concession type
function selectConcessionType(type) {
    console.log('Selecting concession type:', type);
    currentApplicationType = type;
    showScreen('form');
    
    // Show relevant form fields
    document.querySelectorAll('.conditional-fields').forEach(field => field.classList.remove('active'));
    const fields = document.getElementById(`${type}Fields`);
    if (fields) {
        fields.classList.add('active');
    } else {
        console.error(`Fields for ${type} not found`);
    }

    // Update form title
    const formTitle = document.getElementById('formTitle');
    if (formTitle) {
        formTitle.textContent = `${type.toUpperCase()} Concession Application`;
    }

    // Set form type
    const applicationTypeInput = document.getElementById('applicationType');
    if (applicationTypeInput) {
        applicationTypeInput.value = type;
    }

    // Reset form
    const form = document.getElementById('applicationForm');
    if (form) {
        form.reset();
    }

    // Reset conditional fields
    resetConditionalFields();

    // Set required fields
    setRequiredFields(type);

    // Reinitialize autocomplete for student form
    if (type === 'student') {
        console.log('Reinitializing student form autocompletes');
        setTimeout(() => {
            if (window.studentCitizenshipAutocomplete) {
                window.studentCitizenshipAutocomplete.destroy();
                window.studentCitizenshipAutocomplete = new Autocomplete('studentCitizenship', 'studentCitizenshipDropdown', nationalityData);
                console.log('Student citizenship autocomplete reinitialized');
            }
            if (window.schoolAutocomplete) {
                window.schoolAutocomplete.destroy();
                window.schoolAutocomplete = new Autocomplete('schoolName', 'schoolNameDropdown', schoolData.university);
                console.log('School name autocomplete reinitialized');
            }
        }, 100);
    }

    // Reinitialize file uploads
    setTimeout(() => {
        console.log('Reinitializing file uploads after form switch');
        initializeFileUploads();
    }, 200);
}

// Reset conditional form fields
function resetConditionalFields() {
    console.log('Resetting conditional fields');
    const otherDisabilityContainer = document.getElementById('otherDisabilityContainer');
    if (otherDisabilityContainer) {
        otherDisabilityContainer.classList.remove('show');
    }

    const studentIcContainer = document.getElementById('studentIcContainer');
    const studentPassportContainer = document.getElementById('studentPassportContainer');
    if (studentIcContainer) {
        studentIcContainer.style.display = 'block';
    }
    if (studentPassportContainer) {
        studentPassportContainer.style.display = 'none';
    }
}

// Toggle other disability field
function toggleOtherDisabilityField() {
    console.log('Toggling other disability field');
    const disabilityType = document.getElementById('disabilityType')?.value;
    const otherDisabilityContainer = document.getElementById('otherDisabilityContainer');
    const otherDisabilityInput = document.getElementById('otherDisability');

    if (disabilityType === 'other') {
        console.log('Showing other disability field');
        otherDisabilityContainer?.classList.add('show');
        otherDisabilityInput?.setAttribute('required', 'required');
    } else {
        console.log('Hiding other disability field');
        otherDisabilityContainer?.classList.remove('show');
        otherDisabilityInput?.removeAttribute('required');
        otherDisabilityInput.value = '';
    }
}

// Toggle student ID fields
function toggleStudentIdFields() {
    console.log('Toggling student ID fields');
    const citizenship = document.getElementById('studentCitizenship')?.value.toLowerCase();
    const studentIcContainer = document.getElementById('studentIcContainer');
    const studentPassportContainer = document.getElementById('studentPassportContainer');
    const studentIcInput = document.getElementById('studentIc');
    const studentPassportInput = document.getElementById('studentPassport');

    if (citizenship === 'malaysia') {
        console.log('Showing IC field for Malaysian student');
        studentIcContainer.style.display = 'block';
        studentPassportContainer.style.display = 'none';
        studentIcInput?.setAttribute('required', 'required');
        studentPassportInput?.removeAttribute('required');
        studentPassportInput.value = '';
    } else {
        console.log('Showing passport field for non-Malaysian student');
        studentIcContainer.style.display = 'none';
        studentPassportContainer.style.display = 'block';
        studentIcInput?.removeAttribute('required');
        studentIcInput.value = '';
        studentPassportInput?.setAttribute('required', 'required');
    }
}

// Calculate age and gender from IC
function calculateAgeAndGender() {
    console.log('Calculating age and gender from IC');
    const icNumber = document.getElementById('seniorIc')?.value;
    const ageInput = document.getElementById('seniorAge');
    const genderInput = document.getElementById('seniorGender');

    if (icNumber?.length === 12 && /^\d{12}$/.test(icNumber)) {
        console.log('Valid IC number, processing...');
        const year = parseInt(icNumber.substring(0, 2));
        const month = parseInt(icNumber.substring(2, 4));
        const day = parseInt(icNumber.substring(4, 6));
        const fullYear = year <= 30 ? 2000 + year : 1900 + year;

        const today = new Date();
        const birthDate = new Date(fullYear, month - 1, day);
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();

        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        const lastDigit = parseInt(icNumber.charAt(11));
        const gender = lastDigit % 2 === 0 ? 'female' : 'male';

        ageInput.value = age;
        genderInput.value = gender;
        console.log(`Calculated age: ${age}, gender: ${gender}`);
    } else {
        console.log('Invalid IC number, clearing age and gender');
        ageInput.value = '';
        genderInput.value = '';
    }
}

// Set required form fields
function setRequiredFields(type) {
    console.log('Setting required fields for type:', type);
    document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
        field.removeAttribute('required');
    });

    const requiredFields = {
        oku: ['okuIc', 'okuCardNumber', 'disabilityType', 'okuCardPhoto'],
        senior: ['seniorIc', 'seniorIcPhoto'],
        student: ['studentCitizenship', 'educationLevel', 'schoolName', 'matrixNumber', 'studentIdPhoto']
    };

    requiredFields[type]?.forEach(id => {
        const field = document.getElementById(id);
        if (field) {
            field.setAttribute('required', 'required');
            console.log(`Set ${id} as required`);
        }
    });

    const fullNameField = document.getElementById('fullName');
    if (fullNameField) {
        fullNameField.setAttribute('required', 'required');
        console.log('Set fullName as required');
    }
}

// Handle form submission
async function handleFormSubmission(e) {
    e.preventDefault();
    console.log('Form submission started');
    const form = e.target;
    const formData = new FormData(form);
    const currentType = formData.get('type');
    console.log('Current application type:', currentType);

    // Validate autocomplete fields
    const requiredAutocompleteFields = currentType === 'student' ? ['studentCitizenship', 'schoolName'] : [];
    let validationFailed = false;
    for (const fieldName of requiredAutocompleteFields) {
        const field = document.getElementById(fieldName);
        if (field?.value.trim() === '') {
            console.log('Autocomplete validation failed for:', fieldName);
            field.setCustomValidity('This field is required');
            validationFailed = true;
        } else {
            field.setCustomValidity('');
        }
    }

    if (!form.checkValidity() || validationFailed) {
        console.log('Form validation failed');
        form.reportValidity();
        return;
    }

    formData.append('_token', document.querySelector('input[name="_token"]')?.value);
    console.log('Form data collected:', {
        type: formData.get('type'),
        fullName: formData.get('fullName'),
        ic: formData.get('okuIc') || formData.get('seniorIc') || formData.get('studentIc'),
        citizenship: formData.get('studentCitizenship'),
        schoolName: formData.get('schoolName')
    });

    const application = {
        id: 'APP' + Date.now(),
        type: currentType,
        fullName: formData.get('fullName'),
        status: 'pending',
        applicationDate: new Date().toISOString()
    };

    if (currentType === 'oku') {
        application.ic = formData.get('ic');
        application.okuCardNumber = formData.get('okuCardNumber');
        application.disabilityType = formData.get('disabilityType');
        if (application.disabilityType === 'other') {
            application.otherDisability = formData.get('otherDisability');
        }
        const photo = formData.get('okuCardPhoto');
        if (photo?.size > 0) application.photoName = photo.name;
    } else if (currentType === 'senior') {
        application.ic = formData.get('seniorIc');
        application.age = parseInt(formData.get('age')) || null;
        application.gender = formData.get('gender');
        const photo = formData.get('seniorIcPhoto');
        if (photo?.size > 0) application.photoName = photo.name;
    } else if (currentType === 'student') {
        application.studentCitizenship = formData.get('studentCitizenship');
        application.ic = formData.get('studentIc');
        application.passportNumber = formData.get('passportNumber');
        application.educationLevel = formData.get('educationLevel');
        application.schoolName = formData.get('schoolName');
        application.matrixNumber = formData.get('matrixNumber');
        const photo = formData.get('studentIdPhoto');
        if (photo?.size > 0) application.photoName = photo.name;
    }

    console.log('Validating application:', application);
    const result = okuHandler.handle(application);
    console.log('Validation result:', result);

    if (!result.valid) {
        console.log('Validation failed:', result.message);
        Swal.fire({
            title: 'Validation Error',
            text: result.message,
            icon: 'error'
        });
        return;
    }

    console.log('Submitting application to backend...');
    try {
        const response = await fetch('/concession/submit', {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') }
        });
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);

        if (data.success) {
            console.log('Application submitted successfully');
            applications.push(data.application);
            localStorage.setItem('concessionApplications', JSON.stringify(applications));
            Swal.fire({
                title: 'Application Submitted!',
                text: 'Your concession card application has been submitted successfully.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                console.log('Redirecting to main concession card page');
                window.location.href = 'http://127.0.0.1:8000/concession_card';
            });
            loadUserApplications();
        } else {
            console.log('Submission failed:', data.message);
            Swal.fire({
                title: 'Submission Failed',
                text: data.message || Object.values(data.errors || {}).flat().join('\n'),
                icon: 'error'
            });
        }
    } catch (error) {
        console.error('Submission error:', error);
        applications.push(application);
        localStorage.setItem('concessionApplications', JSON.stringify(applications));
        Swal.fire({
            title: 'Error',
            text: 'Failed to submit application: ' + error.message,
            icon: 'error'
        });
    }
}

// Show application status
function showApplicationStatus(app) {
    console.log('Showing application status for:', app.id);
    showScreen('status');
    statusCurrentPage = 0;
    renderStatusTable();
}

// Render status table
function renderStatusTable() {
    console.log('Rendering status table, page:', statusCurrentPage);
    const pageSize = 10;
    const sortedApplications = [...applications].sort((a, b) => new Date(b.applicationDate) - new Date(a.applicationDate));
    const total = sortedApplications.length;
    const maxPage = Math.ceil(total / pageSize) - 1;
    statusCurrentPage = Math.max(0, Math.min(statusCurrentPage, maxPage));

    const start = statusCurrentPage * pageSize;
    const end = start + pageSize;
    const paginatedApps = sortedApplications.slice(start, end);

    const statusContent = document.getElementById('statusContent');
    if (!statusContent) {
        console.error('Status content element not found');
        return;
    }

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
                            <td>${app.fullName || '-'}</td>
                            <td><span class="status-badge ${app.type}">${getTypeLabel(app.type)}</span></td>
                            <td><span class="status-badge ${app.status}">${getStatusLabel(app.status)}</span></td>
                            <td>${formatDate(app.applicationDate)}</td>
                            <td>
                                <button class="action-btn view" onclick="viewUserApplication('${app.id}')">View</button>
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
    console.log('Status table rendered');
}

// Change status page
function changeStatusPage(delta) {
    console.log('Changing status page by:', delta);
    statusCurrentPage += delta;
    renderStatusTable();
}

// Autocomplete class
class Autocomplete {
    constructor(inputId, dropdownId, data) {
        console.log('Initializing autocomplete for:', inputId);
        this.input = document.getElementById(inputId);
        this.dropdown = document.getElementById(dropdownId);
        this.data = data;
        this.selectedIndex = -1;
        this.filteredData = [];
        if (this.input && this.dropdown) this.init();
    }

    init() {
        console.log('Setting up autocomplete event listeners');
        this.handleInput = this.handleInput.bind(this);
        this.handleKeydown = this.handleKeydown.bind(this);
        this.handleBlur = () => {
            console.log('Autocomplete blur, hiding dropdown');
            setTimeout(() => this.hideDropdown(), 200);
        };
        this.handleFocus = () => {
            console.log('Autocomplete focus, showing dropdown');
            this.showDropdown();
        };
        this.handleClickOutside = (e) => {
            if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
                console.log('Clicked outside autocomplete, hiding dropdown');
                this.hideDropdown();
            }
        };

        this.input.addEventListener('input', this.handleInput);
        this.input.addEventListener('keydown', this.handleKeydown);
        this.input.addEventListener('blur', this.handleBlur);
        this.input.addEventListener('focus', this.handleFocus);
        document.addEventListener('click', this.handleClickOutside);
    }

    handleInput(e) {
        console.log('Autocomplete input changed:', e.target.value);
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
        
        console.log('Autocomplete keydown:', e.key);
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
                    console.log('Selecting item:', this.filteredData[this.selectedIndex]);
                    this.selectItem(this.filteredData[this.selectedIndex]);
                }
                break;
            case 'Escape':
                console.log('Escape pressed, hiding dropdown');
                this.hideDropdown();
                break;
        }
    }

    renderDropdown() {
        console.log('Rendering autocomplete dropdown');
        this.dropdown.innerHTML = this.filteredData.length === 0 
            ? '<div class="autocomplete-item">No results found</div>'
            : this.filteredData.map((item, index) => `
                <div class="autocomplete-item ${index === this.selectedIndex ? 'highlighted' : ''}" 
                     data-index="${index}">
                    <span class="item-code">${item.code}</span>
                    <span class="item-name">${item.name}</span>
                    <div class="item-group">${item.group}</div>
                </div>
            `).join('');

        this.dropdown.querySelectorAll('.autocomplete-item').forEach((item, index) => {
            item.removeEventListener('mousedown', this.handleItemClick);
            item.addEventListener('mousedown', this.handleItemClick.bind(this, index));
        });
    }

    handleItemClick(index, e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Autocomplete item clicked:', this.filteredData[index]);
        this.selectItem(this.filteredData[index]);
    }

    updateHighlight() {
        console.log('Updating autocomplete highlight:', this.selectedIndex);
        this.dropdown.querySelectorAll('.autocomplete-item').forEach((item, index) => {
            item.classList.toggle('highlighted', index === this.selectedIndex);
        });
    }

    selectItem(item) {
        console.log('Selecting autocomplete item:', item);
        this.input.value = item.name;
        this.input.setAttribute('data-code', item.code);
        this.hideDropdown();
        this.input.setCustomValidity('');
        this.input.dispatchEvent(new Event('input', { bubbles: true }));
        this.input.dispatchEvent(new Event('change', { bubbles: true }));
    }

    showDropdown() {
        console.log('Showing autocomplete dropdown');
        if (this.filteredData.length > 0 || this.input.value === '') {
            this.filteredData = this.input.value === '' ? this.data : this.filteredData;
            this.renderDropdown();
            this.dropdown.style.display = 'block';
        }
    }

    hideDropdown() {
        console.log('Hiding autocomplete dropdown');
        this.dropdown.style.display = 'none';
        this.selectedIndex = -1;
    }

    destroy() {
        console.log('Destroying autocomplete instance');
        if (this.input) {
            this.input.removeEventListener('input', this.handleInput);
            this.input.removeEventListener('keydown', this.handleKeydown);
            this.input.removeEventListener('blur', this.handleBlur);
            this.input.removeEventListener('focus', this.handleFocus);
        }
        document.removeEventListener('click', this.handleClickOutside);
    }
}

// Get school data by education level
function getSchoolDataByLevel(level) {
    console.log('Getting school data for level:', level);
    return schoolData[level] || [];
}

// Update school autocomplete
function updateSchoolAutocomplete() {
    console.log('Updating school autocomplete');
    const educationLevel = document.getElementById('educationLevel')?.value;
    const schoolInput = document.getElementById('schoolName');
    const schoolDropdown = document.getElementById('schoolNameDropdown');

    if (!educationLevel || !schoolInput || !schoolDropdown) {
        console.error('School autocomplete elements missing');
        return;
    }

    schoolInput.value = '';
    if (window.schoolAutocomplete) {
        window.schoolAutocomplete.data = getSchoolDataByLevel(educationLevel);
        window.schoolAutocomplete.filteredData = [];
        window.schoolAutocomplete.hideDropdown();
        console.log('School autocomplete updated with new data');
    }
}

// Load school data
async function loadSchoolData() {
    console.log('Loading school data from JSON');
    try {
        const response = await fetch('/schools_data.json');
        const data = await response.json();
        schoolData.primary = data.primary?.map(school => ({
            code: school.name.substring(0, 10),
            name: school.name,
            group: `${school.state} - ${school.district}`,
            state: school.state,
            district: school.district
        })) || [];
        schoolData.secondary = data.secondary?.map(school => ({
            code: school.name.substring(0, 10),
            name: school.name,
            group: `${school.state} - ${school.district}`,
            state: school.state,
            district: school.district
        })) || [];
        console.log('School data loaded successfully');
    } catch (error) {
        console.error('Error loading school data:', error);
    }
}

// Load user applications
async function loadUserApplications() {
    console.log('Loading user applications...');
    try {
        const response = await fetch('/concession/applications', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });
        console.log('Applications response status:', response.status);
        const data = await response.json();
        console.log('Applications response data:', data);

        if (data.success) {
            applications = data.applications || [];
            localStorage.setItem('concessionApplications', JSON.stringify(applications));
            console.log('User applications loaded:', applications);
            renderUserApplications(applications);
        } else {
            console.error('Failed to load applications:', data.message);
            Swal.fire({
                title: 'Error',
                text: data.message || 'Failed to load applications',
                icon: 'error'
            });
        }
    } catch (error) {
        console.error('Error loading applications:', error);
        Swal.fire({
            title: 'Error',
            text: 'Failed to load applications: ' + error.message,
            icon: 'error'
        });
    }
}

// Render user applications
function renderUserApplications(userApplications) {
    console.log('Rendering user applications:', userApplications);
    const content = document.getElementById('userApplicationsContent');
    if (!content) {
        console.error('User applications content element not found');
        return;
    }

    if (!userApplications?.length) {
        console.log('No applications found, rendering empty state');
        content.innerHTML = `
            <div class="no-applications">
                <i class="fas fa-file-alt"></i>
                <h3>No Applications Yet</h3>
                <p>You haven't submitted any concession card applications yet.</p>
                <button class="btn btn-primary" onclick="showScreen('main')">Apply Now</button>
            </div>
        `;
        return;
    }

    const sortedApplications = userApplications.sort((a, b) => new Date(b.applicationDate) - new Date(a.applicationDate));
    content.innerHTML = `
        <div class="applications-grid">
            ${sortedApplications.map(app => `
                <div class="application-card ${app.status}">
                    <div class="application-header">
                        <div class="application-type">
                            <span class="type-badge ${app.type}">${getTypeLabel(app.type)}</span>
                        </div>
                        <div class="application-status">
                            <span class="status-badge ${app.status}">${getStatusLabel(app.status)}</span>
                        </div>
                    </div>
                    <div class="application-body">
                        <h4>${app.fullName || '-'}</h4>
                        <p class="application-id">ID: ${app.id}</p>
                        <p class="application-date">Applied: ${formatDate(app.applicationDate)}</p>
                        <div class="application-ic">
                            <strong>IC:</strong> ${getIcNumber(app) || 'N/A'}
                        </div>
                    </div>
                    <div class="application-footer">
                        <button class="btn btn-sm btn-info" onclick="viewUserApplication('${app.id}')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    console.log('User applications rendered');
}

// View user application
async function viewUserApplication(applicationId) {
    console.log('Viewing application:', applicationId);
    try {
        const response = await fetch(`/concession/view/${applicationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });
        console.log('View application response status:', response.status);
        const data = await response.json();
        console.log('View application response data:', data);

        if (!data.success) {
            throw new Error(data.message || 'Application not found');
        }

        const app = data.application;
        if (!app) {
            throw new Error('Application data is missing');
        }

        const index = applications.findIndex(a => a.id === applicationId);
        if (index !== -1) {
            applications[index] = app;
        } else {
            applications.push(app);
        }
        localStorage.setItem('concessionApplications', JSON.stringify(applications));
        console.log('Application data updated in local storage');

        viewApplication(app);
    } catch (error) {
        console.error('Error viewing application:', error);
        Swal.fire({
            title: 'Error',
            text: 'Failed to load application details: ' + error.message,
            icon: 'error'
        });
    }
}

// View application details
function viewApplication(app) {
    console.log('Displaying application details:', app?.id);
    const modal = document.getElementById('viewModal');
    const modalContent = document.getElementById('applicationDetails');

    if (!modal || !modalContent || !app) {
        console.error('Modal or application data missing');
        Swal.fire({
            title: 'Error',
            text: 'Unable to display application details',
            icon: 'error'
        });
        return;
    }

    let detailsTable = `
        <h3>Application Details</h3>
        <table class="details-table">
            <tr><th>Field</th><th>Value</th></tr>
            <tr><td>Application ID</td><td>${app.id || '-'}</td></tr>
            <tr><td>Name</td><td>${app.fullName || '-'}</td></tr>
            <tr><td>Concession Type</td><td>${getTypeLabel(app.type)}</td></tr>
            <tr><td>Status</td><td>${getStatusLabel(app.status)}</td></tr>
            <tr><td>Date & Time</td><td>${formatDate(app.applicationDate)}</td></tr>
    `;

    if (app.type === 'oku') {
        console.log('Rendering OKU application details');
        detailsTable += `
            <tr><td>IC Number</td><td>${app.ic || '-'}</td></tr>
            <tr><td>OKU Card Number</td><td>${app.okuCardNumber || '-'}</td></tr>
            <tr><td>Disability Type</td><td>${app.disabilityType || '-'}</td></tr>
            ${app.disabilityType === 'other' && app.otherDisability ? `<tr><td>Other Disability</td><td>${app.otherDisability || '-'}</td></tr>` : ''}
            ${app.photoUrl ? `<tr><td>OKU Card Photo</td><td><a href="${app.photoUrl}" target="_blank">View Photo</a></td></tr>` : `<tr><td>OKU Card Photo</td><td>${app.photoName || 'No photo uploaded'}</td></tr>`}
        `;
    } else if (app.type === 'senior') {
        console.log('Rendering Senior application details');
        detailsTable += `
            <tr><td>IC Number</td><td>${app.ic || '-'}</td></tr>
            <tr><td>Age</td><td>${app.age || '-'}</td></tr>
            ${app.photoUrl ? `<tr><td>IC Photo</td><td><a href="${app.photoUrl}" target="_blank">View Photo</a></td></tr>` : `<tr><td>IC Photo</td><td>${app.photoName || 'No photo uploaded'}</td></tr>`}
        `;
    } else if (app.type === 'student') {
        console.log('Rendering Student application details');
        detailsTable += `
            <tr><td>${app.studentCitizenship === 'Malaysia' ? 'IC Number' : 'Passport Number'}</td><td>${app.ic || app.passportNumber || '-'}</td></tr>
            <tr><td>Citizenship</td><td>${app.studentCitizenship || '-'}</td></tr>
            <tr><td>Education Level</td><td>${app.educationLevel || '-'}</td></tr>
            <tr><td>School Name</td><td>${app.schoolName || '-'}</td></tr>
            <tr><td>Matrix Number</td><td>${app.matrixNumber || '-'}</td></tr>
            ${app.photoUrl ? `<tr><td>Student ID Photo</td><td><a href="${app.photoUrl}" target="_blank">View Photo</a></td></tr>` : `<tr><td>Student ID Photo</td><td>${app.photoName || 'No photo uploaded'}</td></tr>`}
        `;
    }

    if (app.adminNotes) {
        detailsTable += `<tr><td>Admin Notes</td><td>${app.adminNotes || '-'}</td></tr>`;
    }
    if (app.reviewedAt) {
        detailsTable += `<tr><td>Reviewed At</td><td>${formatDate(app.reviewedAt)}</td></tr>`;
    }
    if (app.reviewedBy) {
        detailsTable += `<tr><td>Reviewed By</td><td>${app.reviewedBy || '-'}</td></tr>`;
    }

    detailsTable += '</table>';
    modalContent.innerHTML = detailsTable;
    modal.classList.add('active');
    console.log('Application details modal displayed');
}

// Helper functions
function getTypeLabel(type) {
    const labels = {
        oku: 'OKU',
        senior: 'Senior Citizen',
        student: 'Student'
    };
    return labels[type] || type;
}

function getStatusLabel(status) {
    const labels = {
        pending: 'Pending',
        approved: 'Approved',
        rejected: 'Rejected'
    };
    return labels[status] || status;
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function getIcNumber(application) {
    return application.ic || application.studentIc || application.seniorIc || 'N/A';
}


// Render user applications
function renderUserApplications(userApplications) {
    console.log('Rendering user applications:', userApplications);
    const content = document.getElementById('userApplicationsContent');
    if (!content) {
        console.error('User applications content element not found');
        Swal.fire({
            title: 'Error',
            text: 'User applications content element not found. Please check the HTML.',
            icon: 'error'
        });
        return;
    }

    if (!userApplications?.length) {
        console.log('No applications found, rendering empty state');
        content.innerHTML = `
            <div class="no-applications">
                <i class="fas fa-file-alt"></i>
                <h3>No Applications Yet</h3>
                <p>You haven't submitted any concession card applications yet.</p>
                <button class="btn btn-primary" onclick="showScreen('main')">Apply Now</button>
            </div>
        `;
        return;
    }

    const sortedApplications = userApplications.sort((a, b) => new Date(b.applicationDate) - new Date(a.applicationDate));
    content.innerHTML = `
        <div class="applications-grid">
            ${sortedApplications.map(app => `
                <div class="application-card ${app.status}">
                    <div class="application-header">
                        <div class="application-type">
                            <span class="type-badge ${app.type}">${getTypeLabel(app.type)}</span>
                        </div>
                        <div class="application-status">
                            <span class="status-badge ${app.status}">${getStatusLabel(app.status)}</span>
                        </div>
                    </div>
                    <div class="application-body">
                        <h4>${app.fullName || '-'}</h4>
                        <p class="application-id">ID: ${app.id}</p>
                        <p class="application-date">Applied: ${formatDate(app.applicationDate)}</p>
                        <div class="application-ic">
                            <strong>IC:</strong> ${getIcNumber(app) || 'N/A'}
                        </div>
                    </div>
                    <div class="application-footer">
                        <button class="btn btn-sm btn-info" onclick="viewUserApplication('${app.id}')">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
    console.log('User applications rendered');
}

// View user application
async function viewUserApplication(applicationId) {
    console.log('Attempting to view application:', applicationId);
    const modal = document.getElementById('viewModal');
    if (!modal) {
        console.error('View modal element not found');
        Swal.fire({
            title: 'Error',
            text: 'View modal element not found. Please check the HTML.',
            icon: 'error'
        });
        return;
    }

    try {
        console.log('Fetching application data from /concession/view/', applicationId);
        const response = await fetch(`/concession/view/${applicationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });
        console.log('View application response status:', response.status);
        const data = await response.json();
        console.log('View application response data:', JSON.stringify(data, null, 2));

        if (!data.success) {
            console.error('Server responded with failure:', data.message);
            throw new Error(data.message || 'Application not found');
        }

        const app = data.application;
        if (!app || !app.id) {
            console.error('Application data is missing or invalid:', app);
            throw new Error('Application data is missing or invalid');
        }

        const index = applications.findIndex(a => a.id === applicationId);
        if (index !== -1) {
            applications[index] = app;
            console.log('Updated existing application in local storage:', applicationId);
        } else {
            applications.push(app);
            console.log('Added new application to local storage:', applicationId);
        }
        localStorage.setItem('concessionApplications', JSON.stringify(applications));
        console.log('Application data saved to local storage');

        viewApplication(app);
    } catch (error) {
        console.error('Error viewing application:', error.message, error.stack);
        Swal.fire({
            title: 'Error',
            text: 'Failed to load application details: ' + error.message,
            icon: 'error'
        });
    }
}

// View application details
function viewApplication(app) {
    console.log('Displaying application details for ID:', app?.id);
    const modal = document.getElementById('viewModal');
    const modalContent = document.getElementById('applicationDetails');

    if (!modal || !modalContent) {
        console.error('Modal or application details element not found', { modal, modalContent });
        Swal.fire({
            title: 'Error',
            text: 'Unable to display application details: Modal elements missing',
            icon: 'error'
        });
        return;
    }

    if (!app || !app.id) {
        console.error('Invalid application data:', app);
        Swal.fire({
            title: 'Error',
            text: 'Invalid application data',
            icon: 'error'
        });
        return;
    }

    let detailsTable = `
        <h3>Application Details</h3>
        <table class="details-table">
            <tr><th>Field</th><th>Value</th></tr>
            <tr><td>Application ID</td><td>${app.id || '-'}</td></tr>
            <tr><td>Name</td><td>${app.fullName || '-'}</td></tr>
            <tr><td>Concession Type</td><td>${getTypeLabel(app.type)}</td></tr>
            <tr><td>Status</td><td>${getStatusLabel(app.status)}</td></tr>
            <tr><td>Date & Time</td><td>${formatDate(app.applicationDate)}</td></tr>
    `;

    if (app.type === 'oku') {
        console.log('Rendering OKU application details');
        detailsTable += `
            <tr><td>IC Number</td><td>${app.ic || '-'}</td></tr>
            <tr><td>OKU Card Number</td><td>${app.okuCardNumber || '-'}</td></tr>
            <tr><td>Disability Type</td><td>${app.disabilityType || '-'}</td></tr>
            ${app.disabilityType === 'other' && app.otherDisability ? `<tr><td>Other Disability</td><td>${app.otherDisability || '-'}</td></tr>` : ''}
            ${app.photoUrl ? `<tr><td>OKU Card Photo</td><td><a href="${app.photoUrl}" target="_blank">View Photo</a></td></tr>` : `<tr><td>OKU Card Photo</td><td>${app.photoName || 'No photo uploaded'}</td></tr>`}
        `;
    } else if (app.type === 'senior') {
        console.log('Rendering Senior application details');
        detailsTable += `
            <tr><td>IC Number</td><td>${app.ic || '-'}</td></tr>
            <tr><td>Age</td><td>${app.age || '-'}</td></tr>
            <tr><td>Gender</td><td>${app.gender || '-'}</td></tr>
            ${app.photoUrl ? `<tr><td>IC Photo</td><td><a href="${app.photoUrl}" target="_blank">View Photo</a></td></tr>` : `<tr><td>IC Photo</td><td>${app.photoName || 'No photo uploaded'}</td></tr>`}
        `;
    } else if (app.type === 'student') {
        console.log('Rendering Student application details');
        detailsTable += `
            <tr><td>${app.studentCitizenship === 'Malaysia' ? 'IC Number' : 'Passport Number'}</td><td>${app.ic || app.passportNumber || '-'}</td></tr>
            <tr><td>Citizenship</td><td>${app.studentCitizenship || '-'}</td></tr>
            <tr><td>Education Level</td><td>${app.educationLevel || '-'}</td></tr>
            <tr><td>School Name</td><td>${app.schoolName || '-'}</td></tr>
            <tr><td>Matrix Number</td><td>${app.matrixNumber || '-'}</td></tr>
            ${app.photoUrl ? `<tr><td>Student ID Photo</td><td><a href="${app.photoUrl}" target="_blank">View Photo</a></td></tr>` : `<tr><td>Student ID Photo</td><td>${app.photoName || 'No photo uploaded'}</td></tr>`}
        `;
    }

    if (app.adminNotes) {
        detailsTable += `<tr><td>Admin Notes</td><td>${app.adminNotes || '-'}</td></tr>`;
    }
    if (app.reviewedAt) {
        detailsTable += `<tr><td>Reviewed At</td><td>${formatDate(app.reviewedAt)}</td></tr>`;
    }
    if (app.reviewedBy) {
        detailsTable += `<tr><td>Reviewed By</td><td>${app.reviewedBy || '-'}</td></tr>`;
    }

    detailsTable += '</table>';
    modalContent.innerHTML = detailsTable;
    console.log('Setting modal content and displaying modal');
    modal.classList.add('active');
    console.log('Modal should now be visible with application details');
}