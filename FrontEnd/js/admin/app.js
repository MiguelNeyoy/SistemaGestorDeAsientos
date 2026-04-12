console.log(window.BASE_API_URL + " Desde APP.js");
import { state } from './modules/state.js';
import { fetchDashboardData } from './modules/api.js';
import { updateMetricsUI, updateCustomLocalMetrics } from './modules/metrics.js';
import { renderTable, setFilterType } from './modules/table.js';
import { openEditModal, setupModalFormListener } from './modules/modal.js';
import { initQRModule } from './modules/qrscanner.js';

let pollInterval = null;

// Exponer métodos globalmente para los eventos onclick en HTML
window.setFilterType = setFilterType;
window.openEditModal = openEditModal;
window.handleLogout = handleLogout;

document.addEventListener("DOMContentLoaded", () => {
    if (typeof window.ADMIN_TOKEN !== 'undefined' && window.ADMIN_TOKEN) {
        loadDashboardData(window.ADMIN_TOKEN);

        if (!pollInterval) {
            pollInterval = setInterval(() => loadDashboardData(window.ADMIN_TOKEN), 5000);
        }

        const btnLogout = document.getElementById("btnLogout");
        if (btnLogout) {
            btnLogout.addEventListener("click", handleLogout);
        }

        const searchInput = document.getElementById("searchInput");
        if (searchInput) {
            searchInput.addEventListener("keyup", (e) => {
                renderTable(e.target.value);
            });
        }

        // Suscribir el listener del modal pasando la función de recarga
        setupModalFormListener(async () => {
            await loadDashboardData(window.ADMIN_TOKEN);
        });

        initQRModule();
    }
});

function handleLogout() {
    window.location.href = "view_admin.php?logout=1";
}

async function loadDashboardData(token) {
    const statusText = document.getElementById("lastUpdated");
    if (statusText) {
        statusText.innerText = "Actualizando...";
        statusText.classList.remove("bg-success", "bg-danger");
        statusText.classList.add("bg-secondary");
    }

    try {
        const { metricasRes, alumnosRes } = await fetchDashboardData(token);

        if (metricasRes.status === 401 || metricasRes.status === 403) {
            handleLogout();
            return;
        }

        const metricasData = await metricasRes.json();
        const alumnosData = await alumnosRes.json();

        console.log(metricasData);
        if (metricasData.success) {
            updateMetricsUI(metricasData.data);
        }
        if (alumnosData.success) {
            const unicos = new Map();
            alumnosData.data.forEach(al => unicos.set(al.numCuenta, al));
            state.allStudentsCache = Array.from(unicos.values());

            updateCustomLocalMetrics(state.allStudentsCache);

            const searchInput = document.getElementById("searchInput");
            renderTable(searchInput ? searchInput.value : "");
        }

        if (statusText) {
            const now = new Date();
            statusText.innerText = `Actualizado: ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
            statusText.classList.replace("bg-secondary", "bg-success");
        }
    } catch (err) {
        console.error("Error obteniendo datos del dashboard:", err);
        if (statusText) {
            statusText.innerText = "Error de conexión";
            statusText.classList.replace("bg-secondary", "bg-danger");
        }
    }
}
