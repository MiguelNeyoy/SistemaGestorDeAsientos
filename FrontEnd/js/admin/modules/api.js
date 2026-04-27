// API Methods
export async function fetchDashboardData(token) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 segundos para asientos

    // Definir todas las promesas para ejecución concurrente
    const promises = [
        fetch(`${window.BASE_API_URL}/admin/metricas`, { 
            headers: { "Authorization": `Bearer ${token}` } 
        }),
        fetch(`${window.BASE_API_URL}/admin/alumnos`, { 
            headers: { "Authorization": `Bearer ${token}` } 
        }),
        fetch(`${window.BASE_API_URL}/asientos/mapa`, { 
            headers: { "Authorization": `Bearer ${token}` },
            signal: controller.signal
        }).catch(e => {
            // Si falla o hay timeout en asientos, devolvemos un objeto de respuesta fallido 
            // para no romper el flujo principal del dashboard
            console.warn("Error o timeout al solicitar mapa de asientos:", e);
            return { ok: false, status: 0, json: () => Promise.resolve({ success: false }) };
        })
    ];

    const [metricasRes, alumnosRes, asientosRes] = await Promise.all(promises);
    clearTimeout(timeoutId);

    return { metricasRes, alumnosRes, asientosRes };
}

export async function updateAlumno(token, dataPayload) {
    const response = await fetch(`${window.BASE_API_URL}/admin/alumnos/editar`, {
        method: 'PUT',
        headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${token}`
        },
        body: JSON.stringify(dataPayload)
    });
    return response.json();
}
