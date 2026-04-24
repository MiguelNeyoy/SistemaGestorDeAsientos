import { getGrupo } from './utils.js?v=5';

export function updateMetricsUI(metrics) {
    // 1. Actualiza el Total Global de Invitados
    if (document.getElementById("metric-total"))
        document.getElementById("metric-total").innerText = metrics.total_invitados || 0;

    // 2. Actualiza los Contadores por Grupo (usando los datos del backend)
    // Asumimos que la API envía el total combinado (Alumno + Invitado).
    const grupos = ['LI4-1', 'LI4-2', 'LISI4-1', 'LISI4-2'];
    grupos.forEach(g => {
        const id = g.toLowerCase().replace('-', '');

        // El ID principal de la tarjeta ej: 'metric-li41'
        const elMetric = document.getElementById(`metric-${id}`);
        if (elMetric) {
            elMetric.innerText = (metrics.por_grupo && metrics.por_grupo[g]) ? metrics.por_grupo[g] : 0;
        }
    });
}

export function updateCustomLocalMetrics(allStudentsCache) {
    let totalConfirmados = 0;
    let totalRechazados = 0;

    // Contadores para separar invitados de los alumnos en la UI
    let guestsLI41 = 0;
    let guestsLI42 = 0;
    let guestsLISI41 = 0;
    let guestsLISI42 = 0;

    allStudentsCache.forEach(al => {
        // Validación de seguridad (Soporta si el backend envía 'estado' o 'asistencia_estado')
        const estadoActual = al.asistencia_estado !== undefined ? al.asistencia_estado : al.estado;
        const isConfirmado = estadoActual === 1 || estadoActual === "1";
        const isRechazado = estadoActual === 0 || estadoActual === "0";

        if (isConfirmado) {
            totalConfirmados++;

            // Si el alumno está confirmado, sumamos sus invitados al contador de su grupo
            const cantInvitados = parseInt(al.cantInvitado) || 0;
            const grupo = getGrupo(al.carrera, al.turno);

            if (grupo === 'LI4-1') guestsLI41 += cantInvitados;
            else if (grupo === 'LI4-2') guestsLI42 += cantInvitados;
            else if (grupo === 'LISI4-1') guestsLISI41 += cantInvitados;
            else if (grupo === 'LISI4-2') guestsLISI42 += cantInvitados;

        } else if (isRechazado) {
            totalRechazados++;
        }
    });

    // 1. Actualiza las tarjetas principales
    if (document.getElementById("metric-confirmados")) document.getElementById("metric-confirmados").innerText = totalConfirmados;
    if (document.getElementById("metric-rechazados")) document.getElementById("metric-rechazados").innerText = totalRechazados;
    if (document.getElementById("metric-total-alumnos")) document.getElementById("metric-total-alumnos").innerText = allStudentsCache.length;

    // 2. Actualiza los 'sub-contadores' de invitados debajo de cada grupo
    if (document.getElementById("guests-li41")) document.getElementById("guests-li41").innerText = guestsLI41;
    if (document.getElementById("guests-li42")) document.getElementById("guests-li42").innerText = guestsLI42;
    if (document.getElementById("guests-lisi41")) document.getElementById("guests-lisi41").innerText = guestsLISI41;
    if (document.getElementById("guests-lisi42")) document.getElementById("guests-lisi42").innerText = guestsLISI42;
}