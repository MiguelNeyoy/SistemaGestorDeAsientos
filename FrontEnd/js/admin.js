const BASE_API_URL = "http://localhost/SistemaGestorDeAsientos/API/publico";
let pollInterval = null;
let allStudentsCache = [];

document.addEventListener("DOMContentLoaded", () => {
    checkAuthStatus();

    document.getElementById("loginForm").addEventListener("submit", handleLogin);
    document.getElementById("btnLogout").addEventListener("click", handleLogout);
    
    // Filtrado de tabla
    document.getElementById("searchInput").addEventListener("keyup", (e) => {
        renderTable(e.target.value);
    });
});

function checkAuthStatus() {
    const token = localStorage.getItem("admin_token");
    if (token) {
        document.getElementById("loginView").style.display = "none";
        document.getElementById("dashboardView").style.display = "block";
        loadDashboardData(token);
        // Configurar Polling (tiempo real) cada 15 segundos
        if(!pollInterval) {
            pollInterval = setInterval(() => loadDashboardData(token), 15000);
        }
    } else {
        document.getElementById("dashboardView").style.display = "none";
        document.getElementById("loginView").style.display = "block";
        if(pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    }
}

async function handleLogin(e) {
    e.preventDefault();
    const usuario = document.getElementById("adminUser").value;
    const contrasena = document.getElementById("adminPass").value;
    const errorBox = document.getElementById("loginError");

    try {
        const res = await fetch(`${BASE_API_URL}/admin/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ usuario, contrasena })
        });
        
        const data = await res.json();
        
        if (data.success && data.data && data.data.token) {
            localStorage.setItem("admin_token", data.data.token);
            errorBox.style.display = "none";
            document.getElementById("loginForm").reset();
            checkAuthStatus();
        } else {
            errorBox.innerText = data.message || "Credenciales inválidas";
            errorBox.style.display = "block";
        }
    } catch (err) {
        errorBox.innerText = "Error de red al intentar iniciar sesión.";
        errorBox.style.display = "block";
    }
}

function handleLogout() {
    localStorage.removeItem("admin_token");
    checkAuthStatus();
}

async function loadDashboardData(token) {
    // Para dar feedback visual de actualización
    const statusText = document.getElementById("lastUpdated");
    statusText.innerText = "Actualizando...";
    statusText.classList.remove("bg-success", "bg-danger");
    statusText.classList.add("bg-secondary");

    try {
        // Ejecutar ambas peticiones en paralelo
        const [metricasRes, alumnosRes] = await Promise.all([
            fetch(`${BASE_API_URL}/admin/metricas`, { headers: { "Authorization": `Bearer ${token}` } }),
            fetch(`${BASE_API_URL}/admin/alumnos`, { headers: { "Authorization": `Bearer ${token}` } })
        ]);

        if (metricasRes.status === 401 || metricasRes.status === 403) {
            // Token inválido o expirado
            handleLogout();
            return;
        }

        const metricasData = await metricasRes.json();
        const alumnosData = await alumnosRes.json();

        if (metricasData.success) {
            updateMetricsUI(metricasData.data);
        }
        if (alumnosData.success) {
            allStudentsCache = alumnosData.data;
            renderTable(document.getElementById("searchInput").value);
        }

        const now = new Date();
        statusText.innerText = `Actualizado: ${now.getHours().toString().padStart(2, '0')}:${now.getMinutes().toString().padStart(2, '0')}:${now.getSeconds().toString().padStart(2, '0')}`;
        statusText.classList.replace("bg-secondary", "bg-success");

    } catch (err) {
        console.error("Error obteniendo datos del dashboard:", err);
        statusText.innerText = "Error de conexión";
        statusText.classList.replace("bg-secondary", "bg-danger");
    }
}

function updateMetricsUI(metrics) {
    document.getElementById("metric-total").innerText = metrics.total_invitados || 0;
    document.getElementById("metric-m").innerText = (metrics.por_turno && metrics.por_turno['M']) ? metrics.por_turno['M'] : 0;
    document.getElementById("metric-v").innerText = (metrics.por_turno && metrics.por_turno['V']) ? metrics.por_turno['V'] : 0;
    
    // Identificar las llaves en por_carrera considerando los agrupamientos creados en el backend
    let ing = 0, inf = 0;
    if(metrics.por_carrera) {
        for (const [key, value] of Object.entries(metrics.por_carrera)) {
            if (key.toLowerCase().includes("ingeniería") || key.toLowerCase().includes("sistemas")) ing += value;
            else if (key.toLowerCase().includes("informática") || key.toLowerCase().includes("informatica")) inf += value;
        }
    }
    
    document.getElementById("metric-ing").innerText = ing;
    document.getElementById("metric-inf").innerText = inf;
}

function renderTable(filterText = "") {
    const tbody = document.getElementById("alumnosTableBody");
    tbody.innerHTML = "";

    const lowerFilter = filterText.toLowerCase();
    
    // Filtrar localmente por cuenta o por nombre completo
    const filtered = allStudentsCache.filter(al => {
        const nombreStr = (al.nombre + " " + al.apellido).toLowerCase();
        return al.numCuenta.includes(lowerFilter) || nombreStr.includes(lowerFilter);
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="text-center">No se encontraron alumnos coincidentes.</td></tr>`;
        return;
    }

    filtered.forEach(al => {
        // Estado
        let estadoBadge = `<span class="badge badge-pendiente">Pendiente</span>`;
        if (al.asistencia_estado === 1 || al.asistencia_estado === "1") {
            estadoBadge = `<span class="badge badge-confirmado">Sí Asiste</span>`;
        } else if (al.asistencia_estado === 0 || al.asistencia_estado === "0") {
            estadoBadge = `<span class="badge badge-rechazado">No Asistirá</span>`;
        }

        const carreraCorta = al.carrera.toLowerCase().includes("informática") ? "Informática" : "Ingeniería";
        
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><strong>${al.numCuenta}</strong></td>
            <td>${al.nombre} ${al.apellido}</td>
            <td><small>${carreraCorta} (${al.turno})</small></td>
            <td class="text-center fs-5">${al.cantInvitado || 0}</td>
            <td>${al.email || '<span class="text-muted fst-italic">Sin correo</span>'}</td>
            <td class="text-center">${estadoBadge}</td>
        `;
        tbody.appendChild(tr);
    });
}
