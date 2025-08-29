@extends('Layout.master')

@section('title', 'Passenger Info - TravelFree')

@push('styles')
    <link href="{{ asset('css/PassengerInfoPage.css') }}" rel="stylesheet">
@endpush

@section('content')
<section>
    <!-- Display general error messages (e.g., duplicate IC/passport) -->
    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                alert('{{ $errors->first() }}');
            });
        </script>
    @endif
    <form action="{{ route('store.passengerinfo') }}" method="POST" data-selectseat-url="{{ route('selectseat') }}">
        @csrf
        <div class="passenger-main-layout">
            <div class="passenger-info-panel">
                <div class="heading">
                    <h2>Passenger Details</h2>
                </div>
                <div class="passenger-info-section">
                    @for ($i = 1; $i <= $passengers; $i++)
                        <div class="passenger-info-container">
                            <div class="passenger-info-form">
                                <div class="passenger-header" onclick="togglePassenger('p{{ $i }}')">
                                    <h2>Passenger {{ $i }}</h2>
                                    <button type="button" class="toggle-btn" id="toggle-p{{ $i }}">Collapse</button>
                                    <span class="error-indicator" id="error-indicator-p{{ $i }}">*</span>
                                </div>
                                <div class="passenger-info" id="p{{ $i }}-info">
                                    <div class="info-item">
                                        <span class="info-label">Name<a>*</a></span>
                                        <input type="text" class="info-value" name="passenger[{{ $i }}][name]" placeholder="John" required>
                                        <span class="error" id="error-p{{ $i }}-name"></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">MyKad no.<a>**</a></span>
                                        <input type="text" class="info-value" name="passenger[{{ $i }}][mykad]" placeholder="Required for Malaysian">
                                        <span class="error" id="error-p{{ $i }}-mykad"></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Passport no.<a>**</a></span>
                                        <input type="text" class="info-value" name="passenger[{{ $i }}][passport]" placeholder="Required for non-Malaysian">
                                        <span class="error" id="error-p{{ $i }}-passport"></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Passport expiry date<a>**</a></span>
                                        <input type="date" class="info-value" name="passenger[{{ $i }}][passport_expiry]">
                                        <span class="error" id="error-p{{ $i }}-passport_expiry"></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Contact no.<a>*</a></span>
                                        <input type="text" class="info-value" name="passenger[{{ $i }}][contact_no]" placeholder="011-12345678" required>
                                        <span class="error" id="error-p{{ $i }}-contact_no"></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Gender<a>*</a></span>
                                        <div class="gender">
                                            <label><input type="radio" name="passenger[{{ $i }}][gender]" value="male" required>Male</label>
                                            <label><input type="radio" name="passenger[{{ $i }}][gender]" value="female">Female</label>
                                        </div>
                                        <span class="error" id="error-p{{ $i }}-gender"></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Ticket Type<a>*</a></span>
                                        <select name="passenger[{{ $i }}][ticket_type]" id="ticket-type-{{ $i }}" required>
                                            <option value="" disabled selected>-- Please Select --</option>
                                            <option value="Dewasa/Adult">Dewasa/Adult</option>
                                            <option value="Kanak-kanak/Child">Kanak-kanak/Child</option>
                                            <option value="OKU">OKU</option>
                                        </select>
                                        <span class="error" id="error-p{{ $i }}-ticket_type"></span>
                                    </div>
                                    <span class="required"><a>***</a>Penalty charges given for applying wrong ticket type. Discount for concession ticket will auto apply at the next page.</span>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div class="heading2">
                    <h2>Confirmation</h2>
                </div>

                <div class="second-section">
                    <div class="confirm-container">
                        <span class="required">Please review your passenger info carefully and click confirmation button and proceed seat selection page.</span>
                        <div class="required-message">
                            <span class="required2"><a>*</a>Required Field</span>
                            <span class="required2"><a>**</a>Either MyKad or Passport is required</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="passenger-side-info">
                <div class="side-card">
                    <div class="side-title">Total price:</div>
                    <div class="side-price">MYR {{ number_format($journey->Price * $passengers, 2) }}</div>
                </div>
                <div class="side-card">
                    <div class="side-title">Trip Summary</div>
                    <div class="side-summary">
                        <div>Depart on {{ date('l, d F Y', strtotime($journey->DepartureTime)) }}</div>
                        <div class="side-flight">
                            <span>{{ date('h:i A', strtotime($journey->DepartureTime)) }}</span>
                            <span class="side-plane"><i class="fas fa-train"></i></span>
                            <span>{{ date('h:i A', strtotime($journey->ArrivalTime)) }}</span>
                        </div>
                        <div>{{ $journey->FromLocation}} → {{ $journey->ToLocation}}</div>
                        <div>
                            <?php
                                $departure = new DateTime($journey->DepartureTime);
                                $arrival = new DateTime($journey->ArrivalTime);
                                $interval = $departure->diff($arrival);
                                $duration = $interval->format('%hh %imin');
                            ?>
                            {{ $duration }}, Non-Stop
                        </div>
                    </div>
                </div>
                <div class="side-card">
                    <button type="submit" class="side-btn main">Select Seat</button>
                    <a href="{{ route('TrainSelectionPage') }}"><button type="button" class="side-btn">Back</button></a>
                </div>
            </div>
        </div>
    </form>
</section>
<script>
// Toggle passenger info section
function togglePassenger(pid) {
    const info = document.getElementById(pid + '-info');
    const btn = document.getElementById('toggle-' + pid);
    if (info.style.display === 'none') {
        info.style.display = '';
        btn.textContent = 'Collapse';
    } else {
        info.style.display = 'none';
        btn.textContent = 'Expand';
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const passengerCount = {{ $passengers }}; // Get passenger count from PHP

    form.addEventListener('submit', (event) => {
        let isValid = true;

        // Clear previous error messages and indicators
        document.querySelectorAll('.error').forEach(error => {
            error.textContent = '';
            error.classList.remove('show');
        });
        document.querySelectorAll('.error-indicator').forEach(indicator => {
            indicator.classList.remove('show');
        });

        // Validate each passenger dynamically
        for (let i = 1; i <= passengerCount; i++) {
            let hasError = false; // Track if current passenger has any validation errors

            // Get input fields for the current passenger
            const nameInput = document.querySelector(`input[name="passenger[${i}][name]"]`);
            const mykadInput = document.querySelector(`input[name="passenger[${i}][mykad]"]`);
            const passportInput = document.querySelector(`input[name="passenger[${i}][passport]"]`);
            const passportExpiryInput = document.querySelector(`input[name="passenger[${i}][passport_expiry]"]`);
            const contactInput = document.querySelector(`input[name="passenger[${i}][contact_no]"]`);
            const genderInputs = document.querySelectorAll(`input[name="passenger[${i}][gender]"]:checked`);
            const ticketType = document.querySelector(`select[name="passenger[${i}][ticket_type]"]`);

            // Name validation: Letters, spaces, hyphens, apostrophes
            const nameRegex = /^[a-zA-Z\s'-]{2,}$/;
            if (!nameRegex.test(nameInput.value.trim())) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-name`).textContent = '*Invalid name provided';
                document.getElementById(`error-p${i}-name`).classList.add('show');
            }

            // MyKad and Passport: At least one required, but not both
            const mykadRegex = /^\d{12}$/;
            const passportRegex = /^[a-zA-Z0-9]{6,12}$/;
            const hasMykad = mykadInput.value.trim() !== '';
            const hasPassport = passportInput.value.trim();
            if (!hasMykad && !hasPassport) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-mykad`).textContent = '*Either MyKad or Passport number is required';
                document.getElementById(`error-p${i}-mykad`).classList.add('show');
                document.getElementById(`error-p${i}-passport`).classList.add('show');
            } else if (hasMykad && hasPassport) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-mykad`).textContent = '*Provide only one of MyKad or Passport number';
                document.getElementById(`error-p${i}-mykad`).classList.add('show');
                document.getElementById(`error-p${i}-passport`).classList.add('show');
            } else if (hasMykad && !mykadRegex.test(mykadInput.value.trim())) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-mykad`).textContent = '*Invalid MyKad number: Must be 12 digits';
                document.getElementById(`error-p${i}-mykad`).classList.add('show');
            } else if (hasPassport && !passportRegex.test(passportInput.value.trim())) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-passport`).textContent = '*Invalid Passport number: Must be 6-12 alphanumeric characters';
                document.getElementById(`error-p${i}-passport`).classList.add('show');
            }

            // Passport expiry validation
            const hasPassportExpiry = passportExpiryInput.value.trim() !== '';
            if (hasPassport && !hasPassportExpiry) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-passport_expiry`).textContent = 'Passport expiry date is required when Passport is provided';
                document.getElementById(`error-p${i}-passport_expiry`).classList.add('show');
            } else if (hasMykad && hasPassportExpiry) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-passport_expiry`).textContent = 'Passport expiry date should not be provided when MyKad is entered';
                document.getElementById(`error-p${i}-passport_expiry`).classList.add('show');
            }

            // Contact number validation: Malaysian mobile format
            const contactRegex = /^01[0-9]-[0-9]{7,8}$/;
            if (!contactRegex.test(contactInput.value.trim())) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-contact_no`).textContent = '*Invalid contact number: Use format 01x-xxxxxxxx';
                document.getElementById(`error-p${i}-contact_no`).classList.add('show');
            }

            // Gender validation: Ensure one option is selected
            if (genderInputs.length === 0) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-gender`).textContent = '*Please select a gender';
                document.getElementById(`error-p${i}-gender`).classList.add('show');
            }

            // Ticket type validation: Ensure an option is selected
            if (!ticketType.value) {
                isValid = false;
                hasError = true;
                document.getElementById(`error-p${i}-ticket_type`).textContent = '*Please select a ticket type';
                document.getElementById(`error-p${i}-ticket_type`).classList.add('show');
            }

            // Show error indicator for this passenger if any validation error occurred
            if (hasError) {
                document.getElementById(`error-indicator-p${i}`).classList.add('show');
            }
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });
});
</script>
@endsection