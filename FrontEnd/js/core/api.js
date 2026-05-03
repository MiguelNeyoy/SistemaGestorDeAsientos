import { API_URL, TOKEN } from '../config.js';
import { handleExpired } from './auth.js';

/**
 * Base fetch wrapper with authorization and timeout.
 */
export async function coreFetch(endpoint, options = {}) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), options.timeout || 10000);

    const headers = {
        'Content-Type': 'application/json',
        ...options.headers
    };

    if (TOKEN) {
        headers['Authorization'] = `Bearer ${TOKEN}`;
    }

    try {
        const response = await fetch(`${API_URL}${endpoint}`, {
            ...options,
            headers,
            signal: controller.signal
        });

        if (response.status === 401 || response.status === 403) {
            handleExpired();
            throw new Error('Session expired or unauthorized');
        }

        return response;
    } catch (error) {
        if (error.name === 'AbortError') {
            throw new Error('Request timeout');
        }
        throw error;
    } finally {
        clearTimeout(timeoutId);
    }
}
