/**
 * API Helper Functions
 * Promise-based API calls
 */

const API_BASE = '/restaurant/public/api';
let apiToken = '';

/**
 * Get API token from session
 * @returns {Promise<string>}
 */
export async function getApiToken() {
    if (apiToken) {
        return Promise.resolve(apiToken);
    }
    
    try {
        const response = await fetch(API_BASE + '/auth/token');
        const data = await response.json();
        if (data.success) {
            setApiToken(data.data.token);
            return apiToken;
        }
    } catch (error) {
        console.error('Error getting API token:', error);
    }
    
    return '';
}

/**
 * Set API token
 * @param {string} token
 */
export function setApiToken(token) {
    apiToken = token;
    localStorage.setItem('api_token', token);
}

/**
 * Make an API call
 * @param {string} endpoint - API endpoint
 * @param {string} method - HTTP method (GET, POST, PUT, DELETE)
 * @param {Object|null} data - Request body data
 * @param {boolean} requireAuth - Whether authentication is required
 * @returns {Promise<Object>}
 */
export async function apiCall(endpoint, method = 'GET', data = null, requireAuth = true) {
    // Ensure we have a token if auth is required
    if (requireAuth && !apiToken) {
        await getApiToken();
    }
    
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (requireAuth && apiToken) {
        options.headers['Authorization'] = 'Bearer ' + apiToken;
    }
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(API_BASE + endpoint, options);
        
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: 'Request failed' }));
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API call failed:', error);
        throw error;
    }
}

/**
 * Verify shopping cart
 * @param {Array} items - Cart items
 * @returns {Promise<Object>}
 */
export async function verifyCart(items) {
    return apiCall('/cart/verify', 'POST', { items }, false);
}

