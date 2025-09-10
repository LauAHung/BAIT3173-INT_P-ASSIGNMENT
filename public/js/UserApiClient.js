/**
 * User Module API Client
 * Handles all user-related API calls
 */
class UserApiClient {
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
            console.error(`User API Error (${method} ${endpoint}):`, error);
            throw error;
        }
    }

    // Health check
    async healthCheck() {
        return this.request('GET', '/api/health');
    }

    // User authentication
    async loginUser(email, password) {
        return this.request('POST', '/api/user/login', { email, password });
    }

    async registerUser(userData) {
        return this.request('POST', '/api/user/register', userData);
    }

    async logoutUser() {
        return this.request('POST', '/api/user/logout');
    }

    // User profile management
    async getUserProfile(userId) {
        return this.request('GET', `/api/user/profile/${userId}`);
    }

    async updateUserProfile(userId, profileData) {
        return this.request('PUT', `/api/user/profile/${userId}`, profileData);
    }

    async deleteUserProfile(userId) {
        return this.request('DELETE', `/api/user/profile/${userId}`);
    }

    // User verification
    async verifyEmail(token) {
        return this.request('POST', '/api/user/verify-email', { token });
    }

    async resendVerificationEmail(email) {
        return this.request('POST', '/api/user/resend-verification', { email });
    }

    // Password management
    async requestPasswordReset(email) {
        return this.request('POST', '/api/user/forgot-password', { email });
    }

    async resetPassword(token, password, password_confirmation) {
        return this.request('POST', '/api/user/reset-password', {
            token,
            password,
            password_confirmation
        });
    }

    async changePassword(userId, currentPassword, newPassword) {
        return this.request('POST', `/api/user/${userId}/change-password`, {
            current_password: currentPassword,
            new_password: newPassword
        });
    }

    // Social authentication
    async socialLogin(provider, token) {
        return this.request('POST', `/api/user/social-login/${provider}`, { token });
    }

    // User preferences
    async getUserPreferences(userId) {
        return this.request('GET', `/api/user/${userId}/preferences`);
    }

    async updateUserPreferences(userId, preferences) {
        return this.request('PUT', `/api/user/${userId}/preferences`, preferences);
    }
}

// Initialize global instance
window.userApiClient = new UserApiClient();

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UserApiClient;
}
