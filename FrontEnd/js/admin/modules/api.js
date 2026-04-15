// API Methods

console.log(window.BASE_API_URL);
export async function fetchDashboardData(token) {
    const [metricasRes, alumnosRes] = await Promise.all([
        fetch(`${window.BASE_API_URL}/admin/metricas`, { headers: { "Authorization": `Bearer ${token}` } }),
        fetch(`${window.BASE_API_URL}/admin/alumnos`, { headers: { "Authorization": `Bearer ${token}` } })
    ]);
    return { metricasRes, alumnosRes };
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

export async function validarQrToken(tokenQr, adminToken) {
    const response = await fetch(`${window.BASE_API_URL}/qr/validar`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${adminToken}`
        },
        body: JSON.stringify({ token: tokenQr })
    });
    return response.json();
}

export async function confirmarLlegadaQr(numCuenta, adminToken) {
    const response = await fetch(`${window.BASE_API_URL}/qr/confirmar`, {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
            "Authorization": `Bearer ${adminToken}`
        },
        body: JSON.stringify({ numero_cuenta: numCuenta })
    });
    return response.json();
}
