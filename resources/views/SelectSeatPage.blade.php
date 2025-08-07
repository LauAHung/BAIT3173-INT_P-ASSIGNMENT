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
                <a href="{{ route('TrainSelectionPage') }}" class="change-journey">Change Journey</a>
            </div>
            <div class="passenger-container">
                <div class="passenger-info-details">
                    @foreach ($passengers as $index => $passenger)
                    <div class="passenger-info">
                        <div class="passenger-item">
                            <span class="passenger-label">Passenger {{ $index + 1 }}</span>
                            <span class="passenger-subtext">{{ $journey['from_location'] }} > {{ $journey['to_location'] }}, (MYR {{ $journey['price'] }})</span>
                        </div>
                        <div class="details-item">
                            <span class="details-label">Ticket type</span>
                            <span class="details-value">{{ $passenger['ticket_type'] }}</span>
                            <span class="details-label">MyKad no. / passport</span>
                            <span class="details-value">{{ $passenger['mykad'] ?? $passenger['passport'] ?? 'N/A' }}</span>
                            <span class="details-label">Contact no.</span>
                            <span class="details-value">{{ $passenger['contact_no'] }}</span>
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
                    <img src="{{ asset('images/logo/' . ($journey['train_service'] == 'ETS' ? 'ets_logo.png' : ($journey['train_service'] == 'Komuter' ? 'komuter_logo.png' : 'intercity_logo.png'))) }}" alt="service-type">
                    <div class="depart-part">
                        <span class="label">DEPART</span>
                        <div>{{ $journey['train_no'] }}</div>
                        <div>{{ date('D, M d (h:i A', strtotime($journey['departure_time'])) }} - {{ date('h:i A', strtotime($journey['arrival_time'])) }})</div>
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
            <button class="proceed-payment">Proceed to Payment</button>
        </div>
    </div>
</section>

<section class="select-seat-section">
    <div class="seat-info-box">
        <div class="seat-info">
            <div class="seat-head-info">
                <h2>Select Seats</h2>
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

            <div id="coach1">
                <div class="train">
                    <div class="exit front train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>

                    <ol class="wagon train-body">
                        <li class="row row--1">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="1A" />
                                    <label for="1A">1A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="1B" />
                                    <label for="1B">1B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" disabled id="1D" />
                                    <label for="1D">Clear</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" disabled id="1C" />
                                    <label for="1C">Clear</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--2">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="2A" />
                                    <label for="2A">2A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="2B" />
                                    <label for="2B">2B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="2C" />
                                    <label for="2C">2C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="2D" />
                                    <label for="2D">2D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--3">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="3A" />
                                    <label for="3A">3A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="3B" />
                                    <label for="3B">3B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="3C" />
                                    <label for="3C">3C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="3D" />
                                    <label for="3D">3D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--4">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="4A" />
                                    <label for="4A">4A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="4B" />
                                    <label for="4B">4B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="4C" />
                                    <label for="4C">4C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="4D" />
                                    <label for="4D">4D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--5">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="5A" />
                                    <label for="5A">5A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="5B" />
                                    <label for="5B">5B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="5C" />
                                    <label for="5C">5C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="5D" />
                                    <label for="5D">5D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--6">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="6A" />
                                    <label for="6A">6A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="6B" />
                                    <label for="6B">6B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="6C" />
                                    <label for="6C">6C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="6D" />
                                    <label for="6D">6D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--7">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="7A" />
                                    <label for="7A">7A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="7B" />
                                    <label for="7B">7B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="7C" />
                                    <label for="7C">7C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="7D" />
                                    <label for="7D">7D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--8">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="8A" />
                                    <label for="8A">8A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="8B" />
                                    <label for="8B">8B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="8C" />
                                    <label for="8C">8C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="8D" />
                                    <label for="8D">8D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--9">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="9A" />
                                    <label for="9A">9A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="9B" />
                                    <label for="9B">9B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="9C" />
                                    <label for="9C">9C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="9D" />
                                    <label for="9D">9D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--10">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="10A" />
                                    <label for="10A">10A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="10B" />
                                    <label for="10B">10B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="10C" />
                                    <label for="10C">10C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="10D" />
                                    <label for="10D">10D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--11">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="11A" />
                                    <label for="11A">11A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="11B" />
                                    <label for="11B">11B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="11C" />
                                    <label for="11C">11C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="11D" />
                                    <label for="11D">11D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--12">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="12A" />
                                    <label for="12A">12A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="12B" />
                                    <label for="12B">12B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="12C" />
                                    <label for="12C">12C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="12D" />
                                    <label for="12D">12D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--13">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="13A" />
                                    <label for="13A">13A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="13B" />
                                    <label for="13B">13B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="13C" />
                                    <label for="13C">13C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="13D" />
                                    <label for="13D">13D</label>
                                </li>
                            </ol>
                        </li>
                    </ol>

                    <div class="exit back train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>

            <div id="coach2">
                <div class="train">
                    <div class="exit front train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>

                    <ol class="wagon train-body">
                        <li class="row row--1">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="14A" />
                                    <label for="14A">14A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="14B" />
                                    <label for="14B">14B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" disabled id="14D" />
                                    <label for="14D">Clear</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" disabled id="14C" />
                                    <label for="14C">Clear</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--2">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="15A" />
                                    <label for="15A">15A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="15B" />
                                    <label for="15B">15B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="15C" />
                                    <label for="15C">15C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="15D" />
                                    <label for="15D">15D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--3">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="16A" />
                                    <label for="16A">16A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="16B" />
                                    <label for="16B">16B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="16C" />
                                    <label for="16C">16C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="16D" />
                                    <label for="16D">16D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--4">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="17A" />
                                    <label for="17A">17A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="17B" />
                                    <label for="17B">17B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="17C" />
                                    <label for="17C">17C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="17D" />
                                    <label for="17D">17D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--5">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="18A" />
                                    <label for="18A">18A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="18B" />
                                    <label for="18B">18B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="18C" />
                                    <label for="18C">18C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="18D" />
                                    <label for="18D">18D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--6">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="19A" />
                                    <label for="19A">19A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="19B" />
                                    <label for="19B">19B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="19C" />
                                    <label for="19C">19C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="19D" />
                                    <label for="19D">19D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--7">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="20A" />
                                    <label for="20A">20A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="20B" />
                                    <label for="20B">20B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="20C" />
                                    <label for="20C">20C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="20D" />
                                    <label for="20D">20D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--8">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="21A" />
                                    <label for="21A">21A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="21B" />
                                    <label for="21B">21B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="21C" />
                                    <label for="21C">21C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="21D" />
                                    <label for="21D">21D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--9">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="22A" />
                                    <label for="22A">22A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="22B" />
                                    <label for="22B">22B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="22C" />
                                    <label for="22C">22C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="22D" />
                                    <label for="22D">22D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--10">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="23A" />
                                    <label for="23A">23A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="23B" />
                                    <label for="23B">23B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="23C" />
                                    <label for="23C">23C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="23D" />
                                    <label for="23D">23D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--11">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="24A" />
                                    <label for="24A">24A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="24B" />
                                    <label for="24B">24B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="24C" />
                                    <label for="24C">24C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="24D" />
                                    <label for="24D">24D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--12">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="25A" />
                                    <label for="25A">25A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="25B" />
                                    <label for="25B">25B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="25C" />
                                    <label for="25C">25C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="25D" />
                                    <label for="25D">25D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--13">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="26A" />
                                    <label for="26A">26A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="26B" />
                                    <label for="26B">26B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="26C" />
                                    <label for="26C">26C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="26D" />
                                    <label for="26D">26D</label>
                                </li>
                            </ol>
                        </li>
                    </ol>

                    <div class="exit back train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>

            <div id="coach3">
                <div class="train">
                    <div class="exit front train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>

                    <ol class="wagon train-body">
                        <li class="row row--1">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="27A" />
                                    <label for="27A">27A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="27B" />
                                    <label for="27B">27B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" disabled id="27D" />
                                    <label for="27D">Clear</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" disabled id="27C" />
                                    <label for="27C">Clear</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--2">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="28A" />
                                    <label for="28A">28A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="28B" />
                                    <label for="28B">28B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="28C" />
                                    <label for="28C">28C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="28D" />
                                    <label for="28D">28D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--3">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="29A" />
                                    <label for="29A">29A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="29B" />
                                    <label for="29B">29B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="29C" />
                                    <label for="29C">29C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="29D" />
                                    <label for="29D">29D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--4">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="30A" />
                                    <label for="30A">30A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="30B" />
                                    <label for="30B">30B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="30C" />
                                    <label for="30C">30C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="30D" />
                                    <label for="30D">30D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--5">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="31A" />
                                    <label for="31A">31A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="31B" />
                                    <label for="31B">31B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="31C" />
                                    <label for="31C">31C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="31D" />
                                    <label for="31D">31D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--6">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="32A" />
                                    <label for="32A">32A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="32B" />
                                    <label for="32B">32B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="32C" />
                                    <label for="32C">32C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="32D" />
                                    <label for="32D">32D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--7">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="33A" />
                                    <label for="33A">33A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="33B" />
                                    <label for="33B">33B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="33C" />
                                    <label for="33C">33C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="33D" />
                                    <label for="33D">33D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--8">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="34A" />
                                    <label for="34A">34A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="34B" />
                                    <label for="34B">34B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="34C" />
                                    <label for="34C">34C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="34D" />
                                    <label for="34D">34D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--9">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="35A" />
                                    <label for="35A">35A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="35B" />
                                    <label for="35B">35B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="35C" />
                                    <label for="35C">35C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="35D" />
                                    <label for="35D">35D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--10">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="36A" />
                                    <label for="36A">36A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="36B" />
                                    <label for="36B">36B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="36C" />
                                    <label for="36C">36C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="36D" />
                                    <label for="36D">36D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--11">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="37A" />
                                    <label for="37A">37A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="37B" />
                                    <label for="37B">37B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="37C" />
                                    <label for="37C">37C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="37D" />
                                    <label for="37D">37D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--12">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="38A" />
                                    <label for="38A">38A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="38B" />
                                    <label for="38B">38B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="38C" />
                                    <label for="38C">38C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="38D" />
                                    <label for="38D">38D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--13">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="39A" />
                                    <label for="39A">39A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="39B" />
                                    <label for="39B">39B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="39C" />
                                    <label for="39C">39C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="39D" />
                                    <label for="39D">39D</label>
                                </li>
                            </ol>
                        </li>
                    </ol>

                    <div class="exit back train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>

            <div id="coach4">
                <div class="train">
                    <div class="exit front train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>

                    <ol class="wagon train-body">
                        <li class="row row--1">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="40A" />
                                    <label for="40A">40A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="40B" />
                                    <label for="40B">40B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" disabled id="40D" />
                                    <label for="40D">Clear</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" disabled id="40C" />
                                    <label for="40C">Clear</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--2">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="41A" />
                                    <label for="41A">41A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="41B" />
                                    <label for="41B">41B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="41C" />
                                    <label for="41C">41C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="41D" />
                                    <label for="41D">41D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--3">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="42A" />
                                    <label for="42A">42A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="42B" />
                                    <label for="42B">42B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="42C" />
                                    <label for="42C">42C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="42D" />
                                    <label for="42D">42D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--4">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="43A" />
                                    <label for="43A">43A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="43B" />
                                    <label for="43B">43B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="43C" />
                                    <label for="43C">43C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="43D" />
                                    <label for="43D">43D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--5">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="44A" />
                                    <label for="44A">44A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="44B" />
                                    <label for="44B">44B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="44C" />
                                    <label for="44C">44C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="44D" />
                                    <label for="44D">44D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--6">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="45A" />
                                    <label for="45A">45A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="45B" />
                                    <label for="45B">45B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="45C" />
                                    <label for="45C">45C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="45D" />
                                    <label for="45D">45D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--7">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="46A" />
                                    <label for="46A">46A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="46B" />
                                    <label for="46B">46B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="46C" />
                                    <label for="46C">46C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="46D" />
                                    <label for="46D">46D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--8">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="47A" />
                                    <label for="47A">47A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="47B" />
                                    <label for="47B">47B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="47C" />
                                    <label for="47C">47C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="47D" />
                                    <label for="47D">47D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--9">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="48A" />
                                    <label for="48A">48A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="48B" />
                                    <label for="48B">48B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="48C" />
                                    <label for="48C">48C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="48D" />
                                    <label for="48D">48D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--10">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="49A" />
                                    <label for="49A">49A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="49B" />
                                    <label for="49B">49B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="49C" />
                                    <label for="49C">49C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="49D" />
                                    <label for="49D">49D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--11">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="50A" />
                                    <label for="50A">50A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="50B" />
                                    <label for="50B">50B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="50C" />
                                    <label for="50C">50C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="50D" />
                                    <label for="50D">50D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--12">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="51A" />
                                    <label for="51A">51A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="51B" />
                                    <label for="51B">51B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="51C" />
                                    <label for="51C">51C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="51D" />
                                    <label for="51D">51D</label>
                                </li>
                            </ol>
                        </li>
                        <li class="row row--13">
                            <ol class="seats">
                                <li class="seat">
                                    <input type="checkbox" id="52A" />
                                    <label for="52A">52A</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="52B" />
                                    <label for="52B">52B</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="52C" />
                                    <label for="52C">52C</label>
                                </li>
                                <li class="seat">
                                    <input type="checkbox" id="52D" />
                                    <label for="52D">52D</label>
                                </li>
                            </ol>
                        </li>
                    </ol>

                    <div class="exit back train-body">
                        <div>Toilet</div>
                        <div></div>
                        <div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const coachSelect = document.getElementById('coach-select');
    const coaches = ['coach1', 'coach2', 'coach3', 'coach4'];

    // Hide all coaches initially
    coaches.forEach(coach => {
        document.getElementById(coach).style.display = 'none';
    });

    // Show the first coach by default
    document.getElementById('coach1').style.display = 'block';

    // Add event listener for coach selection
    coachSelect.addEventListener('change', () => {
        // Hide all coaches
        coaches.forEach(coach => {
            document.getElementById(coach).style.display = 'none';
        });
        // Show the selected coach
        document.getElementById(coachSelect.value).style.display = 'block';
    });
});
</script>

@endsection