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
                    @php
                        $totalPrice = 0;
                    @endphp
                    @foreach ($passengers as $index => $passenger)
                    @php
                        $ticketPrice = $journey['price'];
                        $discountText = '';
                        $discountFactor = 1.0;
                        if ($passenger['ticket_type'] === 'Kanak-kanak/Child') {
                            $discountFactor = 0.9;
                            $discountText = '<br>(10% Child Discount)';
                        } elseif ($passenger['ticket_type'] === 'OKU') {
                            $discountFactor = 0.7;
                            $discountText = '<br>(30% OKU Discount)';
                        }
                        $ticketPrice *= $discountFactor;
                        $totalPrice += $ticketPrice;

                        $ticketPrice2 = 0;
                        $discountText2 = '';
                        if (isset($journey2)) {
                            $ticketPrice2 = $journey2['price'];
                            $ticketPrice2 *= $discountFactor;
                            $discountText2 = $discountText;
                            $totalPrice += $ticketPrice2;
                        }
                    @endphp
                    <div class="passenger-info">
                        <div class="passenger-item">
                            <span class="passenger-label">Passenger {{ $index }}</span>
                            <span class="passenger-subtext">{{ $journey['from_location'] }} > {{ $journey['to_location'] }} (MYR {{ number_format($ticketPrice, 2) }}) {!! $discountText !!}</span>
                            @if (isset($journey2))
                            <span class="passenger-subtext">{{ $journey2['from_location'] }} > {{ $journey2['to_location'] }} (MYR {{ number_format($ticketPrice2, 2) }}) {!! $discountText2 !!}</span>
                            @endif
                        </div>
                        <div class="details-item">
                            <span class="details-label">Name</span>
                            <span class="details-value">{{ $passenger['name'] }}</span>
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
                            {{ date('h:i A)', strtotime($journey['arrival_time'])) }}</div>
                    </div>
                </div>
                <div class="trip-price-info">
                    @foreach ($passengers as $index => $passenger)
                    @php
                        $ticketPrice = $journey['price'];
                        $discountFactor = 1.0;
                        if ($passenger['ticket_type'] === 'Kanak-kanak/Child') {
                            $discountFactor = 0.9;
                        } elseif ($passenger['ticket_type'] === 'OKU') {
                            $discountFactor = 0.7;
                        }
                        $ticketPrice *= $discountFactor;
                    @endphp
                    <div class="price-info">
                        <div>Ticket Outbound ({{ $index }})</div>
                        <div>RM {{ number_format($ticketPrice, 2) }}</div>
                    </div>
                    @endforeach
                    @if (isset($journey2))
                    @foreach ($passengers as $index => $passenger)
                    @php
                        $ticketPrice2 = $journey2['price'];
                        $ticketPrice2 *= $discountFactor;
                    @endphp
                    <div class="price-info">
                        <div>Ticket Return ({{ $index }})</div>
                        <div>RM {{ number_format($ticketPrice2, 2) }}</div>
                    </div>
                    @endforeach
                    @endif
                    <div class="total-price-info">
                        <a>Trip Total</a>
                        <a>RM {{ number_format($totalPrice, 2) }}</a>
                    </div>
                </div>
            </div>
            <button type="button" class="proceed-payment" onclick="submitBooking()">Proceed to Payment</button>
        </div>
    </div>
</section>

@if ($journey['train_service'] === 'ETS')
<section class="select-seat-section">
    <div class="seat-info-box">
        <div class="seat-info">
            <div class="seat-head-info">
                <h2>{{ $journey['from_location'] }} > {{ $journey['to_location'] }} (Outbound)</h2><br>
                <h4>Select Seats ({{ $passengersCount }} required)</h4>
            </div>

            <div class="coach-select">
                <label for="coach-select-outbound">Coach:</label>
                <select id="coach-select-outbound" name="coach-select">
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
            <div id="{{ $coachId }}" @if($coachId=='coach1') style="display: block" @else style="display: none" @endif>
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
                                $isAvailable = !$seat || $seat->is_available == 'Y';
                                @endphp
                                <li class="seat outbound-seat">
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
</section>
@endif

@if (isset($journey2) && $journey2['train_service'] === 'ETS')
<section class="select-seat-section">
    <div class="seat-info-box">
        <div class="seat-info">
            <div class="seat-head-info">
                <h2>{{ $journey2['from_location'] }} > {{ $journey2['to_location'] }} (Return)</h2><br>
                <h4>Select Seats ({{ $passengersCount }} required)</h4>
            </div>

            <div class="coach-select">
                <label for="coach-select-return">Coach:</label>
                <select id="coach-select-return" name="coach-select">
                    <option value="return_coach1">1</option>
                    <option value="return_coach2">2</option>
                    <option value="return_coach3">3</option>
                    <option value="return_coach4">4</option>
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
            $seats2 = \App\Models\Seat::where('JourneyID', $journey2['id'])->get()->keyBy('SeatNo');
            $coaches2 = [
                'return_coach1' => range(1, 13),
                'return_coach2' => range(14, 26),
                'return_coach3' => range(27, 39),
                'return_coach4' => range(40, 52),
            ];
            @endphp

            @foreach ($coaches2 as $coachId => $rows)
            <div id="{{ $coachId }}" @if($coachId=='return_coach1') style="display: block" @else style="display: none" @endif>
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
                                    <input type="checkbox" disabled id="return_{{ $row }}{{ $seatLetter }}" />
                                    <label for="return_{{ $row }}{{ $seatLetter }}" class="disabled">Clear</label>
                                </li>
                                @else
                                @php
                                $seatNo = $row . $seatLetter;
                                $seat = $seats2->get($seatNo);
                                $isAvailable = !$seat || $seat->is_available == 'Y';
                                @endphp
                                <li class="seat return-seat">
                                    <input type="checkbox" id="return_{{ $seatNo }}" {{ $isAvailable ? '' : 'disabled' }} />
                                    <label for="return_{{ $seatNo }}">{{ $seatNo }}</label>
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
</section>
@endif

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const errorMessage = "{{ session('error') }}";
    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            html: `<p style="font-size:18px; margin:10px 0;">${errorMessage}</p>`,
            confirmButtonText: 'Got it',
            confirmButtonColor: '#d33', // Red confirm button
            width: '500px',
            padding: '30px',
            backdrop: `
                rgba(0,0,0,0.4)
                left top
                no-repeat
            `,
            customClass: {
                popup: 'custom-error-popup',
                title: 'custom-error-title',
                confirmButton: 'custom-error-button'
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const passengersCount = parseInt('{{ $passengersCount ?? 1 }}');
    let selectedSeats = [];
    let selectedSeats2 = [];

    // Outbound coach selector
    @if ($journey['train_service'] === 'ETS')
    const coachSelectOutbound = document.getElementById('coach-select-outbound');
    const coachesOutbound = ['coach1', 'coach2', 'coach3', 'coach4'];

    coachesOutbound.forEach(coach => {
        const coachElement = document.getElementById(coach);
        if (coachElement) coachElement.style.display = 'none';
    });

    const firstCoachOutbound = document.getElementById('coach1');
    if (firstCoachOutbound) firstCoachOutbound.style.display = 'block';

    coachSelectOutbound.addEventListener('change', () => {
        coachesOutbound.forEach(coach => {
            const coachElement = document.getElementById(coach);
            if (coachElement) coachElement.style.display = 'none';
        });
        const selectedCoach = document.getElementById(coachSelectOutbound.value);
        if (selectedCoach) selectedCoach.style.display = 'block';
    });

    document.querySelectorAll('.outbound-seat input[type=checkbox]:not([disabled])').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                if (selectedSeats.length >= passengersCount) {
                    checkbox.checked = false;
                    alert('You cannot select more seats than the number of passengers < ' + passengersCount + ' >.');
                } else {
                    selectedSeats.push(checkbox.id);
                }
            } else {
                selectedSeats = selectedSeats.filter(seat => seat !== checkbox.id);
            }
        });
    });
    @endif

    // Return coach selector
    @if (isset($journey2) && $journey2['train_service'] === 'ETS')
    const coachSelectReturn = document.getElementById('coach-select-return');
    const coachesReturn = ['return_coach1', 'return_coach2', 'return_coach3', 'return_coach4'];

    coachesReturn.forEach(coach => {
        const coachElement = document.getElementById(coach);
        if (coachElement) coachElement.style.display = 'none';
    });

    const firstCoachReturn = document.getElementById('return_coach1');
    if (firstCoachReturn) firstCoachReturn.style.display = 'block';

    coachSelectReturn.addEventListener('change', () => {
        coachesReturn.forEach(coach => {
            const coachElement = document.getElementById(coach);
            if (coachElement) coachElement.style.display = 'none';
        });
        const selectedCoach = document.getElementById(coachSelectReturn.value);
        if (selectedCoach) selectedCoach.style.display = 'block';
    });

    document.querySelectorAll('.return-seat input[type=checkbox]:not([disabled])').forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if (checkbox.checked) {
                if (selectedSeats2.length >= passengersCount) {
                    checkbox.checked = false;
                    alert('You cannot select more seats than the number of passengers < ' + passengersCount + ' >.');
                } else {
                    selectedSeats2.push(checkbox.id.replace('return_', ''));
                }
            } else {
                selectedSeats2 = selectedSeats2.filter(seat => seat !== checkbox.id.replace('return_', ''));
            }
        });
    });
    @endif

    window.submitBooking = function() {
        let isValid = true;

        @if ($journey['train_service'] === 'ETS')
        if (selectedSeats.length !== passengersCount) {
            alert('Please select exactly ' + passengersCount + ' seat(s) for the outbound journey.');
            isValid = false;
        }
        @endif

        @if (isset($journey2) && $journey2['train_service'] === 'ETS')
        if (selectedSeats2.length !== passengersCount) {
            alert('Please select exactly ' + passengersCount + ' seat(s) for the return journey.');
            isValid = false;
        }
        @endif

        if (!isValid) return;

        console.log('Selected outbound seats before submission:', selectedSeats);
        console.log('Selected return seats before submission:', selectedSeats2);

        // Use SweetAlert2 for a better UI confirmation
        Swal.fire({
            title: 'Confirm Booking',
            text: 'Are you sure you want to proceed to payment? This will confirm your booking.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Proceed',
            cancelButtonText: 'No, Cancel',
            customClass: {
                popup: 'custom-swal-popup',
                confirmButton: 'confirm-green',
                cancelButton: 'cancel-red'
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

                selectedSeats2.forEach(seat => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_seats2[]';
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