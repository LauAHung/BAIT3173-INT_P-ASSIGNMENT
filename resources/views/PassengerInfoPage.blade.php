@extends('Layout.master')

@section('title', 'Passenger Info - TravelFree')

@push('styles')
    <link href="css/PassengerInfoPage.css" rel="stylesheet">
@endpush

@section('content')

<section>

<form action="#" data-selectseat-url="{{ route('selectseat') }}">
    <div class="heading">
    <h2>Passenger Details</h2>
    </div>

    <div class="passenger-info-section">
        <div class="passenger-info-container">
            <div class="passenger-info-form">
                <h2>Passenger 1</h2>

                <div class="passenger-info">
                    <div class="info-item">
                        <span class="info-label">Name<a>*</a></span>
                        <input type="text" class="info-value" required>
                    </div>
                    <div class="info-item">
                        <span class="info-label">MyKad no.<a>**</a></span>
                         <input type="text" class="info-value" id="mykad" name="mykad" placeholder="Required for Malaysian">
                    </div>
                    <div class="info-item">
                        <span class="info-label">Passport no.<a>**</a></span>
                        <input type="text" class="info-value" id="passport" name="passport" placeholder="Required for non-Malaysian">
                    </div>
                    <div class="info-item">
                        <span class="info-label">Passport expiry date<a>**</a></span>
                        <input type="date" class="info-value" id="passportExpiry" name="passport_expiry">
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact no.<a>*</a></span>
                        <input type="text" class="info-value" required>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Gender<a>*</a></span>
                        <div class="gender">
                        <label><input type="radio" name="gender" value="male" required>Male</label>
                        <label><input type="radio" name="gender" value="female">Female</label>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ticket Type<a>*</a></span>
                        <select name="ticket-type" id="ticket-type" required>
                            <option value="" disabled selected>-- Please Select --</option>
                            <option value="">Dewasa/Adult</option>
                            <option value="">Kanak-kanak/Child</option>
                            <option value="">OKU</option>
                        </select>
                    </div>
                    <span class="required"><a>***</a>Penalty charges given for applying wrong ticket type. Discount for concession ticket will auto apply at the next page.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="passenger-info-section">
        <div class="passenger-info-container">
            <div class="passenger-info-form">
                <h2>Passenger 2</h2>

                <div class="passenger-info">
                    <div class="info-item">
                        <span class="info-label">Name<a>*</a></span>
                        <input type="text" class="info-value" required>
                    </div>
                    <div class="info-item">
                        <span class="info-label">MyKad no.<a>**</a></span>
                         <input type="text" class="info-value" id="mykad" name="mykad" placeholder="Required for Malaysian">
                    </div>
                    <div class="info-item">
                        <span class="info-label">Passport no.<a>**</a></span>
                        <input type="text" class="info-value" id="passport" name="passport" placeholder="Required for non-Malaysian">
                    </div>
                    <div class="info-item">
                        <span class="info-label">Passport expiry date<a>**</a></span>
                        <input type="date" class="info-value" id="passportExpiry" name="passport_expiry">
                    </div>
                    <div class="info-item">
                        <span class="info-label">Contact no.<a>*</a></span>
                        <input type="text" class="info-value" required>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Gender<a>*</a></span>
                        <div class="gender">
                        <label><input type="radio" name="gender" value="male" required>Male</label>
                        <label><input type="radio" name="gender" value="female">Female</label>
                        </div>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ticket Type<a>*</a></span>
                        <select name="ticket-type" id="ticket-type" required>
                            <option value="" disabled selected>-- Please Select --</option>
                            <option value="">Dewasa/Adult</option>
                            <option value="">Kanak-kanak/Child</option>
                            <option value="">OKU</option>
                        </select>
                    </div>
                    <span class="required"><a>***</a>Penalty charges given for applying wrong ticket type. Discount for concession ticket will auto apply at the next page.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="heading2">
    <h2>Confirmation</h2>
    </div>

    <div class="second-section">
        <div class="confirm-container">
            <span class="required">Please review your passenger info carefully and click confirmation button and proceed seat selection page.</span>
            <div class="required-message">
            <span class="required2"><a>*</a>Required Field</span>
            <span class="required2"><a>**</a>Either Mykad or Passport is required</span>
            </div>
        </div>

        <div class="btn-container">
            <a href="{{ route('TrainSelectionPage') }}"><button type="button" class="btn-submit">BACK</button></a>
            <a href="{{ route('selectseat') }}"><button type="submit" class="btn-submit">NEXT</button></a>
            </div>
    </div>
</form>
</section>
<script src="{{ asset('js/PassengerInfoPage.js') }}" defer></script>

@endsection