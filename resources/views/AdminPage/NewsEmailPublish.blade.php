@extends('Layout.master_admin')

@section('title', 'Admin - News Email Publish')
@section('page-title', 'News & Email')

@push('styles')
    <link href="css/AdminPage/NewsEmailPublish.css" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
@endpush

@section('content')
<div class="news-email-container">
    <div class="email-send-card">
        <form id="newsletterSendForm">
            <div class="form-group">
                <label for="email-title">Title:</label>
                <input type="text" id="email-title" name="email-title" required>
            </div>
            <div class="form-group">
                <label>To:</label>
                <span class="to-all">All Subscribers (<span id="subscriber-count">0</span>)</span>
            </div>
            <div class="form-group">
                <label for="email-content">Content:</label>
                <textarea id="email-content" name="email-content" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Send Email</button>
        </form>
    </div>

    <div class="subscriber-section">
        <div class="subscriber-filters">
            <input type="text" id="search-email" placeholder="Search by email">
            <button class="btn btn-primary" onclick="filterSubscribers()">Search</button>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Subscribed At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="subscriber-tbody">
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterSubscribers() {
    const keyword = document.getElementById('search-email').value.toLowerCase();
    document.querySelectorAll('#subscriber-tbody tr').forEach(row => {
        const email = row.children[0].textContent.toLowerCase();
        row.style.display = email.includes(keyword) ? '' : 'none';
    });
}

// Toast helpers (reuse admin styles loaded globally)
function showMessage(message, type) {
    let container = document.getElementById('message-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'message-container';
        container.className = 'message-container';
        container.style.display = 'none';
        container.innerHTML = '<div id="message-content" class="message-content"><span id="message-text"></span><button onclick="closeMessage()" class="close-btn">&times;</button></div>';
        document.body.appendChild(container);
    }
    const messageText = document.getElementById('message-text');
    const messageContent = document.getElementById('message-content');
    messageText.textContent = message;
    messageContent.className = `message-content message-${type}`;
    container.style.display = 'block';
    setTimeout(() => { container.style.display = 'none'; }, 4000);
}
function closeMessage() {
    const container = document.getElementById('message-container');
    if (container) container.style.display = 'none';
}

async function loadSubscribers() {
    try {
        const res = await fetch('/api/admin/newsletter/subscribers', { headers: { 'Accept': 'application/json' }});
        const data = await res.json();
        const tbody = document.getElementById('subscriber-tbody');
        tbody.innerHTML = '';
        if (data.success && Array.isArray(data.data)) {
            document.getElementById('subscriber-count').textContent = data.data.length;
            data.data.forEach(sub => {
                const tr = document.createElement('tr');
                const dateStr = sub.subscribed_at ? new Date(sub.subscribed_at).toISOString().slice(0,16).replace('T',' ') : '';
                tr.innerHTML = `<td>${sub.email}</td><td>${dateStr}</td><td><button class="btn-danger" data-email="${sub.email}">Unsubscribe</button></td>`;
                tbody.appendChild(tr);
            });
            // bind unsubscribe buttons
            tbody.querySelectorAll('button.btn-danger').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const email = this.getAttribute('data-email');
                    try {
                        const res = await fetch('/api/admin/newsletter/unsubscribe', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ email })
                        });
                        const result = await res.json();
                        if (result.success) {
                            showMessage('Unsubscribed successfully', 'success');
                            loadSubscribers();
                        } else {
                            showMessage(result.message || 'Failed to unsubscribe', 'error');
                        }
                    } catch (e) {
                        console.error(e);
                        showMessage('Failed to unsubscribe', 'error');
                    }
                });
            });
        }
    } catch (e) {
        console.error(e);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    loadSubscribers();
    document.getElementById('newsletterSendForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const subject = document.getElementById('email-title').value.trim();
        const content = document.getElementById('email-content').value.trim();
        if (!subject || !content) return;
        try {
            const res = await fetch('/api/admin/newsletter/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ subject, content, recipients: 'newsletter_subscribers' })
            });
            const data = await res.json();
            if (data.success) {
                showMessage('Newsletter sent successfully.', 'success');
            } else {
                showMessage(data.message || 'Failed to send newsletter.', 'error');
            }
        } catch (e) {
            console.error(e);
            showMessage('Failed to send newsletter.', 'error');
        }
    });
});
</script>
@endpush