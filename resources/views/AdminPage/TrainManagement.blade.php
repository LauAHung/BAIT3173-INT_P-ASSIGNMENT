@extends('Layout.master_admin')

@section('title', 'Admin - Train Management')

@push('styles')
    <link href="css/AdminPage/TrainManagement.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
<div class="train-management-container">
    <h2 class="page-title">Train Management</h2>

    <div class="management-cards">
        <!-- Train Info Card -->
        <div class="management-card">
            <h3><i class="fas fa-train"></i> Train</h3>
            <form>
                <div class="form-group">
                    <label for="train_id">Train ID</label>
                    <input type="text" id="train_id" name="train_id">
                </div>
                <div class="form-group">
                    <label for="train_type">Train Type</label>
                    <select id="train_type" name="train_type">
                        <option>ETS</option>
                        <option>KTM Komuter</option>
                        <option>Intercity</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="capacity">Capacity</label>
                    <input type="number" id="capacity" name="capacity">
                </div>
                <div class="form-group">
                    <label for="train_station">Train Station</label>
                    <input type="text" id="train_station" name="train_station">
                </div>
                <button type="submit" class="btn-primary">Save Train</button>
            </form>
        </div>

        <!-- Train Station Card -->
        <div class="management-card">
            <h3><i class="fas fa-subway"></i> Train Station</h3>
            <form>
                <div class="form-group">
                    <label for="station_id">TrainStation ID</label>
                    <input type="text" id="station_id" name="station_id">
                </div>
                <div class="form-group">
                    <label for="station_name">Station Name</label>
                    <input type="text" id="station_name" name="station_name">
                </div>
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city">
                </div>
                <div class="form-group">
                    <label for="state">State</label>
                    <input type="text" id="state" name="state">
                </div>
                <button type="submit" class="btn-primary">Save Station</button>
            </form>
        </div>

        <!-- Journey Card -->
        <div class="management-card">
            <h3><i class="fas fa-route"></i> Journey</h3>
            <form>
                <div class="form-group">
                    <label for="journey_id">Journey ID</label>
                    <input type="text" id="journey_id" name="journey_id">
                </div>
                <div class="form-group">
                    <label for="depart_datetime">Depart DateTime</label>
                    <input type="datetime-local" id="depart_datetime" name="depart_datetime">
                </div>
                <div class="form-group">
                    <label for="arrive_datetime">Arrive DateTime</label>
                    <input type="datetime-local" id="arrive_datetime" name="arrive_datetime">
                </div>
                <div class="form-group">
                    <label for="depart_city">Depart City</label>
                    <input type="text" id="depart_city" name="depart_city">
                </div>
                <div class="form-group">
                    <label for="arrive_city">Arrive City</label>
                    <input type="text" id="arrive_city" name="arrive_city">
                </div>
                <div class="form-group">
                    <label for="train_status">Train Status</label>
                    <select id="train_status" name="train_status">
                        <option>Schedule</option>
                        <option>Delayed</option>
                        <option>Canceled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Price</label>
                    <input type="number" id="price" name="price" step="0.01">
                </div>
                <button type="submit" class="btn-primary">Save Journey</button>
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
            <!-- Train 数据表格 -->
            <table>
                <thead>
                    <tr>
                        <th>Train ID</th>
                        <th>Type</th>
                        <th>Capacity</th>
                        <th>Station</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- 示例数据，可用后端渲染 -->
                    <tr>
                        <td>T001</td>
                        <td>ETS</td>
                        <td>300</td>
                        <td>KL Sentral</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="tab-content" id="tab-station" style="display:none;">
            <!-- Train Station 数据表格 -->
            <table>
                <thead>
                    <tr>
                        <th>Station ID</th>
                        <th>Name</th>
                        <th>City</th>
                        <th>State</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>S001</td>
                        <td>KL Sentral</td>
                        <td>Kuala Lumpur</td>
                        <td>WP Kuala Lumpur</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="tab-content" id="tab-journey" style="display:none;">
            <!-- Journey 数据表格 -->
            <table>
                <thead>
                    <tr>
                        <th>Journey ID</th>
                        <th>Depart</th>
                        <th>Arrive</th>
                        <th>Status</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>J001</td>
                        <td>2024-06-01 08:00</td>
                        <td>2024-06-01 10:00</td>
                        <td>Schedule</td>
                        <td>50.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<script>
function showTab(tab) {
    document.getElementById('tab-train').style.display = (tab === 'train') ? 'block' : 'none';
    document.getElementById('tab-station').style.display = (tab === 'station') ? 'block' : 'none';
    document.getElementById('tab-journey').style.display = (tab === 'journey') ? 'block' : 'none';
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector('.tab-btn[onclick*=\"' + tab + '\"]').classList.add('active');
}
</script>