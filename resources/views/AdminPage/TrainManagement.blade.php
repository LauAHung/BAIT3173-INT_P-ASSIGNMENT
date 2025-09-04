@extends('Layout.master_admin')

@section('title', 'Admin - Train Management')
@section('page-title', 'Train Management')

@push('styles')
    <link href="css/AdminPage/TrainManagement.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
<div class="train-management-container">

    <div class="management-cards">
        <!-- Train Info Card -->
        <div class="management-card">
            <h3><i class="fas fa-train"></i> Train</h3>
            <form id="trainForm">
                @csrf
                <div class="form-group">
                    <label for="train_id">Train ID</label>
                    <input type="text" id="train_id" name="train_id" required>
                </div>
                <div class="form-group">
                    <label for="train_no">Train No</label>
                    <input type="text" id="train_no" name="train_no" required>
                </div>
                <div class="form-group">
                    <label for="train_service">Train Service</label>
                    <select id="train_service" name="train_service" required>
                        <option value="">Select Service</option>
                        <option value="ETS">ETS</option>
                        <option value="KTM Komuter">KTM Komuter</option>
                        <option value="Intercity">Intercity</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="seat_count">Seat Count</label>
                    <input type="number" id="seat_count" name="seat_count" min="1" required>
                </div>
                <div class="form-group">
                    <label for="is_available">Status</label>
                    <select id="is_available" name="is_available" required>
                        <option value="">Select Status</option>
                        <option value="Active">Active</option>
                        <option value="Unavailable">Unavailable</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="station_id">Station ID</label>
                    <select id="station_id" name="station_id" required>
                        <option value="">Select Station</option>
                        @foreach($stations as $station)
                            <option value="{{ $station->StationID }}">{{ $station->StationName }} ({{ $station->StationID }})</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Train</button>
            </form>
        </div>

        <!-- Train Station Card -->
        <div class="management-card">
            <h3><i class="fas fa-subway"></i> Train Station</h3>
            <form id="stationForm">
                @csrf
                <div class="form-group">
                    <label for="station_id_input">Station ID</label>
                    <input type="text" id="station_id_input" name="station_id" required>
                </div>
                <div class="form-group">
                    <label for="station_name">Station Name</label>
                    <input type="text" id="station_name" name="station_name" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required>
                </div>
                <div class="form-group">
                    <label for="is_active">Status</label>
                    <select id="is_active" name="is_active" required>
                        <option value="">Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Station</button>
            </form>
        </div>

        <!-- Journey Card -->
        <div class="management-card">
            <h3><i class="fas fa-route"></i> Journey</h3>
            <form id="journeyForm">
                @csrf
                <div class="form-group">
                    <label for="journey_id">Journey ID</label>
                    <input type="text" id="journey_id" name="journey_id" required>
                </div>
                <div class="form-group">
                    <label for="journey_train_id">Train ID</label>
                    <select id="journey_train_id" name="train_id" required>
                        <option value="">Select Train</option>
                        @foreach($trains as $train)
                            <option value="{{ $train->TrainID }}">{{ $train->TrainNo }} - {{ $train->TrainService }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="from_location">From Station</label>
                    <select id="from_location" name="from_location" required>
                        <option value="">Select From Station</option>
                        @foreach($stations as $station)
                            <option value="{{ $station->StationName }}">{{ $station->StationName }} ({{ $station->Location }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="to_location">To Station</label>
                    <select id="to_location" name="to_location" required>
                        <option value="">Select To Station</option>
                        @foreach($stations as $station)
                            <option value="{{ $station->StationName }}">{{ $station->StationName }} ({{ $station->Location }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="departure_time">Departure Time</label>
                    <input type="datetime-local" id="departure_time" name="departure_time" required>
                </div>
                <div class="form-group">
                    <label for="arrival_time">Arrival Time</label>
                    <input type="datetime-local" id="arrival_time" name="arrival_time" required>
                </div>
                <div class="form-group">
                    <label for="seat_available">Seat Available</label>
                    <input type="number" id="seat_available" name="seat_available" min="0" required>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="">Select Status</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Delayed">Delayed</option>
                        <option value="Canceled">Canceled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Journey</button>
            </form>
        </div>
    </div>

    <div class="data-list-section">
        <div class="data-tabs">
            <button class="tab-btn active" onclick="showTab('train')">Train</button>
            <button class="tab-btn" onclick="showTab('station')">Train Station</button>
            <button class="tab-btn" onclick="showTab('journey')">Journey</button>
        </div>
        <div class="tab-content" id="tab-train">
            <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Train ID</th>
                         <th>Train No</th>
                         <th>Service</th>
                         <th>Seat Count</th>
                         <th>Status</th>
                        <th>Station</th>
                         <th>Actions</th>
                    </tr>
                </thead>
                                 <tbody id="trainTableBody">
                     @foreach($trains as $train)
                     <tr>
                         <td><span class="id-value">{{ $train->TrainID }}</span></td>
                         <td>{{ $train->TrainNo }}</td>
                         <td>{{ $train->TrainService }}</td>
                         <td>{{ $train->SeatCount }}</td>
                         <td>
                             <span class="status-badge {{ strtolower($train->Is_available) === 'active' ? 'status-active' : 'status-inactive' }}">
                                 {{ $train->Is_available }}
                             </span>
                         </td>
                         <td>{{ $train->station ? $train->station->StationName : 'N/A' }}</td>
                         <td>
                             <button class="btn-edit" onclick="editTrain('{{ $train->TrainID }}', '{{ $train->TrainNo }}', '{{ $train->TrainService }}', '{{ $train->SeatCount }}', '{{ $train->Is_available }}', '{{ $train->StationID }}')">
                                 <i class="fas fa-edit"></i> Edit
                             </button>
                         </td>
                    </tr>
                     @endforeach
                </tbody>
            </table>
            </div>
        </div>
        <div class="tab-content" id="tab-station" style="display:none;">
            <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Station ID</th>
                         <th>Station Name</th>
                         <th>Location</th>
                         <th>Status</th>
                         <th>Actions</th>
                    </tr>
                </thead>
                                 <tbody id="stationTableBody">
                     @foreach($stations as $station)
                     <tr>
                         <td><span class="id-value">{{ $station->StationID }}</span></td>
                         <td>{{ $station->StationName }}</td>
                         <td>{{ $station->Location }}</td>
                         <td>
                             <span class="status-badge {{ $station->Is_active ? 'status-active' : 'status-inactive' }}">
                                 {{ $station->Is_active ? 'Active' : 'Inactive' }}
                             </span>
                         </td>
                         <td>
                             <button class="btn-edit" onclick="editStation('{{ $station->StationID }}', '{{ $station->StationName }}', '{{ $station->Location }}', '{{ $station->Is_active }}')">
                                 <i class="fas fa-edit"></i> Edit
                             </button>
                         </td>
                    </tr>
                     @endforeach
                </tbody>
            </table>
            </div>
        </div>
        <div class="tab-content" id="tab-journey" style="display:none;">
            <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Journey ID</th>
                         <th>Train</th>
                         <th>From</th>
                         <th>To</th>
                         <th>Departure</th>
                         <th>Arrival</th>
                         <th>Seats</th>
                         <th>Price</th>
                        <th>Status</th>
                         <th>Actions</th>
                    </tr>
                </thead>
                                 <tbody id="journeyTableBody">
                     @foreach($journeys as $journey)
                     <tr>
                         <td><span class="id-value">{{ $journey->JourneyID }}</span></td>
                         <td>{{ $journey->train ? $journey->train->TrainNo : 'N/A' }}</td>
                         <td>{{ $journey->FromLocation }}</td>
                         <td>{{ $journey->ToLocation }}</td>
                         <td><span class="time-value">{{ \Carbon\Carbon::parse($journey->DepartureTime)->format('Y-m-d H:i') }}</span></td>
                         <td><span class="time-value">{{ \Carbon\Carbon::parse($journey->ArrivalTime)->format('Y-m-d H:i') }}</span></td>
                         <td>{{ $journey->SeatAvailable }}</td>
                         <td><span class="price-value">RM {{ number_format($journey->Price, 2) }}</span></td>
                         <td>
                             <span class="status-badge 
                                 @if($journey->Status === 'Scheduled') status-scheduled
                                 @elseif($journey->Status === 'Delayed') status-delayed
                                 @elseif($journey->Status === 'Canceled') status-canceled
                                 @endif">
                                 {{ $journey->Status }}
                             </span>
                         </td>
                         <td>
                             <button class="btn-edit" onclick="editJourney('{{ $journey->JourneyID }}', '{{ $journey->TrainID }}', '{{ $journey->FromLocation }}', '{{ $journey->ToLocation }}', '{{ \Carbon\Carbon::parse($journey->DepartureTime)->format('Y-m-d H:i') }}', '{{ \Carbon\Carbon::parse($journey->ArrivalTime)->format('Y-m-d H:i') }}', '{{ $journey->SeatAvailable }}', '{{ $journey->Price }}', '{{ $journey->Status }}')">
                                 <i class="fas fa-edit"></i> Edit
                             </button>
                         </td>
                    </tr>
                     @endforeach
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Train Modal -->
<div id="editTrainModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editTrainModal')">&times;</span>
        <h3><i class="fas fa-train"></i> Edit Train</h3>
        <form id="editTrainForm">
            @csrf
            <input type="hidden" id="edit_train_id" name="train_id">
            <div class="form-group">
                <label for="edit_train_no">Train No</label>
                <input type="text" id="edit_train_no" name="train_no" required>
            </div>
            <div class="form-group">
                <label for="edit_train_service">Train Service</label>
                <select id="edit_train_service" name="train_service" required>
                    <option value="">Select Service</option>
                    <option value="ETS">ETS</option>
                    <option value="KTM Komuter">KTM Komuter</option>
                    <option value="Intercity">Intercity</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_seat_count">Seat Count</label>
                <input type="number" id="edit_seat_count" name="seat_count" min="1" required>
            </div>
            <div class="form-group">
                <label for="edit_is_available">Status</label>
                <select id="edit_is_available" name="is_available" required>
                    <option value="">Select Status</option>
                    <option value="Active">Active</option>
                    <option value="Unavailable">Unavailable</option>
                </select>
            </div>
            <div class="form-group">
                <label for="edit_station_id">Station ID</label>
                <select id="edit_station_id" name="station_id" required>
                    <option value="">Select Station</option>
                    @foreach($stations as $station)
                        <option value="{{ $station->StationID }}">{{ $station->StationName }} ({{ $station->StationID }})</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Train</button>
        </form>
    </div>
</div>

<!-- Edit Station Modal -->
<div id="editStationModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editStationModal')">&times;</span>
        <h3><i class="fas fa-subway"></i> Edit Station</h3>
        <form id="editStationForm">
            @csrf
            <input type="hidden" id="edit_station_id_hidden" name="station_id">
            <div class="form-group">
                <label for="edit_station_name">Station Name</label>
                <input type="text" id="edit_station_name" name="station_name" required>
            </div>
            <div class="form-group">
                <label for="edit_location">Location</label>
                <input type="text" id="edit_location" name="location" required>
            </div>
            <div class="form-group">
                <label for="edit_is_active">Status</label>
                <select id="edit_is_active" name="is_active" required>
                    <option value="">Select Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Station</button>
        </form>
    </div>
</div>

<!-- Edit Journey Modal -->
<div id="editJourneyModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editJourneyModal')">&times;</span>
        <h3><i class="fas fa-route"></i> Edit Journey</h3>
        <form id="editJourneyForm">
            @csrf
            <input type="hidden" id="edit_journey_id" name="journey_id">
            <div class="form-group">
                <label for="edit_journey_train_id">Train ID</label>
                <select id="edit_journey_train_id" name="train_id" required>
                    <option value="">Select Train</option>
                    @foreach($trains as $train)
                        <option value="{{ $train->TrainID }}">{{ $train->TrainNo }} - {{ $train->TrainService }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="edit_from_location">From Station</label>
                <select id="edit_from_location" name="from_location" required>
                    <option value="">Select From Station</option>
                    @foreach($stations as $station)
                        <option value="{{ $station->StationName }}">{{ $station->StationName }} ({{ $station->Location }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="edit_to_location">To Station</label>
                <select id="edit_to_location" name="to_location" required>
                    <option value="">Select To Station</option>
                    @foreach($stations as $station)
                        <option value="{{ $station->StationName }}">{{ $station->StationName }} ({{ $station->Location }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="edit_departure_time">Departure Time</label>
                <input type="datetime-local" id="edit_departure_time" name="departure_time" required>
            </div>
            <div class="form-group">
                <label for="edit_arrival_time">Arrival Time</label>
                <input type="datetime-local" id="edit_arrival_time" name="arrival_time" required>
            </div>
            <div class="form-group">
                <label for="edit_seat_available">Seat Available</label>
                <input type="number" id="edit_seat_available" name="seat_available" min="0" required>
            </div>
            <div class="form-group">
                <label for="edit_price">Price</label>
                <input type="number" id="edit_price" name="price" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label for="edit_status">Status</label>
                <select id="edit_status" name="status" required>
                    <option value="">Select Status</option>
                    <option value="Scheduled">Scheduled</option>
                    <option value="Delayed">Delayed</option>
                    <option value="Canceled">Canceled</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update Journey</button>
        </form>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="message-container" style="display: none;" class="message-container">
    <div id="message-content" class="message-content">
        <span id="message-text"></span>
        <button onclick="closeMessage()" class="close-btn">&times;</button>
    </div>
</div>

<script>
function showTab(tab) {
    document.getElementById('tab-train').style.display = (tab === 'train') ? 'block' : 'none';
    document.getElementById('tab-station').style.display = (tab === 'station') ? 'block' : 'none';
    document.getElementById('tab-journey').style.display = (tab === 'journey') ? 'block' : 'none';
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector('.tab-btn[onclick*="' + tab + '"]').classList.add('active');
}

// Unified message helpers
function showMessage(message, type) {
    const container = document.getElementById('message-container');
    const messageText = document.getElementById('message-text');
    const messageContent = document.getElementById('message-content');

    messageText.textContent = message;
    messageContent.className = `message-content message-${type}`;
    container.style.display = 'block';

    setTimeout(() => {
        container.style.display = 'none';
    }, 5000);
}

function closeMessage() {
    document.getElementById('message-container').style.display = 'none';
}

// Train form submission
document.getElementById('trainForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    fetch('/admin/train-management/train', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showMessage(data.message || 'Saved successfully.', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('Error: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred while saving the train. Please check the console for details.', 'error');
    });
});

// Station form submission
document.getElementById('stationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/train-management/station', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showMessage(data.message || 'Saved successfully.', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('Error: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred while saving the station. Please check the console for details.', 'error');
    });
});

// Journey form submission
document.getElementById('journeyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fromLocation = document.getElementById('from_location').value;
    const toLocation = document.getElementById('to_location').value;
    
    if (fromLocation === toLocation) {
        showMessage('From Station and To Station cannot be the same!', 'error');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('/admin/train-management/journey', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showMessage(data.message || 'Saved successfully.', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('Error: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred while saving the journey. Please check the console for details.', 'error');
    });
});

// Prevent selecting same station for from and to
document.getElementById('from_location').addEventListener('change', function() {
    const fromLocation = this.value;
    const toLocation = document.getElementById('to_location');
    
    // Disable the same option in to_location dropdown
    Array.from(toLocation.options).forEach(option => {
        if (option.value === fromLocation) {
            option.disabled = true;
        } else {
            option.disabled = false;
        }
    });
    
    // If current to_location is the same as from_location, clear it
    if (toLocation.value === fromLocation) {
        toLocation.value = '';
    }
});

document.getElementById('to_location').addEventListener('change', function() {
    const toLocation = this.value;
    const fromLocation = document.getElementById('from_location');
    
    // Disable the same option in from_location dropdown
    Array.from(fromLocation.options).forEach(option => {
        if (option.value === toLocation) {
            option.disabled = true;
        } else {
            option.disabled = false;
        }
    });
    
    // If current from_location is the same as to_location, clear it
    if (fromLocation.value === toLocation) {
        fromLocation.value = '';
    }
});

// Edit functions
function editTrain(trainId, trainNo, trainService, seatCount, isAvailable, stationId) {
    document.getElementById('edit_train_id').value = trainId;
    document.getElementById('edit_train_no').value = trainNo;
    document.getElementById('edit_train_service').value = trainService;
    document.getElementById('edit_seat_count').value = seatCount;
    document.getElementById('edit_is_available').value = isAvailable;
    document.getElementById('edit_station_id').value = stationId;
    document.getElementById('editTrainModal').style.display = 'block';
}

function editStation(stationId, stationName, location, isActive) {
    document.getElementById('edit_station_id_hidden').value = stationId;
    document.getElementById('edit_station_name').value = stationName;
    document.getElementById('edit_location').value = location;
    document.getElementById('edit_is_active').value = isActive;
    document.getElementById('editStationModal').style.display = 'block';
}

function editJourney(journeyId, trainId, fromLocation, toLocation, departureTime, arrivalTime, seatAvailable, price, status) {
    document.getElementById('edit_journey_id').value = journeyId;
    document.getElementById('edit_journey_train_id').value = trainId;
    document.getElementById('edit_from_location').value = fromLocation;
    document.getElementById('edit_to_location').value = toLocation;
    document.getElementById('edit_departure_time').value = departureTime;
    document.getElementById('edit_arrival_time').value = arrivalTime;
    document.getElementById('edit_seat_available').value = seatAvailable;
    document.getElementById('edit_price').value = price;
    document.getElementById('edit_status').value = status;
    document.getElementById('editJourneyModal').style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Edit form submissions
document.getElementById('editTrainForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/train-management/train/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message || 'Updated successfully.', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('Error: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred while updating the train.', 'error');
    });
});

document.getElementById('editStationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/train-management/station/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message || 'Updated successfully.', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('Error: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred while updating the station.', 'error');
    });
});

document.getElementById('editJourneyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const fromLocation = document.getElementById('edit_from_location').value;
    const toLocation = document.getElementById('edit_to_location').value;
    
    if (fromLocation === toLocation) {
        showMessage('From Station and To Station cannot be the same!', 'error');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('/admin/train-management/journey/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message || 'Updated successfully.', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showMessage('Error: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(error => {
        showMessage('An error occurred while updating the journey.', 'error');
    });
});
</script>
@endsection