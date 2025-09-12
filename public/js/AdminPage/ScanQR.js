// QR Scanner functionality
let stream = null;
let scanning = false;
let scanInterval = null;

// DOM elements
const startBtn = document.getElementById('start-scanner');
const stopBtn = document.getElementById('stop-scanner');
const video = document.getElementById('scanner-video');
const placeholder = document.getElementById('scanner-placeholder');
const statusIndicator = document.getElementById('status-indicator');
const statusDot = document.querySelector('.status-dot');
const statusText = document.querySelector('.status-indicator .status-text');

// Passenger info elements
const passengerName = document.getElementById('passenger-name');
const journeyId = document.getElementById('journey-id');
const departLocation = document.getElementById('depart-location');
const toLocation = document.getElementById('to-location');
const journeyDate = document.getElementById('journey-date');
const journeyTime = document.getElementById('journey-time');
const journeyStatus = document.getElementById('journey-status');
const checkinBtn = document.getElementById('checkin-btn');
const checkoutBtn = document.getElementById('checkout-btn');
// New elements
let trainNoEl = document.getElementById('train-no');
let trainServiceEl = document.getElementById('train-service');

// Sample passenger data (in real app, this would come from database)
const samplePassengers = {
    'PASS001': {
        name: 'John Smith',
        journeyId: 'JRN001',
        depart: 'Kuala Lumpur',
        to: 'Penang',
        date: 'Dec 15, 2024',
        time: '09:30 AM',
        status: 'paid'
    },
    'PASS002': {
        name: 'Sarah Johnson',
        journeyId: 'JRN002',
        depart: 'Johor Bahru',
        to: 'Kuala Lumpur',
        date: 'Dec 16, 2024',
        time: '02:15 PM',
        status: 'checkin'
    },
    'PASS003': {
        name: 'Michael Chen',
        journeyId: 'JRN003',
        depart: 'Penang',
        to: 'Perak',
        date: 'Dec 17, 2024',
        time: '11:45 AM',
        status: 'checkout'
    }
};

// Event listeners
startBtn.addEventListener('click', startScanner);
stopBtn.addEventListener('click', stopScanner);
checkinBtn.addEventListener('click', () => updateStatus('checkin'));
checkoutBtn.addEventListener('click', () => updateStatus('checkout'));

// Start camera and QR scanning
async function startScanner() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ 
            video: { 
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            } 
        });
        
        video.srcObject = stream;
        video.style.display = 'block';
        placeholder.style.display = 'none';
        
        startBtn.disabled = true;
        stopBtn.disabled = false;
        scanning = true;
        
        // Start scanning for QR codes
        scanInterval = setInterval(scanQRCode, 100);
        
        updateStatusIndicator('Scanner Active', true);
        
    } catch (error) {
        console.error('Error accessing camera:', error);
        alert('Unable to access camera. Please check permissions.');
    }
}

// Stop camera and scanning
function stopScanner() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
        stream = null;
    }
    
    if (scanInterval) {
        clearInterval(scanInterval);
        scanInterval = null;
    }
    
    video.style.display = 'none';
    placeholder.style.display = 'flex';
    
    startBtn.disabled = false;
    stopBtn.disabled = true;
    scanning = false;
    
    updateStatusIndicator('Scanner Stopped', false);
}

// Scan for QR codes in video stream
function scanQRCode() {
    if (!scanning || !video.videoWidth) return;
    
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
    const code = jsQR(imageData.data, imageData.width, imageData.height);
    
    if (code) {
        handleQRCode(code.data);
    }
}

// Handle scanned QR code data
async function handleQRCode(data) {
    console.log('QR Code scanned:', data);
    stopScanner();
    // Expect data is TicketID
    try {
        const res = await fetch(`/api/tickets/${encodeURIComponent(data)}`, { headers: { 'Accept': 'application/json' } });
        const json = await res.json();
        if (!res.ok || !json.success) {
            throw new Error(json.message || 'Invalid ticket');
        }
        const info = json.data;
        displayFromApi(info);
        updateStatusIndicator('QR Code Scanned Successfully', true);
        currentTicketId = info.ticketId;
    } catch (e) {
        alert(e.message || 'Invalid QR Code. Please try again.');
        updateStatusIndicator('Invalid QR Code', false);
        clearPassengerInfo();
        // allow scanning again
        startBtn.disabled = false;
        stopBtn.disabled = true;
    }
}

function displayFromApi(info) {
    passengerName.textContent = info.passenger?.name || '-';
    journeyId.textContent = info.journey?.id || '-';
    departLocation.textContent = info.journey?.from || '-';
    toLocation.textContent = info.journey?.to || '-';
    const dep = info.journey?.departure ? new Date(info.journey.departure) : null;
    const arr = info.journey?.arrival ? new Date(info.journey.arrival) : null;
    journeyDate.textContent = dep ? dep.toISOString().slice(0,10) : '-';
    journeyTime.textContent = dep ? dep.toTimeString().slice(0,5) : '-';
    if (!trainNoEl) trainNoEl = document.getElementById('train-no');
    if (!trainServiceEl) trainServiceEl = document.getElementById('train-service');
    if (trainNoEl) trainNoEl.textContent = info.train?.no || '-';
    if (trainServiceEl) trainServiceEl.textContent = info.train?.service || '-';
    const st = (info.status || 'pending').toLowerCase();
    journeyStatus.className = `status-badge ${st}`;
    journeyStatus.querySelector('.status-text').textContent = st.toUpperCase();
    updateActionButtons(st);
}

// Clear passenger information
function clearPassengerInfo() {
    passengerName.textContent = '-';
    journeyId.textContent = '-';
    departLocation.textContent = '-';
    toLocation.textContent = '-';
    journeyDate.textContent = '-';
    journeyTime.textContent = '-';
    journeyStatus.className = 'status-badge';
    journeyStatus.querySelector('.status-text').textContent = '-';
    
    checkinBtn.disabled = true;
    checkoutBtn.disabled = true;
}

// Update action buttons based on current status
function updateActionButtons(status) {
    switch (status) {
        case 'pending':
        case 'paid': // treat as pending
            checkinBtn.disabled = false;
            checkoutBtn.disabled = true;
            break;
        case 'checkin':
            checkinBtn.disabled = true;
            checkoutBtn.disabled = false;
            break;
        case 'checkout':
            checkinBtn.disabled = true;
            checkoutBtn.disabled = true;
            break;
        default:
            checkinBtn.disabled = true;
            checkoutBtn.disabled = true;
    }
}

// Update passenger status
let currentTicketId = null;
async function updateStatus(newStatus) {
    if (!currentTicketId) return;
    const url = newStatus === 'checkin'
        ? `/api/tickets/${encodeURIComponent(currentTicketId)}/checkin`
        : `/api/tickets/${encodeURIComponent(currentTicketId)}/checkout`;
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.message || 'Update failed');
        // refresh info
        const infoRes = await fetch(`/api/tickets/${encodeURIComponent(currentTicketId)}`, { headers: { 'Accept': 'application/json' } });
        const infoJson = await infoRes.json();
        if (infoRes.ok && infoJson.success) displayFromApi(infoJson.data);
        showNotification(`${newStatus === 'checkin' ? 'Checked In' : 'Checked Out'} successfully!`, 'success');
    } catch (e) {
        alert(e.message || 'Operation failed');
    }
}

// Update status indicator
function updateStatusIndicator(text, isActive) {
    statusText.textContent = text;
    if (isActive) {
        statusDot.classList.add('active');
    } else {
        statusDot.classList.remove('active');
    }
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        z-index: 1000;
        animation: slideIn 0.3s ease;
        background: ${type === 'success' ? '#28a745' : '#17a2b8'};
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    clearPassengerInfo();
    updateStatusIndicator('No QR Code Scanned', false);
});

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (stream) {
        stream.getTracks().forEach(track => track.stop());
    }
    if (scanInterval) {
        clearInterval(scanInterval);
    }
}); 