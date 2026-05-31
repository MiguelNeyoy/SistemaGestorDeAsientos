/**
 * Central configuration for the JS application.
 * Freezes the data injected by PHP via window.__APP_CONFIG__
 */
const cfg = Object.freeze(window.__APP_CONFIG__ || {
    apiUrl: window.BASE_API_URL || '',
    token: window.ADMIN_TOKEN || ''
});

export const API_URL = cfg.apiUrl;
export const TOKEN = cfg.token;
export const IS_ADMIN = !!cfg.token;

console.log("JS Config loaded:", { API_URL, IS_ADMIN });
