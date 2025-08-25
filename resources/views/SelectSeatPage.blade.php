@extends('Layout.master')

@section('title', 'SelectSeat - TravelFree')

@push('styles')
<link href="css/SelectSeatPage.css" rel="stylesheet">
@endpush

@section('content')

<section class="ticket-info-section">
    <div class="passenger-info-box">
        <div class="passenger-info-container">
            <div class="passenger-head-info">
                <h2>Passenger Details</h2>
                <a href="{{ route('passengerinfo', ['passengers' => request()->input('passengers', 1), 'journey_id' => $journey['id']]) }}"
                    class="change-journey">Change Journey</a>
            </div>
            <div class="passenger-container">
                <div class="passenger-info-details">
                    @foreach ($passengers as $index => $passenger)
                    <div class="passenger-info">
                        <div class="passenger-item">
                            <span class="passenger-label">Passenger {{ $index + 1 }}</span>
                            <span class="passenger-subtext">{{ $journey['from_location'] }} >
                                {{ $journey['to_location'] }}, (MYR {{ $journey['price'] }})</span>
                        </div>
                        <div class="details-item">
                            <span class="details-label">Ticket type</span>
                            <span class="details-value">{{ $passenger['ticket_type'] }}</span>
                            <span class="details-label">MyKad no. / passport</span>
                            <span class="details-value">
                                @if (!empty($passenger['mykad']) || !empty($passenger['passport']))
                                {{ str_repeat('*', max(0, strlen($passenger['mykad'] ?? $passenger['passport']) - 4)) . substr($passenger['mykad'] ?? $passenger['passport'], -4) }}
                                @else
                                N/A
                                @endif
                            </span>
                            <span class="details-label">Contact no.</span>
                            <span class="details-value">
                                @if (!empty($passenger['contact_no']))
                                {{ str_repeat('*', max(0, strlen($passenger['contact_no']) - 4)) . substr($passenger['contact_no'], -4) }}
                                @else
                                N/A
                                @endif
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="ticket-details-box">
        <div class="trip-info">
            <h2>Price Details</h2>
            <div class="trip-details">
                <div class="trip-details-col">
                    <img src="{{ asset('images/logo/' . ($journey['train_service'] == 'ETS' ? 'ets_logo.png' : ($journey['train_service'] == 'Komuter' ? 'komuter_logo.png' : 'intercity_logo.png'))) }}"
                        alt="service-type">
                    <div class="depart-part">
                        <span class="label">DEPART</span>
                        <div>{{ $journey['train_no'] }}</div>
                        <div>{{ date('D, M d (h:i A', strtotime($journey['departure_time'])) }} -
                            {{ date('h:i A', strtotime($journey['arrival_time'])) }})</div>
                    </div>
                </div>
                <div class="trip-price-info">
                    <div class="price-info">
                        <div>Total ticket ({{ $passengersCount }})</div>
                        <div>RM {{ $journey['price'] * $passengersCount }}</div>
                    </div>
                    <div class="total-price-info">
                        <a>Trip Total</a>
                        <a>RM {{ $journey['price'] * $passengersCount }}</a>
                    </div>
                </div>
            </div>
            <button type="button" class="proceed-payment" onclick="submitBooking()">Proceed to Payment</button>
        </div>
    </div>
</section>

<section class="select-seat-section">
    <div class="seat-info-box">
        <div class="seat-info">
            <div class="seat-head-info">
                <h2>Select Seats ({{ $passengersCount }} required)</h2>
            </div>

            <div class="coach-select">
                <label for="coach-select">Coach:</label>
                <select id="coach-select" name="coach-select">
                    <option value="coach1">1</option>
                    <option value="coach2">2</option>
                    <option value="coach3">3</option>
                    <option value="coach4">4</option>
                </select>
            </div>

            <div class="seat-status">
                <div class="status-item">
                    <span class="status-color available"></span>
                    <span class="status-label">Available</span>
                </div>
                <div class="status-item">
                    <span class="status-color selected"></span>
                    <span class="status-label">Selected</span>
                </div>
                <div class="status-item">
                    <span class="status-color unavailable"></span>
                    <span class="status-label">Unavailable</span>
                </div>
            </div>

            @php
            $seats = \App\Models\Seat::where('JourneyID', $journey['id'])->get()->keyBy('SeatNo');
            $coaches = [
            'coach1' => range(1, 13),
            'coach2' => range(14, 26),
            'coach3' => range(27, 39),
            'coach4' => range(40, 52),
            ];
            @endphp

            @foreach ($coaches as $coachId => $rows)
            <div id="{{ $coachId }}" @if($coachId=='coach1' ) style="display: block" @else style="display: none" @endif>
                <div class="train">
                    <div class="exit front train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>

                    <ol class="wagon train-body">
                        @foreach ($rows as $row)
                        <li class="row row--{{ $row }}">
                            <ol class="seats">
                                @foreach (['A', 'B', 'C', 'D'] as $seatLetter)
                                @if (in_array($row, [1, 14, 27, 40]) && in_array($seatLetter, ['C', 'D']))
                                <li class="seat">
                                    <input type="checkbox" disabled id="{{ $row }}{{ $seatLetter }}" />
                                    <label for="{{ $row }}{{ $seatLetter }}" class="disabled">Clear</label>
                                </li>
                                @else
                                @php
                                $seatNo = $row . $seatLetter;
                                $seat = $seats->get($seatNo);
                                $isAvailable = $seat && $seat->is_available == 'Y';
                                @endphp
                                <li class="seat">
                                    <input type="checkbox" id="{{ $seatNo }}" {{ $isAvailable ? '' : 'disabled' }} />
                                    <label for="{{ $seatNo }}">{{ $seatNo }}</label>
                                </li>
                                @endif
                                @endforeach
                            </ol>
                        </li>
                        @endforeach
                    </ol>

                    <div class="exit back train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @if (session('error'))
    <div class="error-message" style="color: red; margin: 10px 0;">
        {{ session('error') }}
    </div>
    @endif
    @if (session('success'))
    <div class="success-message" style="color: green; margin: 10px 0;">
        {{ session('success') }}
    </div>
    @endif
</section>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const coachSelect = document.getElementById('coach-select');
    const coaches = ['coach1', 'coach2', 'coach3', 'coach4'];
    const passengersCount = parseInt('{{ $passengersCount ?? 1 }}');
    let selectedSeats = [];

    coaches.forEach(coach => {
        const coachElement = document.getElementById(coach);
        if (coachElement) coachElement.style.display = 'none';
    });

    const firstCoach = document.getElementById('coach1');
    if (firstCoach) firstCoach.style.display = 'block';

    coachSelect.addEventListener('change', () => {
        coaches.forEach(coach => {
            const coachElement = document.getElementById(coach);
            if (coachElement) coachElement.style.display = 'none';
        });
        const selectedCoach = document.getElementById(coachSelect.value);
        if (selectedCoach) selectedCoach.style.display = 'block';
    });

    document.querySelectorAll('input[type=checkbox]:not([disabled])').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                if (selectedSeats.length >= passengersCount) {
                    checkbox.checked = false;
                    alert('You cannot select more seats than the number of passengers < ' +
                        passengersCount + ' >.');
                } else {
                    selectedSeats.push(checkbox.id);
                }
            } else {
                selectedSeats = selectedSeats.filter(seat => seat !== checkbox.id);
            }
        });
    });

    window.submitBooking = function() {
        const passengersCount = parseInt('{{ $passengersCount ?? 1 }}');
        // Use the globally tracked selectedSeats instead of recollecting
        if (selectedSeats.length !== passengersCount) {
            alert('Please select exactly ' + passengersCount + ' seat(s).');
            return;
        }

        console.log('Selected seats before submission:', selectedSeats);

        // Use SweetAlert2 for a better UI confirmation
        Swal.fire({
            title: 'Confirm Booking',
            text: 'Are you sure you want to proceed to payment? This will confirm your booking.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Proceed',
            cancelButtonText: 'No, Cancel',
            customClass: {
                popup: 'custom-swal-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("booking.store") }}';
                form.style.display = 'none';

                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                    'content') || '{{ csrf_token() }}';
                form.appendChild(csrf);

                selectedSeats.forEach(seat => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_seats[]';
                    input.value = seat;
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                try {
                    form.submit();
                } catch (e) {
                    console.error('Form submission failed:', e);
                }
            }
        });
    };
});
</script>

@endsection