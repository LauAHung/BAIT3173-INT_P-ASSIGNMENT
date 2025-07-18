@extends('Layout.master_admin')

@section('title', 'Admin - News Email Publish')

@push('styles')
    <link href="css/AdminPage/NewsEmailPublish.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
<div class="news-email-container">
    <h2 class="page-title">News Email Publish</h2>
    <div class="email-send-card">
        <form>
            <div class="form-group">
                <label for="email-title">Title:</label>
                <input type="text" id="email-title" name="email-title" required>
            </div>
            <div class="form-group">
                <label>To:</label>
                <span class="to-all">All Subscribers (<span id="subscriber-count">123</span>)</span>
            </div>
            <div class="form-group">
                <label for="email-content">Content:</label>
                <textarea id="email-content" name="email-content" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn-primary">Send Email</button>
        </form>
    </div>

    <div class="subscriber-section">
        <div class="subscriber-filters">
            <input type="text" id="search-email" placeholder="Search by email">
            <button class="btn-primary" onclick="filterSubscribers()">Search</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Subscribed At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>user1@email.com</td>
                    <td>2024-06-01</td>
                    <td>
                        <button class="btn-danger" onclick="unsubscribeEmail(this)">Unsubscribe</button>
                    </td>
                </tr>
                <tr>
                    <td>user2@email.com</td>
                    <td>2024-06-02</td>
                    <td>
                        <button class="btn-danger" onclick="unsubscribeEmail(this)">Unsubscribe</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterSubscribers() {
    const keyword = document.getElementById('search-email').value.toLowerCase();
    document.querySelectorAll('.subscriber-section tbody tr').forEach(row => {
        const email = row.children[0].textContent.toLowerCase();
        row.style.display = email.includes(keyword) ? '' : 'none';
    });
}
function unsubscribeEmail(btn) {
    btn.closest('tr').remove();
    const countSpan = document.getElementById('subscriber-count');
    countSpan.textContent = document.querySelectorAll('.subscriber-section tbody tr:visible').length;
}
</script>
@endpush