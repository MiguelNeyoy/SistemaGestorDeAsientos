import { state } from '../store/state.js';
import { filterStudents } from './filters.js';
import { sortByName, sortBySeat } from './sort.js';
import { getGrupo } from '../../shared/utils.js';
import { enviarCorreoIndividual } from './api.js';
import { toast } from '../../core/toast.js';

/**
 * Directory table rendering module.
 * Uses reactive subscriptions and event delegation.
 */

let lastDataHash = null;

export function initTable() {
    const searchInput = document.getElementById('searchAlumno');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            renderTable(); // Re-render on search
        });
    }

    // Subscribe to state changes
    state.subscribe('students', renderTable);
    state.subscribe('filterType', renderTable);

    // Event delegation for table actions
    const tableBody = document.getElementById('alumnosTableBody');
    if (tableBody) {
        tableBody.addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-action]');
            if (!btn) return;

            const action = btn.dataset.action;
            const numCuenta = btn.dataset.numcuenta;

            if (action === 'edit') {
                window.dispatchEvent(new CustomEvent('open-edit-modal', { detail: { numCuenta } }));
            } else if (action === 'send-individual') {
                if (!confirm('¿Enviar correo con QR a este alumno?')) return;

                toast.info("Enviando correo...");
                try {
                    const res = await enviarCorreoIndividual(numCuenta);
                    if (res.success) {
                        toast.success("Correo enviado correctamente.");
                    } else {
                        toast.error(res.message || "Error al enviar correo.");
                    }
                } catch (err) {
                    toast.error("Error de conexión.");
                }
            }
        });
    }
}

export function renderTable() {
    const tableBody = document.getElementById('alumnosTableBody');
    const searchInput = document.getElementById('searchAlumno');
    if (!tableBody) return;

    const allStudents = state.students;
    const filterType = state.filterType;
    const searchText = searchInput ? searchInput.value : '';

    // 1. Filter
    let filtered = filterStudents(allStudents, filterType, searchText);

    // 2. Sort (default by seat)
    filtered = sortBySeat(filtered);

    // 3. Performance: Simple data check to avoid flickering
    const currentHash = JSON.stringify(filtered.map(s => `${s.numCuenta}-${s.asistencia_estado}-${s.email}`)) + searchText + filterType;
    if (currentHash === lastDataHash) return;
    lastDataHash = currentHash;

    // 4. Render
    if (filtered.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="9" class="admin-table__loading">
                    <div class="admin-table__loading-content">
                        <span class="admin-text-muted">No se encontraron alumnos con los criterios seleccionados.</span>
                    </div>
                </td>
            </tr>
        `;
        return;
    }

    tableBody.innerHTML = filtered.map(alumno => {
        const grupo = getGrupo(alumno.carrera, alumno.turno);
        const estado = parseInt(alumno.asistencia_estado || 0);
        const badgeClass = estado === 1 ? 'admin-badge--confirmado' : (estado === 2 ? 'admin-badge--rechazado' : 'admin-badge--pendiente');
        const estadoTexto = estado === 1 ? 'Confirmado' : (estado === 2 ? 'No Asistirá' : 'Pendiente');

        return `
            <tr>
                <td>
                    <input type="checkbox" class="alumno-checkbox" data-numcuenta="${alumno.numCuenta}">
                </td>
                <td class="fw-bold">${alumno.numCuenta}</td>
                <td class="text-uppercase">${alumno.apellido} ${alumno.nombre}</td>
                <td><span class="admin-badge admin-badge--outline">${grupo}</span></td>
                <td class="text-center">${alumno.cantInvitado || 0}</td>
                <td>
                    <div class="admin-table__email text-muted small">${alumno.email || '—'}</div>
                </td>
                <td class="fw-bold text-navy">
                    ${alumno.letra ? `${alumno.letra}-${alumno.numero}` : '—'}
                </td>
                <td>
                    <span class="admin-badge ${badgeClass}">${estadoTexto}</span>
                </td>
                <td>
                    <div class="d-flex flex-column gap-1">
                        <button class="admin-btn admin-btn--outline admin-btn--sm d-flex align-items-center gap-2" 
                                data-action="edit" 
                                data-numcuenta="${alumno.numCuenta}">
                            <span class="admin-icon admin-icon--edit" style="width:14px;height:14px;"></span>
                            <span style="font-size: 0.75rem;">Editar</span>
                        </button>
                        <button class="admin-btn admin-btn--outline admin-btn--sm d-flex align-items-center gap-2" 
                                data-action="send-individual" 
                                data-numcuenta="${alumno.numCuenta}">
                            <span class="admin-icon admin-icon--send" style="width:14px;height:14px;"></span>
                            <span style="font-size: 0.75rem;">Enviar</span>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}
