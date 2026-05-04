import { fetchDashboardData, fetchMetricas } from './api.js';
import { state } from '../store/state.js';

/**
 * Orchestrates data polling and updates the global state.
 */

let pollingInterval = null;

export function initDashboard() {
    // Initial fetch
    refreshData();

    // Start polling every 5 seconds
    pollingInterval = setInterval(refreshData, 5000);
}

export async function refreshData() {
    try {
        // Parallel fetch for efficiency
        const [students, metrics] = await Promise.all([
            fetchDashboardData(),
            fetchMetricas()
        ]);

        // Update state (subscribers will be notified)
        state.setStudents(students);
        state.setMetrics(metrics);

        // Update clock
        const clock = document.getElementById('lastUpdated');
        if (clock) {
            clock.textContent = new Date().toLocaleTimeString();
        }
    } catch (error) {
        console.error("Error refreshing dashboard data:", error);
    }
}

export function stopDashboard() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
        pollingInterval = null;
    }
}
