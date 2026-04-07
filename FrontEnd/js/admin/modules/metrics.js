// Metrics UI Logic

export function updateMetricsUI(metrics) {
    if(document.getElementById("metric-total")) 
        document.getElementById("metric-total").innerText = metrics.total_invitados || 0;
        
    if(document.getElementById("metric-m")) 
        document.getElementById("metric-m").innerText = (metrics.por_turno && metrics.por_turno['M']) ? metrics.por_turno['M'] : 0;
        
    if(document.getElementById("metric-v")) 
        document.getElementById("metric-v").innerText = (metrics.por_turno && metrics.por_turno['V']) ? metrics.por_turno['V'] : 0;

    // Identificar las llaves en por_carrera considerando agrupamientos
    let ing = 0, inf = 0;
    if (metrics.por_carrera) {
        for (const [key, value] of Object.entries(metrics.por_carrera)) {
            if (key.toLowerCase().includes("ingeniería") || key.toLowerCase().includes("sistemas")) ing += value;
            else if (key.toLowerCase().includes("informática") || key.toLowerCase().includes("informatica")) inf += value;
        }
    }

    if(document.getElementById("metric-ing")) document.getElementById("metric-ing").innerText = ing;
    if(document.getElementById("metric-inf")) document.getElementById("metric-inf").innerText = inf;
}

export function updateCustomLocalMetrics(allStudentsCache) {
    let totalConfirmados = 0;
    let totalRechazados = 0;
    let guestsM = 0;
    let guestsV = 0;
    let guestsIng = 0;
    let guestsInf = 0;

    allStudentsCache.forEach(al => {
        const isConfirmado = al.asistencia_estado === 1 || al.asistencia_estado === "1";
        const isRechazado = al.asistencia_estado === 0 || al.asistencia_estado === "0";

        if (isConfirmado) {
            totalConfirmados++;
            const cant = parseInt(al.cantInvitado) || 0;
            if (al.turno.toUpperCase() === 'M') guestsM += cant;
            if (al.turno.toUpperCase() === 'V') guestsV += cant;

            const carLower = al.carrera.toLowerCase();
            if (carLower.includes("ingeniería") || carLower.includes("sistemas")) {
                guestsIng += cant;
            } else if (carLower.includes("informática") || carLower.includes("informatica")) {
                guestsInf += cant;
            }
        } else if (isRechazado) {
            totalRechazados++;
        }
    });

    if(document.getElementById("metric-confirmados")) document.getElementById("metric-confirmados").innerText = totalConfirmados;
    if(document.getElementById("metric-rechazados")) document.getElementById("metric-rechazados").innerText = totalRechazados;
    if(document.getElementById("metric-total-alumnos")) document.getElementById("metric-total-alumnos").innerText = allStudentsCache.length;

    if (document.getElementById("guests-m")) document.getElementById("guests-m").innerText = guestsM;
    if (document.getElementById("guests-v")) document.getElementById("guests-v").innerText = guestsV;
    if (document.getElementById("guests-ing")) document.getElementById("guests-ing").innerText = guestsIng;
    if (document.getElementById("guests-inf")) document.getElementById("guests-inf").innerText = guestsInf;
}
