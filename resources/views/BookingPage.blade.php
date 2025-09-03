@extends('Layout.master')

@section('title', 'TrainBooking - TravelFree')

@push('styles')
<link href="{{ asset('css/BookingPage.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endpush

@section('content')

<section>
    <div class="booking-main-layout">
        <!-- Sidebar -->
        <aside class="booking-sidebar">
            <ul>
                <li class="sidebar-tab active" id="ongoing-tab">Ongoing</li>
                <li class="sidebar-tab" id="past-tab">Past Trip</li>
                <li class="sidebar-tab" id="refunded-tab">Refunded</li> 
            </ul>
        </aside>
        <!-- Main Content -->
        <div class="booking-content">
            <!-- Ongoing Booking -->
            <div class="on-going-booking" id="ongoing-content">
                <div class="booking-heading">
                    <h2>On-going Booking Ticket</h2>
                </div>
                <div class="booking-item-container">
                    @php
                    $ongoingBookings = $ongoingBookings ?? collect();
                    @endphp
                    @forelse ($ongoingBookings as $booking)
                    <div class="booking-item">
                        <div class="booking-flex-row">
                            <div class="booking-col booking-col-left">
                                <img src="{{ asset('images/logo/' . ($booking->Journey->Train->TrainService ?? 'default_logo.png') . '_logo.png') }}"
                                    alt="service_type" class="booking-logo">
                                <div class="train-number">{{ $booking->Journey->Train->TrainNo ?? 'Unknown' }}</div>
                                <div class="booking-id">Booking ID: {{ $booking->BookingID }}</div>
                            </div>
                            <div class="booking-col booking-col-middle">
                                <div class="route-row dashed-line">
                                    <span class="station">{{ $booking->Journey->FromLocation ?? 'Unknown'}}</span>
                                    <span class="train-icon center-icon">
                                        <i class="fas fa-train"></i>
                                    </span>
                                    <span class="station">{{ $booking->Journey->ToLocation ?? 'Unknown'}}</span>
                                </div>
                                <div class="time-row dashed-line">
                                    <span
                                        class="time">{{ date('g:i A', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                    <span class="train-icon center-icon">
                                        <i class="fas fa-train"></i>
                                    </span>
                                    <span
                                        class="time">{{ date('g:i A', strtotime($booking->Journey->ArrivalTime ?? 'Unknown')) }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="date">Date:
                                        {{ date('d F Y', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                </div>
                                <div class="status-row">
                                    <span class="status">Status: {{ $booking->Status }}</span>
                                </div>
                            </div>
                            <div class="booking-col booking-col-right booking-col-height">
                                @if ($booking->showViewQR)
                                    <a href="{{ route('bookingdetail', ['bookingId' => $booking->BookingID]) }}">
                                        <button type="button" class="btn-view">View QR Code</button>
                                    </a>
                                @endif
                                @if ($booking->showRefund)
                                    <a href="{{ route('refund.page', ['bookingId' => $booking->BookingID]) }}">
                                        <button type="button" class="btn-refund">Refund</button>
                                    </a>
                                @endif
                                @if ($booking->showProceedPayment)
                                <a href="{{ route('proceedPayment', ['bookingId' => $booking->BookingID]) }}">
                                    <button type="button" class="btn-payment">Proceed Payment</button>
                                </a>
                                @endif

                                @if ($booking->showCancel)
                                    <button type="button" class="btn-cancel" onclick="confirmCancel('{{ $booking->BookingID }}')">Cancel</button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="booking-item">
                        <p>No ongoing bookings found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            <!-- Past Trip -->
            <div class="booking-history" id="past-content" style="display:none;">
                <div class="booking-heading">
                    <h2>Booking History</h2>
                </div>
                <div class="booking-item-container">
                    @php
                    $pastBookings = $pastBookings ?? collect();
                    @endphp
                    @forelse ($pastBookings as $booking)
                    <div class="booking-item">
                        <div class="booking-flex-row">
                            <div class="booking-col booking-col-left">
                                <img src="{{ asset('images/logo/' . ($booking->Journey->Train->TrainService ?? 'default_logo.png') . '_logo.png') }}"
                                    alt="service_type" class="booking-logo">
                                <div class="train-number">{{ $booking->Journey->Train->TrainNo ?? 'Unknown' }}</div>
                                <div class="booking-id">Booking ID: {{ $booking->BookingID }}</div>
                            </div>
                            <div class="booking-col booking-col-middle">
                                <div class="route-row dashed-line">
                                    <span class="station">{{ $booking->Journey->FromLocation ?? 'Unknown' }}</span>
                                    <span class="train-icon center-icon">
                                        <i class="fas fa-train"></i>
                                    </span>
                                    <span class="station">{{ $booking->Journey->ToLocation ?? 'Unknown' }}</span>
                                </div>
                                <div class="time-row dashed-line">
                                    <span
                                        class="time">{{ date('g:i A', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                    <span class="train-icon center-icon">
                                        <i class="fas fa-train"></i>
                                    </span>
                                    <span
                                        class="time">{{ date('g:i A', strtotime($booking->Journey->ArrivalTime ?? 'Unknown')) }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="date">Date:
                                        {{ date('d F Y', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                </div>
                                <div class="status-row">
                                    <span class="status">Status: {{ $booking->Status }}</span>
                                </div>
                            </div>
                            <div class="booking-col booking-col-right">
                                @if ($booking->showViewQR)
                                    <a href="{{ route('bookingdetail', ['bookingId' => $booking->BookingID]) }}">
                                        <button type="button" class="btn-view">View QR Code</button>
                                    </a>
                                @endif
                                @if ($booking->showRateTrip)
                                    <a href="{{ route('rateTrip', ['bookingId' => $booking->BookingID]) }}">
                                        <button type="button" class="btn-rate">Rate Trip</button>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="booking-item">
                        <p>No past bookings found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
              <!-- Refunded Section -->
            <div class="refunded-booking" id="refunded-content" style="display:none;">
                <div class="booking-heading">
                    <h2>Refunded Bookings</h2>
                </div>
                <div class="booking-item-container">
                    @php
                    $refundedBookings = $refundedBookings ?? collect();
                    @endphp
                    @forelse ($refundedBookings as $booking)
                    <div class="booking-item">
                        <div class="booking-flex-row">
                            <div class="booking-col booking-col-left">
                                <img src="{{ asset('images/logo/' . ($booking->Journey->Train->TrainService ?? 'default_logo.png') . '_logo.png') }}"
                                    alt="service_type" class="booking-logo">
                                <div class="train-number">{{ $booking->Journey->Train->TrainNo ?? 'Unknown' }}</div>
                                <div class="booking-id">Booking ID: {{ $booking->BookingID }}</div>
                            </div>
                            <div class="booking-col booking-col-middle">
                                <div class="route-row dashed-line">
                                    <span class="station">{{ $booking->Journey->FromLocation ?? 'Unknown' }}</span>
                                    <span class="train-icon center-icon">
                                        <i class="fas fa-train"></i>
                                    </span>
                                    <span class="station">{{ $booking->Journey->ToLocation ?? 'Unknown' }}</span>
                                </div>
                                <div class="time-row dashed-line">
                                    <span class="time">{{ date('g:i A', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                    <span class="train-icon center-icon">
                                        <i class="fas fa-train"></i>
                                    </span>
                                    <span class="time">{{ date('g:i A', strtotime($booking->Journey->ArrivalTime ?? 'Unknown')) }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="date">Date:
                                        {{ date('d F Y', strtotime($booking->Journey->DepartureTime ?? 'Unknown')) }}</span>
                                </div>
                                <div class="status-row">
                                    <span class="status refunded">Status: {{ $booking->Status }}</span>
                                </div>
                            </div>
                            <!-- No buttons for refunded bookings -->
                        </div>
                    </div>
                    @empty
                    <div class="booking-item">
                        <p>No refunded bookings found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/BookingPage.js') }}" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.sidebar-tab');
    const contents = document.querySelectorAll('.booking-content > div');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.style.display = 'none');
            this.classList.add('active');
            const contentId = this.id.replace('-tab', '-content');
            document.getElementById(contentId).style.display = 'block';
        });
    });
});

function confirmCancel(bookingId) {
    console.log('Confirming cancel for Booking ID:', bookingId); // Debug log
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to cancel this booking? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep it',
        customClass: {
            popup: 'custom-swal-popup'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('Cancellation confirmed for Booking ID:', bookingId); // Debug log
            // Create a form for POST request to handle CSRF
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '{{ route("cancel", ":bookingId") }}'.replace(':bookingId', bookingId);
            form.style.display = 'none';

            // Add CSRF token
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);

            document.body.appendChild(form);
            console.log('Submitting form to:', form.action); // Debug log
            try {
                form.submit();
            } catch (e) {
                console.error('Form submission failed:', e);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to submit cancellation request. Please try again.',
                    confirmButtonColor: '#d33'
                });
            }
        }
    });
}
</script>

@endsection