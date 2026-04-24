import { getGrupo } from './utils.js?v=5';

export function updateMetricsUI(metrics) {
    if(document.getElementById("metric-total")) 
        document.getElementById("metric-total").innerText = metrics.total_invitados || 0;
        
    // Grupos nuevos
    const grupos = ['LI4-1', 'LI4-2', 'LISI4-1', 'LISI4-2'];
    grupos.forEach(g => {
        const id = g.toLowerCase().replace('-', '');
        const el = document.getElementById(`metric-${id}`);
        if (el) {
            el.innerText = (metrics.por_grupo && metrics.por_grupo[g]) ? metrics.por_grupo[g] : 0;
        }
    });
}

export function updateCustomLocalMetrics(allStudentsCache) {
    let totalConfirmados = 0;
    let totalRechazados = 0;
    
    let guestsLI41 = 0;
    let guestsLI42 = 0;
    let guestsLISI41 = 0;
    let guestsLISI42 = 0;

    allStudentsCache.forEach(al => {
        const isConfirmado = al.asistencia_estado === 1 || al.asistencia_estado === "1";
        const isRechazado = al.asistencia_estado === 0 || al.asistencia_estado === "0";

        if (isConfirmado) {
            totalConfirmados++;
            const cant = parseInt(al.cantInvitado) || 0;
            const grupo = getGrupo(al.carrera, al.turno);
            
            if (grupo === 'LI4-1') guestsLI41 += cant;
            else if (grupo === 'LI4-2') guestsLI42 += cant;
            else if (grupo === 'LISI4-1') guestsLISI41 += cant;
            else if (grupo === 'LISI4-2') guestsLISI42 += cant;
            
        } else if (isRechazado) {
            totalRechazados++;
        }
    });

    if(document.getElementById("metric-confirmados")) document.getElementById("metric-confirmados").innerText = totalConfirmados;
    if(document.getElementById("metric-rechazados")) document.getElementById("metric-rechazados").innerText = totalRechazados;
    if(document.getElementById("metric-total-alumnos")) document.getElementById("metric-total-alumnos").innerText = allStudentsCache.length;

    if (document.getElementById("guests-li41")) document.getElementById("guests-li41").innerText = guestsLI41;
    if (document.getElementById("guests-li42")) document.getElementById("guests-li42").innerText = guestsLI42;
    if (document.getElementById("guests-lisi41")) document.getElementById("guests-lisi41").innerText = guestsLISI41;
    if (document.getElementById("guests-lisi42")) document.getElementById("guests-lisi42").innerText = guestsLISI42;
}
