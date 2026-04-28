import { state } from './modules/state.js?v=5';
import { fetchDashboardData } from './modules/api.js?v=5';
import { updateMetricsUI, updateCustomLocalMetrics } from './modules/metrics.js?v=5';
import { renderTable, setFilterType } from './modules/table.js?v=5';
import { openEditModal, setupModalFormListener } from './modules/modal.js?v=5';
import { initQRModule } from './modules/qrscanner.js?v=5';
import { setupEmailFormListener } from './modules/emails.js?v=5';

let pollInterval = null;

// Exponer métodos globalmente para los eventos onclick en HTML
window.setFilterType = setFilterType;
window.openEditModal = openEditModal;
window.handleLogout = handleLogout;

document.addEventListener("DOMContentLoaded", () => {
    // Manejador del menú colapsable (Sidebar)
    const filterToggleBtn = document.querySelector('.admin-sidebar__section-toggle');
    if (filterToggleBtn) {
        filterToggleBtn.addEventListener('click', () => {
            const section = filterToggleBtn.closest('.admin-sidebar__section--collapsible');
            if (section) section.classList.toggle('admin-sidebar__section--collapsed');
        });
    }

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
        setupEmailFormListener();
    }
});

function handleLogout() {
    window.location.href = "view_admin.php?logout=1";
}

async function loadDashboardData(token) {
    const statusText = document.getElementById("lastUpdated");

    try {
        const { metricasRes, alumnosRes, asientosLiRes, asientosLisiRes } = await fetchDashboardData(token);

        if (metricasRes.status === 401 || metricasRes.status === 403) {
            handleLogout();
            return;
        }

        const metricasData = await metricasRes.json();
        const alumnosData = await alumnosRes.json();

        // Combinar asientos de ambos eventos (LI y LISI)
        const seatMap = new Map();

        // Procesar asientos LI
        try {
            if (asientosLiRes.ok) {
                const asientosLiData = await asientosLiRes.json();
                if (asientosLiData.success && asientosLiData.data && asientosLiData.data.asientos) {
                    asientosLiData.data.asientos.forEach(s => {
                        if (s.numCuenta) {
                            seatMap.set(s.numCuenta.toString(), s.id_asiento);
                        }
                    });
                }
            }
        } catch (e) {
            console.warn("Error procesando asientos LI:", e);
        }

        // Procesar asientos LISI
        try {
            if (asientosLisiRes.ok) {
                const asientosLisiData = await asientosLisiRes.json();
                if (asientosLisiData.success && asientosLisiData.data && asientosLisiData.data.asientos) {
                    asientosLisiData.data.asientos.forEach(s => {
                        if (s.numCuenta) {
                            seatMap.set(s.numCuenta.toString(), s.id_asiento);
                        }
                    });
                }
            }
        } catch (e) {
            console.warn("Error procesando asientos LISI:", e);
        }

        if (metricasData.success && metricasData.data) {
            updateMetricsUI(metricasData.data);
        }

        const unicos = new Map();
        if (alumnosData.success && Array.isArray(alumnosData.data)) {
            alumnosData.data.forEach(al => {
                // Asignar el asiento si existe en el mapa
                if (al.numCuenta) {
                    const numCuentaStr = String(al.numCuenta);
                    al.asiento = seatMap.get(numCuentaStr) || "-";
                    unicos.set(al.numCuenta, al);
                }
            });
        } else {
            console.warn("No se recibieron alumnos o el formato es incorrecto");
        }

        // Ordenar: si hay filtro de evento (LI o LISI), ordenar por asiento
        const filtroEvento = (state.currentFilterType === 'LI' || state.currentFilterType === 'LISI');

        state.allStudentsCache = Array.from(unicos.values()).sort((a, b) => {
            // Si hay filtro de evento activo, ordenar por asiento
            if (filtroEvento) {
                const seatA = a.asiento || "-";
                const seatB = b.asiento || "-";

                // Si no tienen asiento, ir al final
                if (seatA === "-" && seatB === "-") return 0;
                if (seatA === "-") return 1;
                if (seatB === "-") return -1;

                // Extraer letra y numero
                const letraA = seatA.charAt(0);
                const letraB = seatB.charAt(0);
                const numA = parseInt(seatA.substring(1)) || 0;
                const numB = parseInt(seatB.substring(1)) || 0;

                // Comparar letras primero
                if (letraA !== letraB) {
                    return letraA.localeCompare(letraB);
                }
                // Luego numeros
                return numA - numB;
            }

            // Default: ordenar por apellido
            const apellidoA = (a.apellido || "").trim();
            const apellidoB = (b.apellido || "").trim();
            const comparacionApellidos = apellidoA.localeCompare(apellidoB, 'es', { sensitivity: 'base' });

            if (comparacionApellidos !== 0) return comparacionApellidos;

            const nombreA = (a.nombre || "").trim();
            const nombreB = (b.nombre || "").trim();
            return nombreA.localeCompare(nombreB, 'es', { sensitivity: 'base' });
        });

        updateCustomLocalMetrics(state.allStudentsCache);

        const searchInput = document.getElementById("searchInput");
        renderTable(searchInput ? searchInput.value : "");

        if (statusText) {
            const now = new Date();
            statusText.innerText = `Actualizado: ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
        }
    } catch (err) {
        console.error("Error obteniendo datos del dashboard:", err);
        if (statusText) {
            statusText.innerText = "Error de conexión";
        }
    }
}
