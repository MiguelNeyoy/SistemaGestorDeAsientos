// API Methods
export async function fetchDashboardData(token) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 segundos para asientos

    // Definir todas las promesas para ejecución concurrente
    // Ahora llamamos a ambos eventos (li y lisi) y los combinamos
    const promises = [
        fetch(`${window.BASE_API_URL}/admin/metricas`, { 
            headers: { "Authorization": `Bearer ${token}` } 
        }),
        fetch(`${window.BASE_API_URL}/admin/alumnos`, { 
            headers: { "Authorization": `Bearer ${token}` } 
        }),
        // Llamada a evento LI
        fetch(`${window.BASE_API_URL}/asientos/mapa/li`, { 
            headers: { "Authorization": `Bearer ${token}` },
            signal: controller.signal
        }).catch(e => {
            console.warn("Error al solicitar mapa de asientos LI:", e);
            return { ok: false, status: 0, json: () => Promise.resolve({ success: false }) };
        }),
        // Llamada a evento LISI
        fetch(`${window.BASE_API_URL}/asientos/mapa/lisi`, { 
            headers: { "Authorization": `Bearer ${token}` },
            signal: controller.signal
        }).catch(e => {
            console.warn("Error al solicitar mapa de asientos LISI:", e);
            return { ok: false, status: 0, json: () => Promise.resolve({ success: false }) };
        })
    ];

    const [metricasRes, alumnosRes, asientosLiRes, asientosLisiRes] = await Promise.all(promises);
    clearTimeout(timeoutId);

    return { metricasRes, alumnosRes, asientosLiRes, asientosLisiRes };
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
