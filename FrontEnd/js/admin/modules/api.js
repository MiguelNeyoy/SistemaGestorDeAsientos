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

export async function validarQR(token) {
    const response = await coreFetch('/admin/qr/validar', {
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
