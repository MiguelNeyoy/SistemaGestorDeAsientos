import { coreFetch } from '../../core/api.js';

/**
 * Admin-specific API calls.
 */

export async function fetchDashboardData() {
    const response = await coreFetch('/admin/alumnos');
    const data = await response.json();
    return data.success ? data.data : [];
}

export async function fetchMetricas() {
    const response = await coreFetch('/admin/metricas');
    const data = await response.json();
    return data.success ? data.data : {};
}

export async function updateAlumno(alumnoData) {
    const response = await coreFetch('/admin/alumnos/editar', {
        method: 'PUT',
        body: JSON.stringify(alumnoData)
    });
    return await response.json();
}

/**
 * QR Management
 */

export async function toggleGrupoQR(grupo, accion) {
    const response = await coreFetch('/admin/qr/toggle-grupo', {
        method: 'POST',
        body: JSON.stringify({ grupo, accion })
    });
    return await response.json();
}

export async function fetchEstadoGrupo(grupo) {
    const response = await coreFetch(`/admin/qr/estado-grupo?grupo=${grupo}`);
    const data = await response.json();
    return data.success ? data.data : null;
}

export async function validarQR(token) {
    const response = await coreFetch('/admin/qr/validar', {
        method: 'POST',
        body: JSON.stringify({ token })
    });
    return await response.json();
}

export async function marcarQR(token) {
    const response = await coreFetch('/admin/qr/marcar', {
        method: 'POST',
        body: JSON.stringify({ token })
    });
    return await response.json();
}

/**
 * Seats Management
 */
export async function getMapaAsientos(evento) {
    const response = await coreFetch(`/asientos/mapa/${evento}`);
    const data = await response.json();
    return data.success ? data.data : [];
}

export async function enviarCorreoIndividual(numCuenta) {
    const response = await coreFetch('/admin/alumnos/correo', {
        method: 'POST',
        body: JSON.stringify({ numCuenta })
    });
    return await response.json();
}

export async function resetQrEvento(evento) {
    const response = await coreFetch('/admin/qr/reset-evento', {
        method: 'POST',
        body: JSON.stringify({ evento })
    });
    return await response.json();
}

export async function resetearConfirmaciones() {
    const response = await coreFetch('/admin/confirmaciones/reset', {
        method: 'POST'
    });
    return await response.json();
}

export async function eliminarAlumnos(alumnos) {
    const response = await coreFetch('/admin/alumnos/eliminar', {
        method: 'POST',
        body: JSON.stringify({ alumnos })
    });
    return await response.json();
}

export async function fetchEscaneados(evento) {
    const response = await coreFetch(`/admin/alumnos/exportar-pdf/${evento}`);
    return await response.json();
}

/**
 * Dynamic Assignment
 */

export async function limpiarAsignaciones() {
    const response = await coreFetch('/admin/asignacion/limpiar', { method: 'POST' });
    return await response.json();
}

export async function ejecutarAsignacion(dryRun = false) {
    const response = await coreFetch('/admin/asignacion/ejecutar', {
        method: 'POST',
        body: JSON.stringify({ dry_run: dryRun })
    });
    return await response.json();
}

export async function estadoAsignacion() {
    const response = await coreFetch('/admin/asignacion/estado');
    return await response.json();
}

export async function publicarResultados(publicado) {
    const response = await coreFetch('/admin/asignacion/publicar', {
        method: 'POST',
        body: JSON.stringify({ publicado })
    });
    return await response.json();
}
