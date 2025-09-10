/**
 * Admin Module API Client
 * Handles all admin-related API calls
 */
class AdminApiClient {
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
            console.error(`Admin API Error (${method} ${endpoint}):`, error);
            throw error;
        }
    }

    // Health check
    async healthCheck() {
        return this.request('GET', '/api/health');
    }

    // User management
    async getAdminUserInfo(userId, queryFlag = 1) {
        return this.request('GET', `/api/admin/user/${userId}?queryFlag=${queryFlag}`);
    }

    async getAllUsers(queryFlag = 1, limit = 50, offset = 0) {
        return this.request('GET', `/api/admin/users?queryFlag=${queryFlag}&limit=${limit}&offset=${offset}`);
    }

    async updateUserStatus(userId, status) {
        return this.request('PUT', `/api/admin/user/${userId}/status`, { status });
    }

    async deleteUser(userId) {
        return this.request('DELETE', `/api/admin/user/${userId}`);
    }

    // Concession application management
    async getAdminConcessionApplication(applicationId) {
        return this.request('GET', `/api/admin/concession/${applicationId}`);
    }

    async getAllConcessionApplications(queryFlag = 1, limit = 50, offset = 0) {
        return this.request('GET', `/api/admin/concessions?queryFlag=${queryFlag}&limit=${limit}&offset=${offset}`);
    }

    async updateConcessionApplicationStatus(applicationId, status, adminNotes = '') {
        return this.request('PUT', `/api/admin/concession/${applicationId}/status`, {
            status,
            admin_notes: adminNotes
        });
    }

    async getConcessionStatistics(queryFlag = 1) {
        return this.request('GET', `/api/admin/concession/statistics?queryFlag=${queryFlag}`);
    }

    // Activity logs
    async getAdminLogs(queryFlag = 1, limit = 50, offset = 0) {
        return this.request('GET', `/api/admin/logs?queryFlag=${queryFlag}&limit=${limit}&offset=${offset}`);
    }

    async createAdminLog(action, details, targetId = null) {
        return this.request('POST', '/api/admin/logs', {
            action,
            details,
            target_id: targetId
        });
    }

    // Newsletter management
    async getNewsletterSubscribers(queryFlag = 1, limit = 100, offset = 0) {
        return this.request('GET', `/api/admin/newsletter/subscribers?queryFlag=${queryFlag}&limit=${limit}&offset=${offset}`);
    }

    async sendNewsletter(newsletterData) {
        return this.request('POST', '/api/admin/newsletter/send', newsletterData);
    }

    async getNewsletterStatistics(queryFlag = 1) {
        return this.request('GET', `/api/admin/newsletter/statistics?queryFlag=${queryFlag}`);
    }

    // Booking management
    async getAllBookings(queryFlag = 1, limit = 50, offset = 0) {
        return this.request('GET', `/api/admin/bookings?queryFlag=${queryFlag}&limit=${limit}&offset=${offset}`);
    }

    async getBookingDetails(bookingId) {
        return this.request('GET', `/api/admin/bookings/${bookingId}`);
    }

    async updateBookingStatus(bookingId, status) {
        return this.request('PUT', `/api/admin/bookings/${bookingId}/status`, { status });
    }

    async getBookingStatistics(queryFlag = 1) {
        return this.request('GET', `/api/admin/bookings/statistics?queryFlag=${queryFlag}`);
    }

    // System settings
    async getSystemSettings() {
        return this.request('GET', '/api/admin/settings');
    }

    async updateSystemSettings(settings) {
        return this.request('PUT', '/api/admin/settings', settings);
    }

    // Dashboard data
    async getDashboardData(queryFlag = 1) {
        return this.request('GET', `/api/admin/dashboard?queryFlag=${queryFlag}`);
    }
}

/**
 * Module Communication Manager
 * Handles cross-module communication and data aggregation
 */
class ModuleCommunicationManager {
    constructor() {
        this.userApi = window.userApiClient;
        this.adminApi = window.adminApiClient;
        this.concessionApi = window.concessionApiClient;
    }

    /**
     * Set authentication token for all API clients
     */
    setAuthToken(token) {
        this.userApi.setAuthToken(token);
        this.adminApi.setAuthToken(token);
        this.concessionApi.setAuthToken(token);
    }

    /**
     * Example: User Module -> Admin Module Communication
     * Get user information from admin module
     */
    async getUserInfoFromAdmin(userId, queryFlag = 1) {
        try {
            const response = await this.adminApi.getAdminUserInfo(userId, queryFlag);
            console.log('User info from Admin Module:', response.data);
            return response.data;
        } catch (error) {
            console.error('Failed to get user info from Admin Module:', error);
            throw error;
        }
    }

    /**
     * Example: Concession Card Module -> User Module Communication
     * Get user profile when processing concession application
     */
    async getUserProfileForConcession(userId) {
        try {
            const response = await this.userApi.getUserProfile(userId);
            console.log('User profile for concession:', response.data);
            return response.data;
        } catch (error) {
            console.error('Failed to get user profile for concession:', error);
            throw error;
        }
    }

    /**
     * Example: Admin Module -> Concession Card Module Communication
     * Get concession application details for admin review
     */
    async getConcessionApplicationForAdmin(applicationId) {
        try {
            const response = await this.concessionApi.getConcessionApplicationDetails(applicationId);
            console.log('Concession application for admin:', response.data);
            return response.data;
        } catch (error) {
            console.error('Failed to get concession application for admin:', error);
            throw error;
        }
    }

    /**
     * Example: Cross-module data integration
     * Get user info and their concession applications
     */
    async getUserWithApplications(userId) {
        try {
            const [userInfo, userApplications] = await Promise.all([
                this.userApi.getUserProfile(userId),
                this.concessionApi.getUserApplications(userId)
            ]);

            return {
                user: userInfo.data,
                applications: userApplications.data
            };
        } catch (error) {
            console.error('Failed to get user with applications:', error);
            throw error;
        }
    }

    /**
     * Example: Admin dashboard data aggregation
     * Get statistics from multiple modules
     */
    async getAdminDashboardData() {
        try {
            const [concessionStats, adminLogs, newsletterSubs] = await Promise.all([
                this.concessionApi.getConcessionStatistics(3),
                this.adminApi.getAdminLogs(3),
                this.adminApi.getNewsletterSubscribers(3)
            ]);

            return {
                concessionStats: concessionStats.data,
                adminLogs: adminLogs.data,
                newsletterSubs: newsletterSubs.data
            };
        } catch (error) {
            console.error('Failed to get admin dashboard data:', error);
            throw error;
        }
    }

    /**
     * Get user's complete profile with all related data
     */
    async getUserCompleteProfile(userId) {
        try {
            const [userProfile, applications] = await Promise.all([
                this.userApi.getUserProfile(userId),
                this.concessionApi.getUserApplications(userId)
            ]);

            return {
                profile: userProfile.data,
                concessionApplications: applications.data
            };
        } catch (error) {
            console.error('Failed to get user complete profile:', error);
            throw error;
        }
    }
}

// Initialize global instances
window.adminApiClient = new AdminApiClient();
window.moduleCommunication = new ModuleCommunicationManager();

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { AdminApiClient, ModuleCommunicationManager };
}
