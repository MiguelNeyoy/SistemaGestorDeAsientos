import { state } from './state.js?v=8';
import { updateAlumno } from './api.js?v=8';

let currentEditModal = null;

export function openEditModal(numCuenta) {
    const alumno = state.allStudentsCache.find(a => a.numCuenta === numCuenta);
    if (!alumno) return;

    document.getElementById("editNumCuenta").value = alumno.numCuenta;
    document.getElementById("lblNumCuenta").innerText = alumno.numCuenta;
    document.getElementById("lblNombre").innerText = `${alumno.nombre} ${alumno.apellido}`;
    document.getElementById("editCorreo").value = alumno.email || '';
    
    const asistencia = (alumno.asistencia_estado === 1 || alumno.asistencia_estado === "1") ? "1" : "0";
    const editEstado = document.getElementById("editEstado");
    editEstado.value = asistencia;

    const editInvitados = document.getElementById("editInvitados");
    editInvitados.value = alumno.cantInvitado || 0;

    toggleInvitados(asistencia);
    editEstado.onchange = (e) => toggleInvitados(e.target.value);

    const alertDiv = document.getElementById("editAlert");
    alertDiv.classList.add("d-none");

    if (!currentEditModal) {
        currentEditModal = new window.bootstrap.Modal(document.getElementById('editarAlumnoModal'));
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

export function setupModalFormListener(onSuccessCallback) {
    const formEditar = document.getElementById("formEditarAlumno");
    if (!formEditar) return;
    
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
            const data = await updateAlumno(window.ADMIN_TOKEN, {
                numCuenta: numCuenta,
                correo: correo,
                asistencia_estado: estado,
                num_invitados: invitados
            });

            alertDiv.classList.remove("alert-success", "alert-danger");
            if (data.success) {
                alertDiv.classList.add("alert-success");
                alertDiv.innerText = "Guardado con éxito.";
                alertDiv.classList.remove("d-none");
                
                await onSuccessCallback(); // recargar datos
                
                setTimeout(() => {
                    if(currentEditModal) currentEditModal.hide();
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
