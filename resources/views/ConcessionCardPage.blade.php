@extends('Layout.master')

@section('title', 'Home Page')

@push('styles')
    <link href="{{ asset('css/ConcessionCardSimple.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-header">
                    <h1>Concession Card</h1>
                    <p class="hero-subtitle">Special discounts for students, seniors, and persons with disabilities</p>
                </div>
                
                <div class="steps-process">
                    <div class="step">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h3>Choose Category</h3>
                            <p>Select your concession type: OKU, Senior Citizen, or Student</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h3>Fill Application</h3>
                            <p>Complete the online form with your personal details and documents</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h3>Submit & Wait</h3>
                            <p>Submit your application and wait for admin approval</p>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-content">
                            <h3>Get Your Card</h3>
                            <p>Receive your digital concession card and enjoy 30% discount</p>
                        </div>
                    </div>
                </div>
                
                <div class="hero-benefits">
                    <div class="benefit">
                        <i class="fas fa-percentage"></i>
                        <span>30% Discount</span>
                    </div>
                    <div class="benefit">
                        <i class="fas fa-clock"></i>
                        <span>Fast Approval</span>
                    </div>
                    <div class="benefit">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Digital Card</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Application Section -->
        <section class="application-section">
            <div class="container">
                <div class="section-header">
                    <h2>Apply for Concession Card</h2>
                    <p>Choose your category and start your application</p>
                </div>

                <!-- Main Selection Screen -->
                <div id="mainScreen" class="screen active">
                    <div class="concession-types">
                        <div class="concession-card" data-type="oku">
                            <div class="card-icon">
                                <img src="{{ asset('images/oku_icon.png') }}" alt="OKU Icon">
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
                            <div class="card-icon">
                                <img src="{{ asset('images/senior_icon.png') }}" alt="Senior Citizen Icon">
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
                            <div class="card-icon">
                                <img src="{{ asset('images/student_icon.png') }}" alt="Student Icon">
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
                </div>

                <!-- Application Form Screen -->
                <div id="formScreen" class="screen">
                    <div class="form-container">
                        <div class="form-header">
                            <button id="backBtn" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </button>
                            <h2 id="formTitle">Application Form</h2>
                        </div>

                        <form id="applicationForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="applicationType" name="type">
                            
                            <!-- Common Fields -->
                            <div class="form-group">
                                <label for="fullName">Full Name *</label>
                                <input type="text" id="fullName" name="fullName" required>
                                <span class="error-message"></span>
                            </div>

                            <!-- OKU Specific Fields -->
                            <div id="okuFields" class="conditional-fields">
                                <div class="form-group">
                                    <label for="okuIc">IC Number *</label>
                                    <input type="text" id="okuIc" name="ic" maxlength="12" required>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="okuCardNumber">OKU Card Number *</label>
                                    <input type="text" id="okuCardNumber" name="okuCardNumber" required>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="disabilityType">Disability Type *</label>
                                    <select id="disabilityType" name="disabilityType" required>
                                        <option value="">Select Disability Type</option>
                                        <option value="visual">Visual Impairment</option>
                                        <option value="hearing">Hearing Impairment</option>
                                        <option value="mobility">Mobility Impairment</option>
                                        <option value="cognitive">Cognitive Disability</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group" id="otherDisabilityContainer">
                                    <label for="otherDisability">Other Disability Information *</label>
                                    <textarea id="otherDisability" name="otherDisability" rows="3"></textarea>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="okuCardPhoto">OKU Card Photo *</label>
                                    <div class="file-upload" id="okuFileUpload" onclick="document.getElementById('okuCardPhoto').click()">
                                        <input type="file" id="okuCardPhoto" name="okuCardPhoto" accept="image/*" required>
                                        <div class="file-upload-content">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                <polyline points="7,10 12,15 17,10"/>
                                                <line x1="12" y1="15" x2="12" y2="3"/>
                                            </svg>
                                            <p>Click to upload OKU card photo</p>
                                        </div>
                                    </div>
                                    <span class="error-message"></span>
                                </div>
                            </div>

                            <!-- Senior Citizen Specific Fields -->
                            <div id="seniorFields" class="conditional-fields">
                                <div class="form-group">
                                    <label for="seniorIc">IC Number *</label>
                                    <input type="text" id="seniorIc" name="seniorIc" maxlength="12" required>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="calculatedAge">Age</label>
                                    <input type="text" id="calculatedAge" name="calculatedAge" readonly>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="seniorIcPhoto">IC Photo *</label>
                                    <div class="file-upload" id="seniorFileUpload" onclick="document.getElementById('seniorIcPhoto').click()">
                                        <input type="file" id="seniorIcPhoto" name="seniorIcPhoto" accept="image/*" required>
                                        <div class="file-upload-content">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                <polyline points="7,10 12,15 17,10"/>
                                                <line x1="12" y1="15" x2="12" y2="3"/>
                                            </svg>
                                            <p>Click to upload IC photo</p>
                                        </div>
                                    </div>
                                    <span class="error-message"></span>
                                </div>
                                <!-- Auto-calculated fields (hidden) -->
                                <input type="hidden" id="seniorAge" name="age">
                                <input type="hidden" id="seniorGender" name="gender">
                            </div>

                            <!-- Student Specific Fields -->
                            <div id="studentFields" class="conditional-fields">
                                <div class="form-group">
                                    <label for="studentCitizenship">Citizenship *</label>
                                    <div class="autocomplete-container">
                                        <input type="text" id="studentCitizenship" name="studentCitizenship" placeholder="Type to search nationality..." autocomplete="off" required>
                                        <div id="studentCitizenshipDropdown" class="autocomplete-dropdown"></div>
                                    </div>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group" id="studentIcContainer">
                                    <label for="studentIc">IC Number *</label>
                                    <input type="text" id="studentIc" name="studentIc" maxlength="12">
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group" id="studentPassportContainer" style="display: none;">
                                    <label for="studentPassport">Passport Number *</label>
                                    <input type="text" id="studentPassport" name="passportNumber">
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="educationLevel">Education Level *</label>
                                    <select id="educationLevel" name="educationLevel" required>
                                        <option value="">Select Education Level</option>
                                        <option value="primary">Primary School</option>
                                        <option value="secondary">Secondary School</option>
                                        <option value="university">University</option>
                                    </select>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="schoolName">School Name *</label>
                                    <div class="autocomplete-container">
                                        <input type="text" id="schoolName" name="schoolName" placeholder="Type to search school..." autocomplete="off" required>
                                        <div id="schoolNameDropdown" class="autocomplete-dropdown"></div>
                                    </div>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="matrixNumber">Matrix Number *</label>
                                    <input type="text" id="matrixNumber" name="matrixNumber" required>
                                    <span class="error-message"></span>
                                </div>
                                <div class="form-group">
                                    <label for="studentIdPhoto">Student ID Photo *</label>
                                    <div class="file-upload" id="studentFileUpload" onclick="document.getElementById('studentIdPhoto').click()">
                                        <input type="file" id="studentIdPhoto" name="studentIdPhoto" accept="image/*" required>
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
                        <button id="statusBackBtn" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Main Menu
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
                            <h2>Admin Dashboard</h2>
                            <button id="adminBackBtn" class="btn btn-secondary">Back to Main</button>
                        </div>

                        <div class="admin-stats">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <p>Total Applications</p>
                                    <span id="totalApps">0</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon pending">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <p>Pending Review</p>
                                    <span id="pendingApps">0</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon approved">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <p>Approved</p>
                                    <span id="approvedApps">0</span>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon rejected">
                                    <i class="fas fa-times-circle"></i>
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
            </div>

            <!-- User Applications Status Section -->
            @auth
            <section class="user-applications-section">
                <div class="container">
                    <div class="section-header">
                        <h2>My Applications</h2>
                        <p>Track the status of your concession card applications</p>
                    </div>
                    
                    <div class="applications-status">
                        <div id="userApplicationsContent">
                            <!-- User applications will be loaded here -->
                        </div>
                    </div>
                </div>
            </section>
            @endauth

            <!-- Modals -->
            <div id="viewModal" class="modal">
                <div class="modal-content">
                    <div id="applicationDetails"></div>
                    <div class="modal-actions">
                        <button id="closeView" class="btn btn-secondary">Close</button>
                    </div>
                </div>
            </div>
        </main>

        <script>
            // Add event listener for senior IC number input to calculate age
            document.addEventListener('DOMContentLoaded', function() {
                const seniorIcInput = document.getElementById('seniorIc');
                const calculatedAgeInput = document.getElementById('calculatedAge');
                const seniorAgeInput = document.getElementById('seniorAge');
                const seniorGenderInput = document.getElementById('seniorGender');

                if (seniorIcInput && calculatedAgeInput && seniorAgeInput && seniorGenderInput) {
                    seniorIcInput.addEventListener('input', function() {
                        const ic = this.value.replace(/[^0-9]/g, ''); // Remove non-digits
                        if (ic.length === 12) {
                            const yy = parseInt(ic.substring(0, 2));
                            const mm = ic.substring(2, 4);
                            const dd = ic.substring(4, 6);
                            const currentYear = new Date().getFullYear() % 100; // Get last two digits of current year
                            let age = currentYear - yy;
                            if (age < 0) {
                                age += 100; // Handle cases like 99 -> 1999
                            }
                            calculatedAgeInput.value = `${age} years`;
                            seniorAgeInput.value = age; // Update hidden age field
                            
                            // Calculate gender
                            const lastDigit = parseInt(ic.substring(11, 12));
                            seniorGenderInput.value = lastDigit % 2 === 1 ? 'male' : 'female';
                        } else {
                            calculatedAgeInput.value = '';
                            seniorAgeInput.value = '';
                            seniorGenderInput.value = '';
                        }
                    });
                }
            });
        </script>

        <script src="{{ asset('js/ConcessionCard.js') }}" defer></script>
    @endsection