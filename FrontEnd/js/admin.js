// BASE_API_URL ya está definido globalmente en los archivos PHP (como view_admin.php)
let pollInterval = null;
let allStudentsCache = [];
let currentFilterType = 'ALL';

document.addEventListener("DOMContentLoaded", () => {
    // Si ADMIN_TOKEN está definido (inyectado por PHP) y no está vacío, cargamos los datos
    if (typeof ADMIN_TOKEN !== 'undefined' && ADMIN_TOKEN) {
        loadDashboardData(ADMIN_TOKEN)

        // Configurar Polling (tiempo real) cada 5 segundos
        if (!pollInterval) {
            pollInterval = setInterval(() => loadDashboardData(ADMIN_TOKEN), 5000);
        }

        // Eventos del dashboard
        const btnLogout = document.getElementById("btnLogout");
        if (btnLogout) {
            btnLogout.addEventListener("click", handleLogout);
        }

        const searchInput = document.getElementById("searchInput");
        if (searchInput) {
            searchInput.addEventListener("keyup", (e) => {
                renderTable(e.target.value);
            });
        }
    }
});

function handleLogout() {
    // Redirigir al script PHP para destruir la sesión de forma segura
    window.location.href = "view_admin.php?logout=1";
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

            document.getElementById("metric-confirmados").innerText = totalConfirmados;
            document.getElementById("metric-rechazados").innerText = totalRechazados;
            document.getElementById("metric-total-alumnos").innerText = allStudentsCache.length;

            if (document.getElementById("guests-m")) document.getElementById("guests-m").innerText = guestsM;
            if (document.getElementById("guests-v")) document.getElementById("guests-v").innerText = guestsV;
            if (document.getElementById("guests-ing")) document.getElementById("guests-ing").innerText = guestsIng;
            if (document.getElementById("guests-inf")) document.getElementById("guests-inf").innerText = guestsInf;
            // --------------------------------------------------

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
    if (metrics.por_carrera) {
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

    const directorioCardBody = document.getElementById("directorioCardBody");
    if (directorioCardBody) {
        if (currentFilterType !== 'ALL' || filterText.trim() !== "") {
            directorioCardBody.classList.add("has-filter");
        } else {
            directorioCardBody.classList.remove("has-filter");
        }
    }

    // Filtrar localmente por cuenta o por nombre completo Y POR TIPO DE MÉTRICA
    const filtered = allStudentsCache.filter(al => {
        // 1. Filtro de búsqueda por texto
        const nombreStr = (al.nombre + " " + al.apellido).toLowerCase();
        const matchesText = al.numCuenta.includes(lowerFilter) || nombreStr.includes(lowerFilter);

        if (!matchesText) return false;

        // 2. Filtro de selección de tarjetas (Métricas)
        if (currentFilterType !== 'ALL') {
            const isConfirmado = al.asistencia_estado === 1 || al.asistencia_estado === "1";
            const isRechazado = al.asistencia_estado === 0 || al.asistencia_estado === "0";

            if (currentFilterType === 'CONFIRMADOS' && !isConfirmado) return false;
            if (currentFilterType === 'RECHAZADOS' && !isRechazado) return false;

            if (currentFilterType === 'INVITADOS' && !(isConfirmado && al.cantInvitado > 0)) return false;

            if (currentFilterType === 'M' && !(isConfirmado && al.turno.toUpperCase() === 'M')) return false;
            if (currentFilterType === 'V' && !(isConfirmado && al.turno.toUpperCase() === 'V')) return false;

            if (currentFilterType === 'ING') {
                const esIngenieria = al.carrera.toLowerCase().includes("ingeniería") || al.carrera.toLowerCase().includes("sistemas");
                if (!(isConfirmado && esIngenieria)) return false;
            }
            if (currentFilterType === 'INF') {
                const esInformatica = al.carrera.toLowerCase().includes("informática") || al.carrera.toLowerCase().includes("informatica");
                if (!(isConfirmado && esInformatica)) return false;
            }
        }

        return true;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center">No se encontraron alumnos coincidentes.</td></tr>`;
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

        let carreraCorta = al.carrera;
        const carLower = al.carrera.toLowerCase();
        if (carLower.includes("informática") || carLower.includes("informatica")) {
            carreraCorta = "Informática";
        } else if (carLower.includes("ingeniería") || carLower.includes("sistemas")) {
            carreraCorta = "Ingeniería";
        }

        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><strong>${al.numCuenta}</strong></td>
            <td>${al.nombre} ${al.apellido}</td>
            <td><small>${carreraCorta} (${al.turno})</small></td>
            <td class="text-center fs-5">${al.cantInvitado || 0}</td>
            <td>${al.email || '<span class="text-muted fst-italic">Sin correo</span>'}</td>
            <td class="text-center text-muted">-</td>
            <td class="text-center">${estadoBadge}</td>
            <td class="text-center">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary" title="Editar Alumno" onclick="openEditModal('${al.numCuenta}')"><i class="bi bi-pencil-square"></i> Editar</button>
                    <button type="button" class="btn btn-sm btn-outline-success" title="Enviar QR" onclick="alert('Función de enviar QR en desarrollo')"><i class="bi bi-envelope-paper"></i> Enviar</button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function setFilterType(type) {
    currentFilterType = type;

    // Mostrar u ocultar botón de "Mostrar Todo"
    const btn = document.getElementById("btnMostrarTodo");
    if (btn) {
        if (type === 'ALL') {
            btn.classList.add('d-none');
        } else {
            btn.classList.remove('d-none');
        }
    }

    // Refrescar tabla respetando el buscador
    const searchVal = document.getElementById("searchInput") ? document.getElementById("searchInput").value : "";
    renderTable(searchVal);
}

// Lógica para Edición
let currentEditModal = null;

function openEditModal(numCuenta) {
    const alumno = allStudentsCache.find(a => a.numCuenta === numCuenta);
    if (!alumno) return;

    document.getElementById("editNumCuenta").value = alumno.numCuenta;
    document.getElementById("lblNumCuenta").innerText = alumno.numCuenta;
    document.getElementById("lblNombre").innerText = `${alumno.nombre} ${alumno.apellido}`;
    document.getElementById("editCorreo").value = alumno.email || '';
    
    // Estado
    const asistencia = (alumno.asistencia_estado === 1 || alumno.asistencia_estado === "1") ? "1" : "0";
    const editEstado = document.getElementById("editEstado");
    editEstado.value = asistencia;

    // Invitados
    const editInvitados = document.getElementById("editInvitados");
    editInvitados.value = alumno.cantInvitado || 0;

    // Toggle para esconder invitados si no asiste
    toggleInvitados(asistencia);
    
    editEstado.onchange = (e) => toggleInvitados(e.target.value);

    // Esconder alertas
    const alertDiv = document.getElementById("editAlert");
    alertDiv.classList.add("d-none");

    if (!currentEditModal) {
        currentEditModal = new bootstrap.Modal(document.getElementById('editarAlumnoModal'));
    }
    currentEditModal.show();
}

function toggleInvitados(estado) {
    const container = document.getElementById("containerInvitados");
    if (estado === "0") {
        container.style.display = "none";
        document.getElementById("editInvitados").value = 0;
    } else {
        container.style.display = "block";
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const formEditar = document.getElementById("formEditarAlumno");
    if (formEditar) {
        formEditar.addEventListener("submit", async (e) => {
            e.preventDefault();
            const numCuenta = document.getElementById("editNumCuenta").value;
            const correo = document.getElementById("editCorreo").value;
            const estado = document.getElementById("editEstado").value;
            const invitados = document.getElementById("editInvitados").value;

            const btnGuardar = document.getElementById("btnGuardarEdicion");
            const spinner = document.getElementById("spinnerEdit");
            const alertDiv = document.getElementById("editAlert");

            btnGuardar.disabled = true;
            spinner.classList.remove("d-none");
            alertDiv.classList.add("d-none");

            try {
                const response = await fetch(`${BASE_API_URL}/admin/alumnos/editar`, {
                    method: 'PUT',
                    headers: { 
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${ADMIN_TOKEN}` 
                    },
                    body: JSON.stringify({
                        numCuenta: numCuenta,
                        correo: correo,
                        asistencia_estado: estado,
                        num_invitados: invitados
                    })
                });

                const data = await response.json();
                
                alertDiv.classList.remove("alert-success", "alert-danger");
                if (data.success) {
                    alertDiv.classList.add("alert-success");
                    alertDiv.innerText = "Guardado con éxito.";
                    alertDiv.classList.remove("d-none");
                    
                    // Recargar datos en la tabla
                    await loadDashboardData(ADMIN_TOKEN);
                    
                    setTimeout(() => {
                        currentEditModal.hide();
                    }, 1000);
                } else {
                    alertDiv.classList.add("alert-danger");
                    alertDiv.innerText = data.message || "Error al actualizar alumno";
                    alertDiv.classList.remove("d-none");
                }
            } catch (err) {
                alertDiv.classList.remove("alert-success");
                alertDiv.classList.add("alert-danger");
                alertDiv.innerText = "Error de conexión al servidor";
                alertDiv.classList.remove("d-none");
            } finally {
                btnGuardar.disabled = false;
                spinner.classList.add("d-none");
            }
        });
    }
});
