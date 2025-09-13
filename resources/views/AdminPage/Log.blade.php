@extends('Layout.master_admin')

@section('title', 'Admin - Log')
@section('page-title', 'Logs')

@push('styles')
    <link href="{{ asset('css/AdminPage/AdminPage.css') }}" rel="stylesheet">
    <style>
        .log-container { padding: 32px; }
        .filters-section { margin-bottom: 24px; padding: 20px; background: linear-gradient(135deg, #2d2d2d 0%, #1f1f1f 100%); border: 1px solid #404040; border-radius: 12px; }
        .filter-group { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; }
        .filter-input, .filter-select { padding: 10px 16px; border: 1px solid #404040; border-radius: 8px; background: #1f1f1f; color: #ffffff; font-size: 14px; }
        .table-container { background: linear-gradient(135deg, #2d2d2d 0%, #1f1f1f 100%); border: 1px solid #404040; border-radius: 16px; overflow: hidden; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background-color: #404040; color: #ffffff; font-weight: 600; padding: 16px; text-align: left; font-size: 14px; }
        .table td { padding: 16px; border-bottom: 1px solid #404040; color: #b3b3b3; font-size: 14px; }
        .table tr:hover { background-color: #404040; }
        .table tr:last-child td { border-bottom: none; }
    </style>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
<div class="log-container">
    <div class="filters-section">
        <div class="filter-group">
            <input id="search-email" class="filter-input" type="text" placeholder="Filter by admin email">
            <select id="search-action" class="filter-select">
                <option value="">All actions</option>
                <option value="add_train">Add Train</option>
                <option value="add_station">Add Station</option>
                <option value="add_journey">Add Journey</option>
                <option value="update_train">Update Train</option>
                <option value="update_station">Update Station</option>
                <option value="update_journey">Update Journey</option>
                <option value="change_user_status">Change User Status</option>
                <option value="delete_user">Delete User</option>
                <option value="send_newsletter">Send News</option>
                <option value="ticket_checkin">Ticket Check-in</option>
                <option value="ticket_checkout">Ticket Check-out</option>
            </select>
            <button class="btn btn-primary" onclick="loadLogs()">Apply</button>
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Admin Email</th>
                    <th>Action</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody id="logs-tbody"></tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function loadLogs() {
    const email = document.getElementById('search-email').value.trim();
    const action = document.getElementById('search-action').value;
    const params = new URLSearchParams();
    if (email) params.append('email', email);
    if (action) params.append('action', action);
    const res = await fetch(`/api/admin/logs?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
    const data = await res.json();
    const tbody = document.getElementById('logs-tbody');
    tbody.innerHTML = '';
    if (data.success && Array.isArray(data.data)) {
        data.data.forEach(row => {
            const tr = document.createElement('tr');
            const when = row.created_at ? new Date(row.created_at).toISOString().slice(0,16).replace('T',' ') : '';
            tr.innerHTML = `<td>${when}</td><td>${row.admin_email}</td><td>${row.action}</td><td>${formatDetails(row.details, row.action)}</td>`;
            tbody.appendChild(tr);
        });
    }
}

function formatDetails(details, action) {
    let d = details;
    if (typeof d === 'string') {
        try { d = JSON.parse(d); } catch (e) {}
    }
    const toTitle = (s) => (s || '').toString().replace(/_/g,' ').replace(/\b\w/g, c => c.toUpperCase());
    if (action === 'change_user_status' && d && d.target_user_id) {
        return `Changed user ${d.target_user_id} status to ${toTitle(d.new_status)}`;
    }
    if (action === 'delete_user' && d && d.target_user_id) {
        return `Deleted user ${d.target_user_id}`;
    }
    if (action === 'add_train' && d) {
        return `Added train ${d.train_no ? d.train_no + ' ' : ''}(ID ${d.train_id})`;
    }
    if (action === 'update_train' && d) {
        return `Updated train ${d.train_id}`;
    }
    if (action === 'add_station' && d) {
        return `Added station ${d.station_name} (ID ${d.station_id})`;
    }
    if (action === 'update_station' && d) {
        return `Updated station ${d.station_id}`;
    }
    if (action === 'add_journey' && d) {
        return `Added journey ${d.journey_id} for train ${d.train_id}`;
    }
    if (action === 'update_journey' && d) {
        return `Updated journey ${d.journey_id}`;
    }
    if (action === 'send_newsletter' && d) {
        return `Sent newsletter "${d.subject}" to ${toTitle(d.recipients)}`;
    }
    if (action === 'ticket_checkin' && d && d.ticket_id) {
        return `Checked in ticket ${d.ticket_id}`;
    }
    if (action === 'ticket_checkout' && d && d.ticket_id) {
        return `Checked out ticket ${d.ticket_id}`;
    }
    try { return `<pre style="margin:0;color:#9ecbff;white-space:pre-wrap;">${JSON.stringify(d, null, 2)}</pre>`; } catch (e) { return d || ''; }
}

document.addEventListener('DOMContentLoaded', loadLogs);
</script>
@endpush