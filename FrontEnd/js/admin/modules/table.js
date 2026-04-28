import { state } from './state.js?v=5';
import { getGrupo } from './utils.js?v=5';

/**
 * Función principal para renderizar la tabla de alumnos.
 * @param {string} filterText - Texto de búsqueda ingresado por el usuario.
 */
export function renderTable(filterText = "") {
    // Obtener el cuerpo de la tabla donde se insertarán las filas
    const tbody = document.getElementById("alumnosTableBody");
    if (!tbody) return;

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
            
            // Filtros por evento (LI o LISI)
            if (state.currentFilterType === 'LI') {
                const carLower = (al.carrera || '').toLowerCase();
                if (!(carLower.includes('informática') || carLower.includes('informatica'))) return false;
            }
            if (state.currentFilterType === 'LISI') {
                const carLower = (al.carrera || '').toLowerCase();
                if (!(carLower.includes('ingeniería') || carLower.includes('sistemas'))) return false;
            }
            // Filtros por grupo específico
            if (['LI4-1', 'LI4-2', 'LISI4-1', 'LISI4-2'].includes(state.currentFilterType)) {
                if (!(isConfirmado && getGrupo(al.carrera, al.turno) === state.currentFilterType)) return false;
            }
        }
        return true;
    });

    // Ordenar Alfabéticamente: Asegura que la lista resultante siempre esté ordenada por apellido
    filtered.sort((a, b) => {
        const nameA = (a.apellido + " " + a.nombre).toLowerCase();
        const nameB = (b.apellido + " " + b.nombre).toLowerCase();
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
        let estadoBadge = `<span class="admin-badge admin-badge--pendiente">Pendiente</span>`;
        if (al.asistencia_estado === 1 || al.asistencia_estado === "1") {
            estadoBadge = `<span class="admin-badge admin-badge--confirmado">Confirmado</span>`;
        } else if (al.asistencia_estado === 0 || al.asistencia_estado === "0") {
            estadoBadge = `<span class="admin-badge admin-badge--rechazado">Rechazado</span>`;
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
            <td data-label="No. Cuenta"><strong>${al.numCuenta}</strong></td>
            <td data-label="Nombre">${al.apellido} ${al.nombre}</td>
            <td data-label="Grupo">${getGrupo(al.carrera, al.turno)}</td>
            <td data-label="Invitados" class="admin-text-center">${al.cantInvitado || 0}</td>
            <td data-label="Correo" class="admin-table__email">${al.email || '<span class="admin-text-muted">Sin correo</span>'}</td>
            <td data-label="Asiento" class="admin-text-center">${al.asiento || "-"}</td>
            <td data-label="Estado" class="admin-text-center">${estadoBadge}</td>
            <td data-label="Acciones" class="admin-text-center">
                <div class="admin-action-group">
                    <button type="button" class="admin-btn admin-btn--outline" style="padding: 0.5rem;" title="Editar Alumno" onclick="window.openEditModal('${al.numCuenta}')">
                        <span class="admin-icon admin-icon--edit"></span> <span class="admin-hide-mobile ms-1">Editar</span>
                    </button>
                    <button type="button" class="admin-btn admin-btn--outline" style="padding: 0.5rem;" title="Enviar QR" onclick="alert('Función de enviar QR en desarrollo')">
                        <span class="admin-icon admin-icon--send"></span> <span class="admin-hide-mobile ms-1">Enviar</span>
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

    // Actualizar estado activo en el sidebar
    document.querySelectorAll('.admin-sidebar__link').forEach(link => {
        link.classList.remove('admin-sidebar__link--active');
    });

    // Mapear el tipo a ID de link para activar el correcto
    const filterMap = {
        'ALL': 'link-filter-all',
        'CONFIRMADOS': 'link-filter-confirmados',
        'INVITADOS': 'link-filter-invitados',
        'LI': 'link-filter-li',
        'LISI': 'link-filter-lisi',
        'LI4-1': 'link-filter-li41',
        'LI4-2': 'link-filter-li42',
        'LISI4-1': 'link-filter-lisi41',
        'LISI4-2': 'link-filter-lisi42',
        'RECHAZADOS': 'link-filter-rechazados'
    };

    const activeLinkId = filterMap[type];
    if (activeLinkId && document.getElementById(activeLinkId)) {
        document.getElementById(activeLinkId).classList.add('admin-sidebar__link--active');
    }

    // Mostrar/Ocultar el botón de "Mostrar Todo" dependiendo del filtro
    const btn = document.getElementById("btnMostrarTodo");
    if (btn) {
        if (type === 'ALL') {
            btn.classList.add('admin-hidden');
        } else {
            btn.classList.remove('admin-hidden');
        }
    }

    // Obtener el valor actual de búsqueda y rerenderizar la tabla
    const searchVal = document.getElementById("searchInput") ? document.getElementById("searchInput").value : "";
    renderTable(searchVal);
}

