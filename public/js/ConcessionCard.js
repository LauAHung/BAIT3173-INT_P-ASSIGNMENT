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
        if (!application.ic || application.ic.length !== 12) {
            return { valid: false, message: 'IC number must be 12 digits' };
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
        if (!application.ic || application.ic.length !== 12) {
            return { valid: false, message: 'IC number must be 12 digits' };
        }
        if (!application.age || application.age < 60) {
            return { valid: false, message: 'Age must be 60 or above' };
        }
        if (!application.gender) {
            return { valid: false, message: 'Gender is required' };
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
        if (!application.studentCitizenship) {
            return { valid: false, message: 'Citizenship is required' };
        }
        if (!application.educationLevel) {
            return { valid: false, message: 'Education level is required' };
        }
        if (!application.schoolName) {
            return { valid: false, message: 'School name is required' };
        }
        if (!application.matrixNumber || application.matrixNumber.length < 4) {
            return { valid: false, message: 'Matrix number must be at least 4 characters' };
        }
        // Check IC or Passport based on citizenship
        const isMalaysian = application.studentCitizenship.toLowerCase() === 'malaysia';
        if (isMalaysian) {
            if (!application.ic || application.ic.length !== 12) {
                return { valid: false, message: 'IC number must be 12 digits for Malaysian citizens' };
            }
        } else {
            if (!application.passportNumber || application.passportNumber.length < 6) {
                return { valid: false, message: 'Passport number is required for non-Malaysian citizens' };
            }
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

document.addEventListener('DOMContentLoaded', async () => {
    console.log('DOM loaded, initializing...'); // Debug log
    
    // Check if user is logged in
    const isLoggedIn = document.querySelector('meta[name="user-authenticated"]')?.getAttribute('content') === 'true';

    // Fallback: event delegation to ensure Apply buttons always work
    document.addEventListener('click', (event) => {
        const applyBtn = event.target.closest('.concession-card .btn-primary');
        if (!applyBtn) return;
        event.preventDefault();

        if (!isLoggedIn) {
            Swal.fire({
                title: 'Login Required',
                text: 'You need to be logged in to apply for a concession card.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Login',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '/signin';
                }
            });
            return;
        }

        const card = applyBtn.closest('.concession-card');
        if (card && card.dataset.type) {
            selectConcessionType(card.dataset.type);
        }
    });
    
    // Debug: Check if elements exist
    console.log('Student citizenship input:', document.getElementById('studentCitizenship'));
    console.log('Student citizenship dropdown:', document.getElementById('studentCitizenshipDropdown'));
    console.log('School name input:', document.getElementById('schoolName'));
    console.log('School name dropdown:', document.getElementById('schoolNameDropdown'));
    
    // Debug: Check file upload elements
    console.log('Student file upload container:', document.getElementById('studentFileUpload'));
    console.log('Student file input:', document.getElementById('studentIdPhoto'));
    console.log('Senior file upload container:', document.getElementById('seniorFileUpload'));
    console.log('Senior file input:', document.getElementById('seniorIcPhoto'));
    console.log('OKU file upload container:', document.getElementById('okuFileUpload'));
    console.log('OKU file input:', document.getElementById('okuCardPhoto'));
    
    // Attach event listener to "Apply Now" buttons (only for main screen cards)
    document.querySelectorAll('.concession-card .btn-primary').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Check if user is logged in
            if (!isLoggedIn) {
                Swal.fire({
                    title: 'Login Required',
                    text: 'You need to be logged in to apply for a concession card.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Login',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '/signin';
                    }
                });
                return;
            }
            
            const card = button.closest('.concession-card');
            selectConcessionType(card.dataset.type);
        });
    });

    // If the page has the "My Applications" section, load data
    if (document.getElementById('userApplicationsContent') && isLoggedIn) {
        loadUserApplications();
    }

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

    // Initialize file upload handlers
    initializeFileUploads();

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
    const studentCitizenshipInput = document.getElementById('studentCitizenship');
    const studentCitizenshipDropdown = document.getElementById('studentCitizenshipDropdown');
    if (studentCitizenshipInput && studentCitizenshipDropdown) {
        window.studentCitizenshipAutocomplete = new Autocomplete('studentCitizenship', 'studentCitizenshipDropdown', nationalityData);
        console.log('Student citizenship autocomplete initialized');
    } else {
        console.error('Student citizenship elements not found');
    }
    
    // Initialize school autocomplete
    const schoolNameInput = document.getElementById('schoolName');
    const schoolNameDropdown = document.getElementById('schoolNameDropdown');
    if (schoolNameInput && schoolNameDropdown) {
        window.schoolAutocomplete = new Autocomplete('schoolName', 'schoolNameDropdown', schoolData.university);
        console.log('School autocomplete initialized');
    } else {
        console.error('School elements not found');
    }
    
    // Add event listener for education level change
    const educationLevelSelect = document.getElementById('educationLevel');
    if (educationLevelSelect) {
        educationLevelSelect.addEventListener('change', updateSchoolAutocomplete);
    }

    // Add event listener for disability type change (OKU)
    const disabilityTypeSelect = document.getElementById('disabilityType');
    if (disabilityTypeSelect) {
        disabilityTypeSelect.addEventListener('change', toggleOtherDisabilityField);
    }

    // Add event listener for citizenship change (Student)
    const studentCitizenshipInputElement = document.getElementById('studentCitizenship');
    if (studentCitizenshipInputElement) {
        studentCitizenshipInputElement.addEventListener('change', toggleStudentIdFields);
    }

    // Add event listener for IC number change (Senior)
    const seniorIcInput = document.getElementById('seniorIc');
    if (seniorIcInput) {
        seniorIcInput.addEventListener('input', calculateAgeAndGender);
    }

    // Load user applications if logged in
    if (isLoggedIn) {
        loadUserApplications();
    }
});

function initializeFileUploads() {
    console.log('Initializing file uploads...');
    
    // Student ID Photo
    const studentInput = document.getElementById('studentIdPhoto');
    const studentContainer = document.getElementById('studentFileUpload');
    console.log('Student input element:', studentInput);
    console.log('Student container element:', studentContainer);
    
    if (studentInput && studentContainer) {
        console.log('Setting up student file upload');
        
        // Remove any existing event listeners
        studentContainer.onclick = null;
        studentInput.onchange = null;
        
        studentContainer.onclick = function(e) {
            console.log('Student file upload clicked');
            console.log('Attempting to trigger file input click...');
            
            // Create a new file input element
            const newFileInput = document.createElement('input');
            newFileInput.type = 'file';
            newFileInput.accept = 'image/*';
            newFileInput.style.display = 'none';
            document.body.appendChild(newFileInput);
            
            newFileInput.onchange = function(event) {
                console.log('File selected via new input:', event.target.files[0]);
                if (event.target.files.length > 0) {
                    // Copy the file to the original input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(event.target.files[0]);
                    studentInput.files = dataTransfer.files;
                    
                    // Update the display text
                    const p = studentContainer.querySelector('p');
                    if (p) {
                        p.textContent = `Uploaded: ${event.target.files[0].name}`;
                    }
                    
                    // Trigger change event on original input
                    studentInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                
                // Clean up
                document.body.removeChild(newFileInput);
            };
            
            // Trigger the file selection
            newFileInput.click();
        };
        
        studentInput.onchange = function(e) {
            console.log('Student file selected:', e.target.files[0]);
            const p = studentContainer.querySelector('p');
            if (p) {
                p.textContent = e.target.files.length > 0 
                    ? `Uploaded: ${e.target.files[0].name}`
                    : 'Click to upload student ID photo';
            }
        };
    } else {
        console.log('Student file upload elements not found');
    }
    
    // Senior IC Photo
    const seniorInput = document.getElementById('seniorIcPhoto');
    const seniorContainer = document.getElementById('seniorFileUpload');
    console.log('Senior input element:', seniorInput);
    console.log('Senior container element:', seniorContainer);
    
    if (seniorInput && seniorContainer) {
        console.log('Setting up senior file upload');
        
        // Remove any existing event listeners
        seniorContainer.onclick = null;
        seniorInput.onchange = null;
        
        seniorContainer.onclick = function(e) {
            console.log('Senior file upload clicked');
            console.log('Attempting to trigger file input click...');
            
            // Create a new file input element
            const newFileInput = document.createElement('input');
            newFileInput.type = 'file';
            newFileInput.accept = 'image/*';
            newFileInput.style.display = 'none';
            document.body.appendChild(newFileInput);
            
            newFileInput.onchange = function(event) {
                console.log('File selected via new input:', event.target.files[0]);
                if (event.target.files.length > 0) {
                    // Copy the file to the original input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(event.target.files[0]);
                    seniorInput.files = dataTransfer.files;
                    
                    // Update the display text
                    const p = seniorContainer.querySelector('p');
                    if (p) {
                        p.textContent = `Uploaded: ${event.target.files[0].name}`;
                    }
                    
                    // Trigger change event on original input
                    seniorInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                
                // Clean up
                document.body.removeChild(newFileInput);
            };
            
            // Trigger the file selection
            newFileInput.click();
        };
        
        seniorInput.onchange = function(e) {
            console.log('Senior file selected:', e.target.files[0]);
            const p = seniorContainer.querySelector('p');
            if (p) {
                p.textContent = e.target.files.length > 0 
                    ? `Uploaded: ${e.target.files[0].name}`
                    : 'Click to upload IC photo';
            }
        };
    } else {
        console.log('Senior file upload elements not found');
    }
    
    // OKU Card Photo
    const okuInput = document.getElementById('okuCardPhoto');
    const okuContainer = document.getElementById('okuFileUpload');
    console.log('OKU input element:', okuInput);
    console.log('OKU container element:', okuContainer);
    
    if (okuInput && okuContainer) {
        console.log('Setting up OKU file upload');
        
        // Remove any existing event listeners
        okuContainer.onclick = null;
        okuInput.onchange = null;
        
        okuContainer.onclick = function(e) {
            console.log('OKU file upload clicked');
            console.log('Attempting to trigger file input click...');
            
            // Create a new file input element
            const newFileInput = document.createElement('input');
            newFileInput.type = 'file';
            newFileInput.accept = 'image/*';
            newFileInput.style.display = 'none';
            document.body.appendChild(newFileInput);
            
            newFileInput.onchange = function(event) {
                console.log('File selected via new input:', event.target.files[0]);
                if (event.target.files.length > 0) {
                    // Copy the file to the original input
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(event.target.files[0]);
                    okuInput.files = dataTransfer.files;
                    
                    // Update the display text
                    const p = okuContainer.querySelector('p');
                    if (p) {
                        p.textContent = `Uploaded: ${event.target.files[0].name}`;
                    }
                    
                    // Trigger change event on original input
                    okuInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
                
                // Clean up
                document.body.removeChild(newFileInput);
            };
            
            // Trigger the file selection
            newFileInput.click();
        };
        
        okuInput.onchange = function(e) {
            console.log('OKU file selected:', e.target.files[0]);
            const p = okuContainer.querySelector('p');
            if (p) {
                p.textContent = e.target.files.length > 0 
                    ? `Uploaded: ${e.target.files[0].name}`
                    : 'Click to upload OKU card photo';
            }
        };
    } else {
        console.log('OKU file upload elements not found');
    }
}

function selectConcessionType(type) {
    currentApplicationType = type;
    showScreen('form');
    document.querySelectorAll('.conditional-fields').forEach(field => field.classList.remove('active'));
    document.getElementById(`${type}Fields`).classList.add('active');
    document.getElementById('formTitle').textContent = `${type.toUpperCase()} Concession Application`;
    document.getElementById('applicationType').value = type;
    document.getElementById('applicationForm').reset();
    
    // Reset conditional fields
    resetConditionalFields();
    
    // Set required attributes based on type
    setRequiredFields(type);
    
    // Reinitialize autocomplete for student form
    if (type === 'student') {
        setTimeout(() => {
            // Reinitialize school autocomplete
            if (window.schoolAutocomplete) {
                window.schoolAutocomplete.destroy();
            }
            const schoolNameInput = document.getElementById('schoolName');
            const schoolNameDropdown = document.getElementById('schoolNameDropdown');
            if (schoolNameInput && schoolNameDropdown) {
                window.schoolAutocomplete = new Autocomplete('schoolName', 'schoolNameDropdown', schoolData.university);
                console.log('School autocomplete reinitialized');
            }
            
            // Reinitialize citizenship autocomplete
            if (window.studentCitizenshipAutocomplete) {
                window.studentCitizenshipAutocomplete.destroy();
            }
            const studentCitizenshipInput = document.getElementById('studentCitizenship');
            const studentCitizenshipDropdown = document.getElementById('studentCitizenshipDropdown');
            if (studentCitizenshipInput && studentCitizenshipDropdown) {
                window.studentCitizenshipAutocomplete = new Autocomplete('studentCitizenship', 'studentCitizenshipDropdown', nationalityData);
                console.log('Student citizenship autocomplete reinitialized');
            }
        }, 100);
    }
    
    // Reinitialize file upload handlers after form is shown
    setTimeout(() => {
        initializeFileUploads();
    }, 200);
}

function resetConditionalFields() {
    // Reset OKU other disability field
    const otherDisabilityContainer = document.getElementById('otherDisabilityContainer');
    if (otherDisabilityContainer) {
        otherDisabilityContainer.classList.remove('show');
    }
    
    // Reset student ID fields
    const studentIcContainer = document.getElementById('studentIcContainer');
    const studentPassportContainer = document.getElementById('studentPassportContainer');
    if (studentIcContainer) {
        studentIcContainer.style.display = 'block';
    }
    if (studentPassportContainer) {
        studentPassportContainer.style.display = 'none';
    }
}

function toggleOtherDisabilityField() {
    const disabilityType = document.getElementById('disabilityType').value;
    const otherDisabilityContainer = document.getElementById('otherDisabilityContainer');
    const otherDisabilityInput = document.getElementById('otherDisability');
    
    if (disabilityType === 'other') {
        otherDisabilityContainer.classList.add('show');
        otherDisabilityInput.setAttribute('required', 'required');
    } else {
        otherDisabilityContainer.classList.remove('show');
        otherDisabilityInput.removeAttribute('required');
        otherDisabilityInput.value = '';
    }
}

function toggleStudentIdFields() {
    const citizenship = document.getElementById('studentCitizenship').value.toLowerCase();
    const studentIcContainer = document.getElementById('studentIcContainer');
    const studentPassportContainer = document.getElementById('studentPassportContainer');
    const studentIcInput = document.getElementById('studentIc');
    const studentPassportInput = document.getElementById('studentPassport');
    
    if (citizenship === 'malaysia') {
        studentIcContainer.style.display = 'block';
        studentPassportContainer.style.display = 'none';
        studentIcInput.setAttribute('required', 'required');
        studentPassportInput.removeAttribute('required');
        studentPassportInput.value = '';
    } else {
        studentIcContainer.style.display = 'none';
        studentPassportContainer.style.display = 'block';
        studentIcInput.removeAttribute('required');
        studentIcInput.value = '';
        studentPassportInput.setAttribute('required', 'required');
    }
}

function calculateAgeAndGender() {
    const icNumber = document.getElementById('seniorIc').value;
    const ageInput = document.getElementById('seniorAge');
    const genderInput = document.getElementById('seniorGender');
    
    if (icNumber.length === 12 && /^\d{12}$/.test(icNumber)) {
        // Extract birth date from IC (YYMMDD format)
        const year = parseInt(icNumber.substring(0, 2));
        const month = parseInt(icNumber.substring(2, 4));
        const day = parseInt(icNumber.substring(4, 6));
        
        // Determine century (assume 00-30 is 2000s, 31-99 is 1900s)
        const fullYear = year <= 30 ? 2000 + year : 1900 + year;
        
        // Calculate age
        const today = new Date();
        const birthDate = new Date(fullYear, month - 1, day);
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        // Determine gender (last digit: even = female, odd = male)
        const lastDigit = parseInt(icNumber.charAt(11));
        const gender = lastDigit % 2 === 0 ? 'female' : 'male';
        
        // Update hidden fields
        ageInput.value = age;
        genderInput.value = gender;
        
        // Show calculated values to user (optional)
        console.log(`Calculated age: ${age}, gender: ${gender}`);
    } else {
        ageInput.value = '';
        genderInput.value = '';
    }
}

function setRequiredFields(type) {
    // Clear all required attributes first
    document.querySelectorAll('input[required], select[required], textarea[required]').forEach(field => {
        field.removeAttribute('required');
    });
    
    // Set required attributes based on type
    if (type === 'oku') {
        document.getElementById('okuIc').setAttribute('required', 'required');
        document.getElementById('okuCardNumber').setAttribute('required', 'required');
        document.getElementById('disabilityType').setAttribute('required', 'required');
        document.getElementById('okuCardPhoto').setAttribute('required', 'required');
    } else if (type === 'senior') {
        document.getElementById('seniorIc').setAttribute('required', 'required');
        document.getElementById('seniorIcPhoto').setAttribute('required', 'required');
    } else if (type === 'student') {
        document.getElementById('studentCitizenship').setAttribute('required', 'required');
        document.getElementById('educationLevel').setAttribute('required', 'required');
        document.getElementById('schoolName').setAttribute('required', 'required');
        document.getElementById('matrixNumber').setAttribute('required', 'required');
        document.getElementById('studentIdPhoto').setAttribute('required', 'required');
    }
    
    // Always required fields
    document.getElementById('fullName').setAttribute('required', 'required');
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
        requiredFields = [];
    } else if (currentType === 'senior') {
        requiredFields = [];
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
        schoolName: formData.get('schoolName'),
        studentIc: formData.get('studentIc'),
        passportNumber: formData.get('passportNumber')
    }); // Debug log
    
    // Additional debug for student form
    if (currentType === 'student') {
        console.log('Student form debug:');
        console.log('- studentCitizenship value:', formData.get('studentCitizenship'));
        console.log('- studentIc value from formData:', formData.get('studentIc'));
        console.log('- passportNumber value from formData:', formData.get('passportNumber'));
        
        // Check actual DOM elements
        const studentIcElement = document.getElementById('studentIc');
        const studentPassportElement = document.getElementById('studentPassport');
        console.log('- studentIc DOM value:', studentIcElement ? studentIcElement.value : 'element not found');
        console.log('- studentPassport DOM value:', studentPassportElement ? studentPassportElement.value : 'element not found');
        
        // Check if citizenship is Malaysia to determine which field should be used
        const citizenship = formData.get('studentCitizenship');
        console.log('- Citizenship check:', citizenship);
        console.log('- Is Malaysian?', citizenship && citizenship.toLowerCase() === 'malaysia');
    }
    
    // Additional debug for senior form
    if (currentType === 'senior') {
        console.log('Senior form debug:');
        console.log('- seniorIc value from formData:', formData.get('seniorIc'));
        console.log('- age value from formData:', formData.get('age'));
        console.log('- gender value from formData:', formData.get('gender'));
        
        // Check actual DOM elements
        const seniorIcElement = document.getElementById('seniorIc');
        console.log('- seniorIc DOM value:', seniorIcElement ? seniorIcElement.value : 'element not found');
    }
    
    const application = {
        id: 'APP' + Date.now(),
        type: formData.get('type'),
        fullName: formData.get('fullName'),
        status: 'pending',
        applicationDate: new Date().toISOString()
    };

    if (application.type === 'oku') {
        application.ic = formData.get('ic');
        application.okuCardNumber = formData.get('okuCardNumber');
        application.disabilityType = formData.get('disabilityType');
        if (application.disabilityType === 'other') {
            application.otherDisability = formData.get('otherDisability');
        }
        const photo = formData.get('okuCardPhoto');
        if (photo && photo.size > 0) application.photoName = photo.name;
    } else if (application.type === 'senior') {
        application.ic = formData.get('seniorIc');
        application.age = parseInt(formData.get('age')) || null;
        application.gender = formData.get('gender');
        const photo = formData.get('seniorIcPhoto');
        if (photo && photo.size > 0) application.photoName = photo.name;
    } else if (application.type === 'student') {
        application.studentCitizenship = formData.get('studentCitizenship');
        // For student form, use the specific field name
        application.ic = formData.get('studentIc');
        application.passportNumber = formData.get('passportNumber');
        application.educationLevel = formData.get('educationLevel');
        application.schoolName = formData.get('schoolName');
        application.matrixNumber = formData.get('matrixNumber');
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
                
                // Show success notification with Sweet Alert
                Swal.fire({
                    title: 'Application Submitted!',
                    text: 'Your concession card application has been submitted successfully.',
                    icon: 'success',
                    confirmButtonText: 'View Status'
                }).then((result) => {
                    if (result.isConfirmed) {
                        showApplicationStatus(data.application);
                    } else {
                        showScreen('main');
                    }
                });
                
                // Reload user applications
                loadUserApplications();
            } else {
                let errorMsg = data.message || 'Submission failed';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('\n');
                }
                console.log('Submission failed:', errorMsg); // Debug log
                
                // Show error notification with Sweet Alert
                Swal.fire({
                    title: 'Submission Failed',
                    text: errorMsg,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
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
        // Bind methods to preserve context
        this.handleInput = this.handleInput.bind(this);
        this.handleKeydown = this.handleKeydown.bind(this);
        this.handleBlur = () => {
            setTimeout(() => this.hideDropdown(), 200);
        };
        this.handleFocus = () => this.showDropdown();
        this.handleClickOutside = (e) => {
            if (!this.input.contains(e.target) && !this.dropdown.contains(e.target)) {
                this.hideDropdown();
            }
        };
        
        this.input.addEventListener('input', this.handleInput);
        this.input.addEventListener('keydown', this.handleKeydown);
        this.input.addEventListener('blur', this.handleBlur);
        this.input.addEventListener('focus', this.handleFocus);
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', this.handleClickOutside);
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
    
    destroy() {
        // Remove event listeners
        if (this.input) {
            this.input.removeEventListener('input', this.handleInput);
            this.input.removeEventListener('keydown', this.handleKeydown);
            this.input.removeEventListener('blur', this.handleBlur);
            this.input.removeEventListener('focus', this.handleFocus);
        }
        document.removeEventListener('click', this.handleClickOutside);
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

// Removed admin stats UI updater from user script

// Removed admin table rendering from user script

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
                <tr><td>IC Number</td><td>${app.ic || '-'}</td></tr>
                <tr><td>OKU Card Number</td><td>${app.okuCardNumber || '-'}</td></tr>
                <tr><td>Disability Type</td><td>${app.disabilityType || '-'}</td></tr>
                ${app.disabilityType === 'other' ? `<tr><td>Other Disability</td><td>${app.otherDisability || '-'}</td></tr>` : ''}
                <tr><td>OKU Card Photo</td><td>${app.photoName || '-'}</td></tr>
            `;
        } else if (app.type === 'senior') {
            detailsTable += `
                <tr><td>IC Number</td><td>${app.ic || '-'}</td></tr>
                <tr><td>Age</td><td>${app.age || '-'}</td></tr>
                <tr><td>Gender</td><td>${app.gender || '-'}</td></tr>
                <tr><td>IC Photo</td><td>${app.photoName || '-'}</td></tr>
            `;
        } else if (app.type === 'student') {
            detailsTable += `
                <tr><td>Citizenship</td><td>${app.studentCitizenship || '-'}</td></tr>
                <tr><td>IC Number</td><td>${app.ic || '-'}</td></tr>
                <tr><td>Passport Number</td><td>${app.passportNumber || '-'}</td></tr>
                <tr><td>Education Level</td><td>${app.educationLevel || '-'}</td></tr>
                <tr><td>School Name</td><td>${app.schoolName || '-'}</td></tr>
                <tr><td>Matrix Number</td><td>${app.matrixNumber || '-'}</td></tr>
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

// Admin-only function removed from user script
/* async function approveApplication(id) {
    if (!confirm('Are you sure you want to approve this application?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/concession/applications/${id}/approve`, {
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
            // Admin-only status messaging removed in user script
            
            // Update local data
            const app = applications.find(a => a.id === id);
            if (app) {
                app.status = 'approved';
                app.reviewedBy = 'Current Admin'; // You might want to get actual admin name
                app.reviewedAt = new Date().toISOString();
                localStorage.setItem('concessionApplications', JSON.stringify(applications));
            }
            
            // Reload data from server to ensure consistency
            if (window.location.pathname.includes('card-approval')) {
                await loadAllApplicationsForAdmin();
            } else {
                // Admin-only UI refresh removed in user script
            }
        } else {
            // Admin-only messaging removed
        }
    } catch (error) {
        console.error('Error approving application:', error);
        // Admin-only messaging removed
    }
} */

/* async function rejectApplication(id) {
    if (!confirm('Are you sure you want to reject this application?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/concession/applications/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                notes: prompt('Add rejection notes (optional):') || ''
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Admin-only messaging removed
            
            // Update local data
            const app = applications.find(a => a.id === id);
            if (app) {
                app.status = 'rejected';
                app.reviewedBy = 'Current Admin'; // You might want to get actual admin name
                app.reviewedAt = new Date().toISOString();
                localStorage.setItem('concessionApplications', JSON.stringify(applications));
            }
            
            // Reload data from server to ensure consistency
            if (window.location.pathname.includes('card-approval')) {
                await loadAllApplicationsForAdmin();
            } else {
                // Admin-only UI refresh removed in user script
            }
        } else {
            // Admin-only messaging removed
        }
    } catch (error) {
        console.error('Error rejecting application:', error);
        // Admin-only messaging removed
    }
} */

/* function withdrawApplication(id) {
    const app = applications.find(a => a.id === id);
    if (app) {
        app.status = 'pending';
        localStorage.setItem('concessionApplications', JSON.stringify(applications));
        // Admin-only UI refresh removed in user script
    }
} */

// Helper functions for UI
// Removed admin message helpers from user script

// Removed admin loading helpers from user script

// Removed admin loading helpers from user script

// Load all applications for admin approval page
// Removed admin loader from user script

// Load admin statistics for all applications
async function loadAdminAllStats() {
    try {
        const response = await fetch('/api/concession/admin/all-stats', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update the stats display
            document.getElementById('totalApps').textContent = data.stats.total;
            document.getElementById('pendingApps').textContent = data.stats.pending;
            document.getElementById('approvedApps').textContent = data.stats.approved;
            document.getElementById('rejectedApps').textContent = data.stats.rejected;
        }
    } catch (error) {
        console.error('Error loading admin stats:', error);
    }
}

// Load user applications for the status section
async function loadUserApplications() {
    console.log('Loading user applications...');
    try {
        const response = await fetch('/api/concession/applications');
        console.log('Response status:', response.status);
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            console.log('Applications loaded:', data.applications);
            renderUserApplications(data.applications);
        } else {
            console.error('Failed to load user applications:', data.message);
        }
    } catch (error) {
        console.error('Error loading user applications:', error);
    }
}

function renderUserApplications(userApplications) {
    console.log('Rendering user applications:', userApplications);
    const content = document.getElementById('userApplicationsContent');
    console.log('Content element:', content);
    
    if (!content) {
        console.error('userApplicationsContent element not found');
        return;
    }
    
    if (userApplications.length === 0) {
        console.log('No applications found, showing empty state');
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
    
    // Sort applications by date (newest first)
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
                        <h4>${app.fullName}</h4>
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
}

function viewUserApplication(applicationId) {
    // Find the application in the current applications array
    const app = applications.find(a => a.id === applicationId);
    if (app) {
        viewApplication(applicationId);
    } else {
        // If not found locally, fetch from server
        fetch(`/api/concession/applications/${applicationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    viewApplication(applicationId);
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Application not found',
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Error fetching application:', error);
                Swal.fire({
                    title: 'Error',
                    text: 'Failed to load application details',
                    icon: 'error'
                });
            });
    }
}

// Helper functions for user applications display
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
    // Get IC number based on application type
    if (application.type === 'student') {
        return application.studentIc || application.ic;
    } else if (application.type === 'senior') {
        return application.seniorIc || application.ic;
    } else {
        return application.ic;
    }
}