import { state } from '../store/state.js';
import { toggleGrupoQR, fetchEstadoGrupo } from './api.js';
import { toast } from '../../core/toast.js';
import { refreshData } from './dashboard.js';

/**
 * Logic for the Bulk QR Access feature.
 */

export function initBulkQR() {
    const container = document.getElementById('bulkQrActionContainer');
    
    // Subscribe to filter changes to show/hide the action button
    state.subscribe('filterType', async (type) => {
        const isGroup = ['LI4-1', 'LI4-2', 'LISI4-1', 'LISI4-2'].includes(type);
        
        if (isGroup && container) {
            const estado = await fetchEstadoGrupo(type);
            renderBulkButton(type, estado);
            container.classList.remove('admin-hidden');
        } else if (container) {
            container.classList.add('admin-hidden');
            container.innerHTML = '';
        }
    });
}

function renderBulkButton(grupo, estado) {
    const container = document.getElementById('bulkQrActionContainer');
    if (!container) return;

    const qrHabilitado = estado && estado.qr_habilitado == 1;

    container.innerHTML = `
        <div class="d-flex gap-2">
            ${!qrHabilitado ? `
                <button class="admin-btn admin-btn--success" id="btnEnableGroupQR">
                    <span class="admin-icon admin-icon--scan admin-icon--white"></span>
                    Habilitar QRs ${grupo}
                </button>
            ` : ''}
            <button class="admin-btn admin-btn--outline" id="btnDisableGroupQR">
                <span class="admin-icon admin-icon--student-disable"></span>
                Deshabilitar QRs ${grupo}
            </button>
        </div>
    `;

    if (!qrHabilitado) {
        document.getElementById('btnEnableGroupQR').onclick = () => handleBulkAction(grupo, 'habilitar');
    }
    document.getElementById('btnDisableGroupQR').onclick = () => handleBulkAction(grupo, 'deshabilitar');
}

async function handleBulkAction(grupo, accion) {
    const confirmMsg = accion === 'habilitar' 
        ? `¿Habilitar el acceso QR para todos los alumnos confirmados de ${grupo}?`
        : `¿Revocar el acceso QR para todos los alumnos de ${grupo}?`;

    if (!confirm(confirmMsg)) return;

    try {
        const result = await toggleGrupoQR(grupo, accion);
        if (result.success) {
            toast.success(result.message || 'Operación exitosa');
            const nuevoEstado = await fetchEstadoGrupo(grupo);
            renderBulkButton(grupo, nuevoEstado);
            refreshData(); // Refresh to update table statuses
        } else {
            toast.error(result.message || 'Error al procesar la acción');
        }
    } catch (error) {
        toast.error('Error de conexión al servidor');
    }
}
