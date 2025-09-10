/**
 * Concession Card Module API Client
 * Handles all concession card related API calls
 */
class ConcessionApiClient {
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
            console.error(`Concession API Error (${method} ${endpoint}):`, error);
            throw error;
        }
    }

    // Health check
    async healthCheck() {
        return this.request('GET', '/api/health');
    }

    // Application management
    async getUserApplications(userId) {
        return this.request('GET', `/api/concession/user/${userId}`);
    }

    async getConcessionApplicationDetails(applicationId) {
        return this.request('GET', `/api/concession/application/${applicationId}`);
    }

    async submitConcessionApplication(applicationData) {
        return this.request('POST', '/api/concession/application', applicationData);
    }

    async updateConcessionApplication(applicationId, applicationData) {
        return this.request('PUT', `/api/concession/application/${applicationId}`, applicationData);
    }

    async cancelConcessionApplication(applicationId, reason = '') {
        return this.request('POST', `/api/concession/application/${applicationId}/cancel`, { reason });
    }

    async withdrawConcessionApplication(applicationId, reason = '') {
        return this.request('POST', `/api/concession/application/${applicationId}/withdraw`, { reason });
    }

    // Document management
    async uploadConcessionDocument(applicationId, documentType, file) {
        const formData = new FormData();
        formData.append('document_type', documentType);
        formData.append('file', file);

        return this.request('POST', `/api/concession/application/${applicationId}/document`, formData, {
            'Content-Type': 'multipart/form-data'
        });
    }

    async getConcessionDocuments(applicationId) {
        return this.request('GET', `/api/concession/application/${applicationId}/documents`);
    }

    async deleteConcessionDocument(documentId) {
        return this.request('DELETE', `/api/concession/document/${documentId}`);
    }

    // Application status and tracking
    async getApplicationStatus(applicationId) {
        return this.request('GET', `/api/concession/application/${applicationId}/status`);
    }

    async getApplicationHistory(applicationId) {
        return this.request('GET', `/api/concession/application/${applicationId}/history`);
    }

    // Statistics and reporting
    async getConcessionStatistics(queryFlag = 1) {
        return this.request('GET', `/api/concession/statistics?queryFlag=${queryFlag}`);
    }

    async getUserConcessionHistory(userId) {
        return this.request('GET', `/api/concession/user/${userId}/history`);
    }

    // Concession types and eligibility
    async getConcessionTypes() {
        return this.request('GET', '/api/concession/types');
    }

    async checkEligibility(userId, concessionType) {
        return this.request('POST', '/api/concession/check-eligibility', {
            user_id: userId,
            concession_type: concessionType
        });
    }

    // Card management (if applicable)
    async getConcessionCard(cardId) {
        return this.request('GET', `/api/concession/card/${cardId}`);
    }

    async getUserConcessionCards(userId) {
        return this.request('GET', `/api/concession/user/${userId}/cards`);
    }

    async reportLostCard(cardId, details) {
        return this.request('POST', `/api/concession/card/${cardId}/report-lost`, details);
    }

    async requestCardReplacement(cardId, reason) {
        return this.request('POST', `/api/concession/card/${cardId}/replacement`, { reason });
    }

    // Notifications
    async getConcessionNotifications(userId) {
        return this.request('GET', `/api/concession/user/${userId}/notifications`);
    }

    async markNotificationAsRead(notificationId) {
        return this.request('PUT', `/api/concession/notification/${notificationId}/read`);
    }
}

// Initialize global instance
window.concessionApiClient = new ConcessionApiClient();

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ConcessionApiClient;
}
