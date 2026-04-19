import { state } from './state.js';

export function renderTable(filterText = "") {
    const tbody = document.getElementById("alumnosTableBody");
    if(!tbody) return;
    
    tbody.innerHTML = "";

    const lowerFilter = filterText.toLowerCase();
    const directorioCardBody = document.getElementById("directorioCardBody");
    
    if (directorioCardBody) {
        if (state.currentFilterType !== 'ALL' || filterText.trim() !== "") {
            directorioCardBody.classList.add("has-filter");
        } else {
            directorioCardBody.classList.remove("has-filter");
        }
    }

    // Filtrar
    const filtered = state.allStudentsCache.filter(al => {
        const nombreStr = (al.nombre + " " + al.apellido).toLowerCase();
        const matchesText = al.numCuenta.includes(lowerFilter) || nombreStr.includes(lowerFilter);

        if (!matchesText) return false;

        if (state.currentFilterType !== 'ALL') {
            const isConfirmado = al.asistencia_estado === 1 || al.asistencia_estado === "1";
            const isRechazado = al.asistencia_estado === 0 || al.asistencia_estado === "0";

            if (state.currentFilterType === 'CONFIRMADOS' && !isConfirmado) return false;
            if (state.currentFilterType === 'RECHAZADOS' && !isRechazado) return false;
            if (state.currentFilterType === 'INVITADOS' && !(isConfirmado && al.cantInvitado > 0)) return false;
            if (state.currentFilterType === 'M' && !(isConfirmado && al.turno.toUpperCase() === 'M')) return false;
            if (state.currentFilterType === 'V' && !(isConfirmado && al.turno.toUpperCase() === 'V')) return false;

            if (state.currentFilterType === 'ING') {
                const esIng = al.carrera.toLowerCase().includes("ingeniería") || al.carrera.toLowerCase().includes("sistemas");
                if (!(isConfirmado && esIng)) return false;
            }
            if (state.currentFilterType === 'INF') {
                const esInf = al.carrera.toLowerCase().includes("informática") || al.carrera.toLowerCase().includes("informatica");
                if (!(isConfirmado && esInf)) return false;
            }
        }
        return true;
    });

    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center">No se encontraron alumnos coincidentes.</td></tr>`;
        return;
    }

    filtered.forEach(al => {
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
                <div class="btn-group shadow-sm" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary" title="Editar Alumno" onclick="window.openEditModal('${al.numCuenta}')">
                        <i class="bi bi-pencil-square"></i> <span class="d-none d-md-inline ms-1">Editar</span>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" title="Enviar QR" onclick="alert('Función de enviar QR en desarrollo')">
                        <i class="bi bi-envelope-paper"></i> <span class="d-none d-md-inline ms-1">Enviar</span>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

export function setFilterType(type) {
    state.currentFilterType = type;

    const btn = document.getElementById("btnMostrarTodo");
    if (btn) {
        if (type === 'ALL') {
            btn.classList.add('d-none');
        } else {
            btn.classList.remove('d-none');
        }
    }

    const searchVal = document.getElementById("searchInput") ? document.getElementById("searchInput").value : "";
    renderTable(searchVal);
}
