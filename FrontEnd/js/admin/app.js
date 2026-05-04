import { state } from './store/state.js';
import { initDashboard, refreshData } from './modules/dashboard.js';
import { initTable } from './modules/table.js';
import { initMetrics } from './modules/metrics.js';
import { initQRScanner } from './modules/qrscanner.js';
import { initEditModal } from './modules/modal_editar.js';
import { initBulkQR } from './modules/bulk_qr.js';
import { initMap, show as showMap, hide as hideMap } from './modules/map.js';

/**
 * Main application orchestrator.
 */

document.addEventListener('DOMContentLoaded', () => {
    console.log("Admin Dashboard: DOM loaded. Initializing modules...");

    try {
        // 1. Core Modules Initialization
        initDashboard();
        initTable();
        initMetrics();
        initQRScanner();
        initEditModal();
        initBulkQR();
        initMap();

        // 2. Global UI Listeners
        setupNavigation();
        setupFilters();
        setupSidebar();

        console.log("Admin Dashboard: Initialization complete.");
    } catch (error) {
        console.error("Admin Dashboard: Critical initialization error:", error);
    }
});

function setupNavigation() {
    console.log("Setting up navigation listeners...");
    
    const btnMap = document.getElementById('btnMapaAsientos');
    const btnTable = document.getElementById('link-filter-all'); // Using the sidebar link as "Home"
    const btnRefresh = document.getElementById('btnRefreshData');
    
    // Topbar Actions
    const btnScan = document.getElementById('btnEscanearQR');
    const btnSend = document.getElementById('btnEnviarQR');
    const btnAdd = document.getElementById('btnAgregarAlumno');

    if (btnMap) {
        btnMap.onclick = (e) => {
            e.preventDefault();
            console.log("Map button clicked");
            showMap('li');
        };
    }

    if (btnTable) {
        btnTable.onclick = (e) => {
            e.preventDefault();
            console.log("Dashboard button clicked");
            hideMap();
            state.setFilterType('ALL');
        };
    }

    if (btnRefresh) {
        btnRefresh.onclick = () => {
            console.log("Refresh button clicked");
            refreshData();
        };
    }

    // Modal Triggers
    if (btnScan) {
        btnScan.onclick = () => {
            console.log("Opening QR Scanner modal...");
            const modalEl = document.getElementById('qrScannerModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            } else {
                console.error("qrScannerModal not found");
            }
        };
    }

    if (btnSend) {
        btnSend.onclick = () => {
            console.log("Opening Send QR modal...");
            const modalEl = document.getElementById('enviarQRModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        };
    }

    if (btnAdd) {
        btnAdd.onclick = () => {
            console.log("Opening Add Alumno modal...");
            const modalEl = document.getElementById('agregarAlumnoModal');
            if (modalEl) {
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        };
    }
}

function setupFilters() {
    const filterLinks = document.querySelectorAll('[data-filter]');
    filterLinks.forEach(link => {
        link.onclick = (e) => {
            e.preventDefault();
            const filter = link.dataset.filter;
            console.log(`Filter changed to: ${filter}`);
            
            filterLinks.forEach(l => l.classList.remove('admin-sidebar__link--active'));
            link.classList.add('admin-sidebar__link--active');

            hideMap();
            state.setFilterType(filter);
        };
    });
}

function setupSidebar() {
    const btnLogout = document.getElementById('btnLogout');
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (btnLogout) {
        btnLogout.onclick = () => {
            window.location.href = 'view_admin.php?logout=true';
        };
    }

    if (btnToggleSidebar && sidebar) {
        btnToggleSidebar.onclick = () => {
            sidebar.classList.toggle('admin-sidebar--active');
        };
    }

    // Sidebar collapsibles
    const headers = document.querySelectorAll('.admin-sidebar__section-header');
    headers.forEach(h => {
        h.onclick = () => {
            const section = h.closest('.admin-sidebar__section--collapsible');
            if (section) section.classList.toggle('admin-sidebar__section--collapsed');
        };
    });
}
