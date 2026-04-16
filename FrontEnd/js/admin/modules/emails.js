let alumnosCargados = [];

export function setupEmailFormListener() {
    const form = document.getElementById("formEnviarQRs");
    if (!form) return;

    const btnBuscar = document.getElementById("btnBuscarAlumnos");
    const btnEnvio = document.getElementById("btnProcesarEnvio");
    const checkTodos = document.getElementById("seleccionarTodos");
    const listaAlumnos = document.getElementById("listaAlumnos");

    btnBuscar?.addEventListener("click", cargarAlumnos);
    checkTodos?.addEventListener("change", toggleSeleccionarTodos);
    
    listaAlumnos?.addEventListener("change", (e) => {
        if (e.target.classList.contains("alumno-checkbox")) {
            actualizarContador();
            actualizarBotonEnvio();
        }
    });

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        await enviarCorreos();
    });
}

async function cargarAlumnos() {
    const listaAlumnos = document.getElementById("listaAlumnos");
    const btnBuscar = document.getElementById("btnBuscarAlumnos");
    
    const carrera = document.getElementById("selectCarreraEnvio")?.value || "ALL";
    const turno = document.getElementById("selectTurnoEnvio")?.value || "ALL";
    const estado = document.getElementById("selectEstadoEnvio")?.value || "todos";

    btnBuscar.disabled = true;
    btnBuscar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Buscando...';

    listaAlumnos.innerHTML = `
        <div class="text-center py-4">
            <span class="spinner-border spinner-border-sm me-2"></span>
            Cargando alumnos...
        </div>
    `;

    try {
        const response = await fetch(`${window.BASE_API_URL}/admin/alumnos`, {
            headers: {
                "Authorization": `Bearer ${window.ADMIN_TOKEN}`
            }
        });

        if (!response.ok) {
            throw new Error("Error al obtener alumnos");
        }

        let alumnos = await response.json();
        
        if (carrera !== "ALL") {
            alumnos = alumnos.filter(a => a.carrera === carrera);
        }
        if (turno !== "ALL") {
            alumnos = alumnos.filter(a => a.turno === turno);
        }
        if (estado === "confirmados") {
            alumnos = alumnos.filter(a => a.asistencia_estado == 1);
        } else if (estado === "no_confirmados") {
            alumnos = alumnos.filter(a => a.asistencia_estado != 1);
        }

        alumnosCargados = alumnos;
        renderizarListaAlumnos(alumnos);

    } catch (error) {
        console.error("Error cargando alumnos:", error);
        listaAlumnos.innerHTML = `
            <div class="text-center text-danger py-4">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Error al cargar alumnos
            </div>
        `;
    } finally {
        btnBuscar.disabled = false;
        btnBuscar.innerHTML = '<i class="bi bi-search me-1"></i>Buscar alumnos';
    }
}

function renderizarListaAlumnos(alumnos) {
    const listaAlumnos = document.getElementById("listaAlumnos");
    const checkTodos = document.getElementById("seleccionarTodos");

    if (alumnos.length === 0) {
        listaAlumnos.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="bi bi-people me-1"></i>
                No hay alumnos que coincidan con los filtros
            </div>
        `;
        checkTodos.checked = false;
        actualizarContador();
        actualizarBotonEnvio();
        return;
    }

    listaAlumnos.innerHTML = alumnos.map(alumno => {
        const confirmado = alumno.asistencia_estado == 1;
        const badge = confirmado 
            ? '<span class="badge bg-success ms-2">Confirmado</span>'
            : '<span class="badge bg-secondary ms-2">Sin confirmar</span>';
        
        const iconoCorreo = alumno.email 
            ? '<i class="bi bi-envelope text-success me-1" title="Tiene correo"></i>'
            : '<i class="bi bi-envelope-x text-danger me-1" title="Sin correo"></i>';

        return `
            <label class="list-group-item d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <input class="form-check-input me-2 alumno-checkbox" type="checkbox" 
                           value="${alumno.numCuenta}"
                           data-nombre="${alumno.nombre}"
                           data-apellido="${alumno.apellido}"
                           data-email="${alumno.email || ''}"
                           data-cantinvitado="${alumno.cantInvitado || 0}">
                    <div>
                        <strong>${alumno.nombre} ${alumno.apellido}</strong>
                        <small class="text-muted d-block">${alumno.numCuenta}</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    ${iconoCorreo}
                    ${badge}
                </div>
            </label>
        `;
    }).join('');

    checkTodos.checked = false;
    actualizarContador();
    actualizarBotonEnvio();
}

function toggleSeleccionarTodos() {
    const checkTodos = document.getElementById("seleccionarTodos");
    const checkboxes = document.querySelectorAll(".alumno-checkbox");
    
    checkboxes.forEach(cb => cb.checked = checkTodos.checked);
    actualizarContador();
    actualizarBotonEnvio();
}

function actualizarContador() {
    const checkboxes = document.querySelectorAll(".alumno-checkbox:checked");
    const contador = document.getElementById("contadorSeleccionados");
    const sinCorreo = Array.from(checkboxes).filter(cb => !cb.dataset.email);
    
    let texto = `${checkboxes.length} alumno${checkboxes.length !== 1 ? 's' : ''} seleccionado${checkboxes.length !== 1 ? 's' : ''}`;
    
    if (sinCorreo.length > 0) {
        texto += ` <span class="text-warning">(${sinCorreo.length} sin correo)</span>`;
    }
    
    if (contador) {
        contador.innerHTML = texto;
    }
}

function actualizarBotonEnvio() {
    const checkboxes = document.querySelectorAll(".alumno-checkbox:checked");
    const btnEnvio = document.getElementById("btnProcesarEnvio");
    const sinCorreo = Array.from(checkboxes).filter(cb => !cb.dataset.email);
    
    if (checkboxes.length === 0) {
        btnEnvio.disabled = true;
    } else if (sinCorreo.length > 0) {
        btnEnvio.disabled = false;
        btnEnvio.classList.remove("btn-success");
        btnEnvio.classList.add("btn-warning");
    } else {
        btnEnvio.disabled = false;
        btnEnvio.classList.remove("btn-warning");
        btnEnvio.classList.add("btn-success");
    }
}

async function enviarCorreos() {
    const checkboxes = document.querySelectorAll(".alumno-checkbox:checked");
    const btnEnvio = document.getElementById("btnProcesarEnvio");
    const spinner = document.getElementById("spinnerEnvio");
    const icon = document.getElementById("iconEnvio");
    const textoEnvio = document.getElementById("textoEnvio");
    const alertBox = document.getElementById("envioAlert");

    if (checkboxes.length === 0) {
        showAlert("Selecciona al menos un alumno", "warning");
        return;
    }

    const alumnos = Array.from(checkboxes).map(cb => ({
        numCuenta: cb.value,
        nombre: cb.dataset.nombre,
        apellido: cb.dataset.apellido,
        email: cb.dataset.email,
        cantInvitado: parseInt(cb.dataset.cantinvitado) || 0
    }));

    const sinCorreo = alumnos.filter(a => !a.email);
    if (sinCorreo.length > 0) {
        const confirmar = confirm(
            `${sinCorreo.length} alumno${sinCorreo.length !== 1 ? 's' : ''} no tiene${sinCorreo.length === 1 ? '' : 'n'} correo registrado.\n\n` +
            `¿Deseas continuar de todos modos?\n\n` +
            `Alumnos sin correo:\n${sinCorreo.map(a => `- ${a.nombre} ${a.apellido}`).join('\n')}`
        );
        if (!confirmar) return;
    }

    btnEnvio.disabled = true;
    spinner.classList.remove("d-none");
    icon.classList.add("d-none");
    textoEnvio.textContent = "Enviando...";
    alertBox.classList.add("d-none");

    try {
        const response = await fetch(`${window.BASE_API_URL}/admin/enviar-qrs`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Authorization": `Bearer ${window.ADMIN_TOKEN}`
            },
            body: JSON.stringify({ alumnos })
        });

        const data = await response.json();

        if (data.success) {
            const { enviados, fallidos } = data.data;
            let mensaje = `<strong>✓ Proceso completado</strong><br>`;
            mensaje += `Enviados exitosamente: ${enviados.length}<br>`;
            mensaje += `Fallidos: ${fallidos.length}`;
            
            if (fallidos.length > 0) {
                mensaje += `<br><small>Fallidos:</small><br>`;
                mensaje += `<small>${fallidos.map(f => `• ${f.nombre}: ${f.error}`).join('<br>')}</small>`;
            }
            
            showAlert(mensaje, "success");
            
            document.getElementById("seleccionarTodos").checked = false;
            await cargarAlumnos();
        } else {
            showAlert(data.message || "No se pudieron enviar los correos", "danger");
        }
    } catch (error) {
        console.error("Error enviando QRs:", error);
        showAlert("Ocurrió un error en la conexión", "danger");
    } finally {
        btnEnvio.disabled = false;
        spinner.classList.add("d-none");
        icon.classList.remove("d-none");
        textoEnvio.textContent = "Enviar Correos";
    }
}

function showAlert(message, type) {
    const alertBox = document.getElementById("envioAlert");
    alertBox.className = `alert alert-${type} mt-3 mb-0`;
    alertBox.innerHTML = message;
    alertBox.classList.remove("d-none");
}
