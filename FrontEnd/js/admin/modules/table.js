import { state } from './state.js';
import { getGrupo } from './utils.js';

/**
 * Función principal para renderizar la tabla de alumnos.
 * @param {string} filterText - Texto de búsqueda ingresado por el usuario.
 */
export function renderTable(filterText = "") {
    // Obtener el cuerpo de la tabla donde se insertarán las filas
    const tbody = document.getElementById("alumnosTableBody");
    if(!tbody) return;
    
    // Limpiar el contenido actual de la tabla
    tbody.innerHTML = "";

    const lowerFilter = filterText.toLowerCase();
    const directorioCardBody = document.getElementById("directorioCardBody");
    
    // Feedback visual: Si hay filtros activos, se agrega una clase CSS para resaltar el contenedor
    if (directorioCardBody) {
        if (state.currentFilterType !== 'ALL' || filterText.trim() !== "") {
            directorioCardBody.classList.add("has-filter");
        } else {
            directorioCardBody.classList.remove("has-filter");
        }
    }

    // Lógica de Filtrado: Filtra la caché de alumnos basado en el texto de búsqueda y el tipo de filtro activo
    const filtered = state.allStudentsCache.filter(al => {
        const nombreStr = (al.nombre + " " + al.apellido).toLowerCase();
        // Comprobar si el número de cuenta o el nombre completo coinciden con la búsqueda
        const matchesText = al.numCuenta.includes(lowerFilter) || nombreStr.includes(lowerFilter);

        if (!matchesText) return false;

        // Aplicar filtros por categorías (Confirmados, Rechazados, Turno, Carrera, etc.)
        if (state.currentFilterType !== 'ALL') {
            const isConfirmado = al.asistencia_estado === 1 || al.asistencia_estado === "1";
            const isRechazado = al.asistencia_estado === 0 || al.asistencia_estado === "0";

            if (state.currentFilterType === 'CONFIRMADOS' && !isConfirmado) return false;
            if (state.currentFilterType === 'RECHAZADOS' && !isRechazado) return false;
            if (state.currentFilterType === 'INVITADOS' && !(isConfirmado && al.cantInvitado > 0)) return false;
            if (['LI4-1', 'LI4-2', 'LISI4-1', 'LISI4-2'].includes(state.currentFilterType)) {
                if (!(isConfirmado && getGrupo(al.carrera, al.turno) === state.currentFilterType)) return false;
            }
        }
        return true;
    });

    // Ordenar Alfabéticamente: Asegura que la lista resultante siempre esté ordenada por nombre completo
    filtered.sort((a, b) => {
        const nameA = (a.nombre + " " + a.apellido).toLowerCase();
        const nameB = (b.nombre + " " + b.apellido).toLowerCase();
        return nameA.localeCompare(nameB);
    });

    // Manejar el caso donde no hay resultados
    if (filtered.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center">No se encontraron alumnos coincidentes.</td></tr>`;
        return;
    }

    // Generar las filas de la tabla para cada alumno filtrado
    filtered.forEach(al => {
        // Definir el badge visual basado en el estado de asistencia
        let estadoBadge = `<span class="badge badge-pendiente">Pendiente</span>`;
        if (al.asistencia_estado === 1 || al.asistencia_estado === "1") {
            estadoBadge = `<span class="badge badge-confirmado">Sí Asiste</span>`;
        } else if (al.asistencia_estado === 0 || al.asistencia_estado === "0") {
            estadoBadge = `<span class="badge badge-rechazado">No Asistirá</span>`;
        }

        // Simplificar el nombre de la carrera para la visualización en la tabla
        let carreraCorta = al.carrera;
        const carLower = al.carrera.toLowerCase();
        if (carLower.includes("informática") || carLower.includes("informatica")) {
            carreraCorta = "Informática";
        } else if (carLower.includes("ingeniería") || carLower.includes("sistemas")) {
            carreraCorta = "Ingeniería";
        }

        // Crear el elemento de fila (tr) e insertar el HTML con los datos
        const tr = document.createElement("tr");
        tr.innerHTML = `
            <td><strong>${al.numCuenta}</strong></td>
            <td>${al.nombre} ${al.apellido}</td>
            <td><small>${getGrupo(al.carrera, al.turno)}</small></td>
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

/**
 * Establece el tipo de filtro actual y refresca la tabla.
 * @param {string} type - El identificador del filtro (ALL, CONFIRMADOS, etc.)
 */
export function setFilterType(type) {
    state.currentFilterType = type;

    // Mostrar/Ocultar el botón de "Mostrar Todo" dependiendo del filtro
    const btn = document.getElementById("btnMostrarTodo");
    if (btn) {
        if (type === 'ALL') {
            btn.classList.add('d-none');
        } else {
            btn.classList.remove('d-none');
        }
    }

    // Obtener el valor actual de búsqueda y rerenderizar la tabla
    const searchVal = document.getElementById("searchInput") ? document.getElementById("searchInput").value : "";
    renderTable(searchVal);
}

