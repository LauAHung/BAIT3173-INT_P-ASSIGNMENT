@extends('Layout.master')

@section('title', 'Home Page')

@push('styles')
    <link href="{{ asset('css/ConcessionCard.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
@endpush

@section('content')
    <main>
        <div id="header"></div>
        <section class="first-section">
            <div class="background"></div>
            <div class="particles">
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>
                <div class="particle"></div>    
                <div class="particle"></div>
                <div class="particle"></div>
            </div>
            <div class="preface">
                <h1>Special Benefit</h1><br><br><br>
                <p>
                    Exclusive benefits, thoughtful discounts – everyone can enjoy the freedom of travel. <br><br><br>
                    Benefits designed for students, seniors, and special groups – making travel truly accessible, 
                    affordable, and inclusive.
                </p>
            </div>
            <div class="img_preface">
                <img src="{{ asset('images/concession_card.png') }}" alt="Concession Card">
            </div>
        </section>

        <section class="second-section">
            <div class="container">
                <main class="main-content">
                    <!-- Main Selection Screen -->
                    <div id="mainScreen" class="screen active">
                        <div class="hero-section">
                            <p class="hero-subtitle">Choose your concession type and enjoy 30% discount on your next purchase</p>
                        </div>

                        <div class="card-grid">
                            <div class="concession-card" data-type="oku">
                                <div class="card-icon oku">
                                    <img src="{{ asset('images/oku_icon.png') }}">
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
                                    <img src="{{ asset('images/senior_icon.png') }}">
                                </div>
                                <h3>Senior Citizen</h3>
                                <p>For citizens aged 60 and above</p>
                                <ul class="requirements">
                                    <li>Age 60+</li>
                                    <li>Valid IC</li>
                                    <li>Malaysian Citizenship</li>
                                </ul>
                                <button class="btn btn-primary">Apply Now</button>
                                <button class="btn btn-secondary" onclick="showScreen('admin')">View Applications</button>
                            </div>

                            <div class="concession-card" data-type="student">
                                <div class="card-icon student">
                                    <img src="{{ asset('images/student_icon.png') }}">
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
                                        <img src="{{ asset('images/application_icon.png') }}">
                                    </div>
                                    <h2 id="formTitle">Application Form</h2>
                                </div>
                            </div>

                            <form id="applicationForm" enctype="multipart/form-data">
                                @csrf
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
                                        <textarea id="disability" name="disability" rows="1"></textarea>
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
                                    <h2>Application List</h2>
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
                                <div class="table-container">
                                    <table id="applicationsTable">
                                        <thead>
                                            <tr>
                                                <th>Applicant</th>
                                                <th>Type</th>
                                                <th>Status</th>
                                                <th>Date & Time</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Applications will be populated by JavaScript -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="pagination">
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </section>

        <!-- Modals (moved here to avoid overflow: hidden clipping from .second-section) -->
        <div id="viewModal" class="modal">
            <div class="modal-content">
                <div id="applicationDetails"></div>
                <div class="modal-actions">
                    <button id="closeView" class="btn btn-secondary">Close</button>
                </div>
            </div>
        </div>
    </main>

<script src="{{ asset('js/ConcessionCard.js') }}" defer></script>
    
@endsection