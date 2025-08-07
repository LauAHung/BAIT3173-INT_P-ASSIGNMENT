@extends('Layout.master')

@section('title', 'Passenger Info - TravelFree')

@push('styles')
    <link href="{{ asset('css/PassengerInfoPage.css') }}" rel="stylesheet">
@endpush

@section('content')
<section>
    <form action="{{ route('store.passengerinfo') }}" method="POST" data-selectseat-url="{{ route('selectseat') }}">
        @csrf
        <div class="passenger-main-layout">
            <!-- 左侧：乘客信息 -->
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
                                    <button type="button" class="toggle-btn" id="toggle-p{{ $i }}">收起</button>
                                </div>
                                <div class="passenger-info" id="p{{ $i }}-info">
                                    <div class="info-item">
                                        <span class="info-label">Name<a>*</a></span>
                                        <input type="text" class="info-value" name="passenger[{{ $i }}][name]" required>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">MyKad no.<a>**</a></span>
                                        <input type="text" class="info-value" name="passenger[{{ $i }}][mykad]" placeholder="Required for Malaysian">
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Passport no.<a>**</a></span>
                                        <input type="text" class="info-value" name="passenger[{{ $i }}][passport]" placeholder="Required for non-Malaysian">
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Passport expiry date<a>**</a></span>
                                        <input type="date" class="info-value" name="passenger[{{ $i }}][passport_expiry]">
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Contact no.<a>*</a></span>
                                        <input type="text" class="info-value" name="passenger[{{ $i }}][contact_no]" required>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Gender<a>*</a></span>
                                        <div class="gender">
                                            <label><input type="radio" name="passenger[{{ $i }}][gender]" value="male" required>Male</label>
                                            <label><input type="radio" name="passenger[{{ $i }}][gender]" value="female">Female</label>
                                        </div>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Ticket Type<a>*</a></span>
                                        <select name="passenger[{{ $i }}][ticket_type]" id="ticket-type-{{ $i }}" required>
                                            <option value="" disabled selected>-- Please Select --</option>
                                            <option value="Dewasa/Adult">Dewasa/Adult</option>
                                            <option value="Kanak-kanak/Child">Kanak-kanak/Child</option>
                                            <option value="OKU">OKU</option>
                                        </select>
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
                            <span class="required2"><a>**</a>Either Mykad or Passport is required</span>
                        </div>
                    </div>

                    <div class="btn-container">
                        <a href="{{ route('TrainSelectionPage') }}"><button type="button" class="btn-submit">BACK</button></a>
                        <a href="{{ route('selectseat') }}"><button type="submit" class="btn-submit">NEXT</button></a>
                    </div>
                </div>
            </div>
            <!-- 右侧：资讯卡片 -->
            <div class="passenger-side-info">
                <div class="side-card">
                    <div class="side-title">Total price:</div>
                    <div class="side-price">MYR 100.56</div>
                </div>
                <div class="side-card">
                    <div class="side-title">Trip Summary</div>
                    <div class="side-summary">
                        <div>Depart on Tuesday, 14 October 2025</div>
                        <div class="side-flight">
                            <span>11:45</span>
                            <span class="side-plane"><i class="fas fa-plane"></i></span>
                            <span>12:45</span>
                        </div>
                        <div>KUL → PEN</div>
                        <div>1h 0min, Non-Stop</div>
                    </div>
                </div>
                <div class="side-card">
                    <button class="side-btn main">Select Services</button>
                    <button class="side-btn">Skip to Payment</button>
                </div>
            </div>
        </div>
    </form>
</section>
<script>
function togglePassenger(pid) {
    const info = document.getElementById(pid + '-info');
    const btn = document.getElementById('toggle-' + pid);
    if (info.style.display === 'none') {
        info.style.display = '';
        btn.textContent = '收起';
    } else {
        info.style.display = 'none';
        btn.textContent = '展开';
    }
}
</script>


@endsection