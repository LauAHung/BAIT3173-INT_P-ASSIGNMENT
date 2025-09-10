/**
 * Booking Module API Client
 * Handles all booking related API calls
 */
class BookingApiClient {
    constructor(baseUrl = '') {
        this.baseUrl = baseUrl;
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
    }

    /**
     * Set authentication token
     */
    setAuthToken(token) {
        this.defaultHeaders['Authorization'] = `Bearer ${token}`;
    }

    /**
     * Make HTTP request
     */
    async request(method, endpoint, data = null, headers = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const config = {
            method: method,
            headers: { ...this.defaultHeaders, ...headers }
        };

        if (data && (method === 'POST' || method === 'PUT' || method === 'PATCH')) {
            config.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, config);
            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.message || `HTTP ${response.status}`);
            }
            
            return result;
        } catch (error) {
            console.error(`Booking API Error (${method} ${endpoint}):`, error);
            throw error;
        }
    }

    // Health check
    async healthCheck() {
        return this.request('GET', '/api/health');
    }

    // Booking Module API calls
    async getBookings() {
        return this.request('GET', '/api/bookings/bookings');
    }

    async getBookingDetails(bookingId) {
        return this.request('GET', `/api/bookings/bookings_detail/${bookingId}`);
    }

    async cancelBooking(bookingId) {
        return this.request('POST', `/api/bookings/bookings/${bookingId}/cancel`);
    }
}

// Initialize global instance
window.bookingApiClient = new BookingApiClient();

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BookingApiClient;
}
