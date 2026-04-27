// API Methods

console.log(window.BASE_API_URL);
export async function fetchDashboardData(token) {
    // Definir las promesas básicas obligatorias
    const corePromises = [
        fetch(`${window.BASE_API_URL}/admin/metricas`, { headers: { "Authorization": `Bearer ${token}` } }),
        fetch(`${window.BASE_API_URL}/admin/alumnos`, { headers: { "Authorization": `Bearer ${token}` } })
    ];

    // Intentar obtener asientos por separado para no bloquear si falla
    const [metricasRes, alumnosRes] = await Promise.all(corePromises);
    
    let asientosRes = { ok: false, status: 0, json: () => Promise.resolve({ success: false }) };
    try {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 segundos
        
        asientosRes = await fetch(`${window.BASE_API_URL}/asientos/mapa`, { 
            headers: { "Authorization": `Bearer ${token}` },
            signal: controller.signal
        });
        clearTimeout(timeoutId);
    } catch (e) {
        console.warn("Error o timeout al solicitar mapa de asientos:", e);
    }

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
