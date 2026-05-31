import { state } from '../store/state.js';

/**
 * Metrics display module.
 * Updates the dashboard counters when the student list changes.
 */

export function initMetrics() {
    state.subscribe('metrics', renderMetrics);
}

function renderMetrics(data) {
    // Update main counters
    updateCounter('metric-confirmados', data.total_confirmados || 0);
    updateCounter('metric-invitados', data.total_invitados || 0);
    updateCounter('metric-asientos', data.total_asientos || 0);
    
    // Detailed breakdown per group
    if (data.por_grupo) {
        Object.entries(data.por_grupo).forEach(([grupo, count]) => {
            const el = document.getElementById(`metric-grupo-${grupo}`);
            if (el) el.textContent = count;
        });
    }
}

function updateCounter(id, value) {
    const el = document.getElementById(id);
    if (!el) return;
    
    // Simple count-up animation
    const start = parseInt(el.textContent) || 0;
    const duration = 500;
    const startTime = performance.now();

    function animate(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const current = Math.floor(start + (value - start) * progress);
        
        el.textContent = current;
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    }

    requestAnimationFrame(animate);
}