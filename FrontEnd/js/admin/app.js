import { state } from './store/state.js';
import { initDashboard, refreshData } from './modules/dashboard.js';
import { initTable } from './modules/table.js';
import { initMetrics } from './modules/metrics.js';
import { initQRScanner } from './modules/qrscanner.js';
import { initEditModal } from './modules/modal_editar.js';
import { initBulkQR } from './modules/bulk_qr.js';
import { initMap, show as showMap, hide as hideMap } from './modules/map.js';
import { initEliminar } from './modules/eliminar.js';
import { resetQrEvento, resetearConfirmaciones, limpiarAsignaciones, ejecutarAsignacion, estadoAsignacion, publicarResultados } from './modules/api.js';
import { exportarPdf } from './modules/pdf_export.js?v=1';
import { setupEmailFormListener } from './modules/emails.js';
import { toast } from '../core/toast.js';

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
        initEliminar();
        setupEmailFormListener();

        // 2. Global UI Listeners
        setupNavigation();
        setupFilters();
        setupSidebar();

        console.log("Admin Dashboard: Initialization complete.");
    } catch (error) {
        console.error("Admin Dashboard: Critical initialization error:", error);
    }
});

function clearAllActiveLinks() {
    const allLinks = document.querySelectorAll('.admin-sidebar__link');
    allLinks.forEach(l => l.classList.remove('admin-sidebar__link--active'));
}

function setupNavigation() {
    console.log("Setting up navigation listeners...");
    
    const btnMap = document.getElementById('btnMapaAsientos');
    const btnTable = document.getElementById('link-filter-all'); // Using the sidebar link as "Home"
    const btnRefresh = document.getElementById('btnRefreshData');
    const logoLink = document.getElementById('link-logo-dashboard');
    
    // Topbar Actions
    const btnScan = document.getElementById('btnEscanearQR');
    const btnSend = document.getElementById('btnEnviarQR');
    const btnAdd = document.getElementById('btnAgregarAlumno');

    if (logoLink) {
        logoLink.onclick = (e) => {
            e.preventDefault();
            console.log("Logo clicked, redirecting to dashboard...");
            clearAllActiveLinks();
            if (btnTable) {
                btnTable.classList.add('admin-sidebar__link--active');
            }
            hideMap();
            state.setFilterType('ALL');
        };
    }

    if (btnMap) {
        btnMap.onclick = (e) => {
            e.preventDefault();
            console.log("Map button clicked");
            clearAllActiveLinks();
            btnMap.classList.add('admin-sidebar__link--active');
            showMap('li');
        };
    }

    if (btnTable) {
        btnTable.onclick = (e) => {
            e.preventDefault();
            console.log("Dashboard button clicked");
            clearAllActiveLinks();
            btnTable.classList.add('admin-sidebar__link--active');
            hideMap();
            state.setFilterType('ALL');
        };
    }

    // Botones de Restablecimiento de QRs
    const btnResetLi = document.getElementById('btnResetQrLi');
    const btnResetLisi = document.getElementById('btnResetQrLisi');

    const handleReset = async (evento, label) => {
        const confirmacion = confirm(`¿Estás seguro de que deseas restablecer TODOS los QRs de ${label}? Esta acción no se puede deshacer y marcará todos los pases de este evento como NO escaneados.`);
        if (!confirmacion) return;

        try {
            toast.info(`Restableciendo pases de ${label}...`);
            const result = await resetQrEvento(evento);
            if (result.success) {
                toast.success(result.message || `Pases de ${label} restablecidos correctamente.`);
                refreshData();
            } else {
                toast.error(result.message || 'Error al restablecer pases.');
            }
        } catch (error) {
            console.error(error);
            toast.error('Error al comunicarse con el servidor.');
        }
    };

    if (btnResetLi) {
        btnResetLi.onclick = (e) => {
            e.preventDefault();
            handleReset('li', 'LI (Informática)');
        };
    }

    if (btnResetLisi) {
        btnResetLisi.onclick = (e) => {
            e.preventDefault();
            handleReset('lisi', 'LISI (Sistemas)');
        };
    }

    const btnResetConfirmaciones = document.getElementById('btnResetConfirmaciones');
    if (btnResetConfirmaciones) {
        btnResetConfirmaciones.onclick = async (e) => {
            e.preventDefault();
            if (!confirm('⚠️ ¿Estás seguro de resetear TODAS las confirmaciones?')) return;
            if (!confirm('⚠️ Esta acción no se puede deshacer. ¿Confirmar?')) return;
            
            try {
                toast.info('Reseteando confirmaciones...');
                const result = await resetearConfirmaciones();
                if (result.success) {
                    toast.success(result.message || 'Confirmaciones reseteadas correctamente.');
                    refreshData();
                } else {
                    toast.error(result.message || 'Error al resetear confirmaciones.');
                }
            } catch (error) {
                console.error(error);
                toast.error('Error al resetear confirmaciones.');
            }
        };
    }

    // === Dynamic Assignment Handlers ===
    const btnLimpiar = document.getElementById('btnLimpiarAsignaciones');
    const btnAsignar = document.getElementById('btnAsignarAsientos');
    const switchPublicar = document.getElementById('switchPublicar');

    if (btnLimpiar) {
        btnLimpiar.onclick = async (e) => {
            e.preventDefault();
            if (!confirm('¿Seguro de limpiar todas las asignaciones? Los alumnos perderán su asiento.')) return;
            try {
                toast.info('Limpiando asignaciones...');
                const res = await limpiarAsignaciones();
                if (res.success) {
                    toast.success(res.message || 'Asignaciones limpiadas');
                    refreshData();
                } else {
                    toast.error(res.message || 'Error al limpiar');
                }
            } catch (err) {
                toast.error('Error al comunicarse con el servidor.');
            }
        };
    }

    if (btnAsignar) {
        btnAsignar.onclick = async (e) => {
            e.preventDefault();
            try {
                // Step 1: Dry run
                toast.info('Obteniendo vista previa...');
                const preview = await ejecutarAsignacion(true);
                if (!preview.success) {
                    toast.error(preview.message || 'Error en vista previa');
                    return;
                }
                const d = preview.data;
                const body = document.getElementById('vistaPreviaBody');
                if (body) {
                    body.innerHTML = `
                        <p>Se asignarán:</p>
                        <ul class="list-unstyled">
                            <li><strong>LI:</strong> ${d.li} alumnos → ${d.li} asientos</li>
                            <li><strong>LISI:</strong> ${d.lisi} alumnos → ${d.lisi} asientos</li>
                        </ul>
                        ${d.sin_asiento > 0 ? `<div class="alert alert-warning mb-0">${d.sin_asiento} alumno(s) sin asiento disponible</div>` : '<p class="text-muted mb-0">Todos los alumnos tienen asiento disponible.</p>'}
                    `;
                }
                const modalEl = document.getElementById('modalVistaPrevia');
                if (modalEl) {
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                }
            } catch (err) {
                toast.error('Error al obtener vista previa.');
            }
        };
    }

    // Confirmar asignación (button inside the vista previa modal)
    const btnConfirmar = document.getElementById('btnConfirmarAsignacion');
    if (btnConfirmar) {
        btnConfirmar.onclick = async () => {
            // Close preview modal
            const previewModal = bootstrap.Modal.getInstance(document.getElementById('modalVistaPrevia'));
            if (previewModal) previewModal.hide();

            // Show progress modal
            const progressModalEl = document.getElementById('modalProgreso');
            if (!progressModalEl) return;
            const progressModal = new bootstrap.Modal(progressModalEl);
            progressModal.show();

            const bar = document.getElementById('barraProgreso');
            const texto = document.getElementById('progresoTexto');
            if (bar) bar.style.width = '30%';
            if (texto) texto.textContent = 'Asignando asientos...';

            try {
                const res = await ejecutarAsignacion(false);
                if (res.success) {
                    const d = res.data;
                    if (bar) bar.style.width = '100%';
                    if (texto) {
                        texto.innerHTML = `<span class="text-success">✓ Asignación completada</span><br><small>LI: ${d.li} asientos | LISI: ${d.lisi} asientos</small>`;
                    }
                    toast.success(res.message || 'Asignación completada');
                    refreshData();
                } else {
                    if (bar) bar.classList.replace('progress-bar-animated', 'bg-danger');
                    if (texto) texto.innerHTML = `<span class="text-danger">✗ Error: ${res.message}</span>`;
                    toast.error(res.message || 'Error en asignación');
                }
            } catch (err) {
                if (texto) texto.innerHTML = '<span class="text-danger">✗ Error de conexión</span>';
                toast.error('Error al comunicarse con el servidor.');
            }

            // Auto-close progress modal after 3s
            setTimeout(() => {
                const pm = bootstrap.Modal.getInstance(progressModalEl);
                if (pm) pm.hide();
            }, 3000);
        };
    }

    // Switch publicar
    if (switchPublicar) {
        switchPublicar.onchange = async function() {
            try {
                const res = await publicarResultados(this.checked);
                if (res.success) {
                    toast.success(this.checked ? 'Resultados publicados' : 'Resultados ocultados');
                } else {
                    this.checked = !this.checked;
                    toast.error(res.message || 'Error al publicar');
                }
            } catch (err) {
                this.checked = !this.checked;
                toast.error('Error de conexión');
            }
        };
    }

    // PDF Export
    const btnExportLi = document.getElementById('btnExportarPdfLi');
    const btnExportLisi = document.getElementById('btnExportarPdfLisi');

    if (btnExportLi) {
        btnExportLi.onclick = (e) => {
            e.preventDefault();
            exportarPdf('li', 'LI (Informática)');
        };
    }
    if (btnExportLisi) {
        btnExportLisi.onclick = (e) => {
            e.preventDefault();
            exportarPdf('lisi', 'LISI (Sistemas)');
        };
    }

    if (btnRefresh) {
        btnRefresh.onclick = () => {
            console.log("Refresh button clicked");
            refreshData();
        };
    }

    // Load initial estado for switch
    (async function initSwitchState() {
        try {
            const res = await estadoAsignacion();
            if (res.success && res.data) {
                const sw = document.getElementById('switchPublicar');
                if (sw) sw.checked = res.data.publicado;
            }
        } catch (e) {
            // Silent fail — switch stays unchecked
        }
    })();

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
            
            clearAllActiveLinks();
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
    const overlay = document.getElementById('sidebarOverlay');
    
    const toggleSidebar = () => {
        sidebar.classList.toggle('admin-sidebar--active');
    };

    if (btnLogout) {
        btnLogout.onclick = () => {
            window.location.href = 'view_admin.php?logout=true';
        };
    }

    if (btnToggleSidebar && sidebar) {
        btnToggleSidebar.onclick = (e) => {
            e.stopPropagation();
            toggleSidebar();
        };

        if (overlay) {
            overlay.onclick = () => {
                sidebar.classList.remove('admin-sidebar--active');
            };
        }

        // Close sidebar when clicking outside (on desktop or content)
        document.addEventListener('click', (e) => {
            if (sidebar.classList.contains('admin-sidebar--active') && 
                !sidebar.contains(e.target) && 
                !btnToggleSidebar.contains(e.target)) {
                sidebar.classList.remove('admin-sidebar--active');
            }
        });
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
