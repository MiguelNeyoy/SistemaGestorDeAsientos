import { state } from '../store/state.js';
import { updateAlumno } from './api.js';
import { toast } from '../../core/toast.js';
import { refreshData } from './dashboard.js';

/**
 * Modal module for editing student details.
 */

export function initEditModal() {
    // Listen for the custom event dispatched by the table
    window.addEventListener('open-edit-modal', (e) => {
        const { numCuenta } = e.detail;
        openModal(numCuenta);
    });

    const form = document.getElementById('formEditarAlumno');
    if (form) {
        form.onsubmit = handleUpdate;
    }
}

function openModal(numCuenta) {
    const student = state.students.find(s => s.numCuenta == numCuenta);
    if (!student) return;

    const modalEl = document.getElementById('editarAlumnoModal');
    if (!modalEl) return;

    // Set values for inputs and labels
    document.getElementById('editNumCuenta').value = student.numCuenta;
    document.getElementById('lblNumCuenta').textContent = student.numCuenta;
    document.getElementById('lblNombre').textContent = `${student.apellido} ${student.nombre}`;
    document.getElementById('editCorreo').value = student.email || '';
    document.getElementById('editEstado').value = student.asistencia_estado || 0;
    document.getElementById('editInvitados').value = student.cantInvitado || 0;

    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();
}

async function handleUpdate(e) {
    e.preventDefault();
    
    const formData = {
        numCuenta: document.getElementById('editNumCuenta').value,
        correo: document.getElementById('editCorreo').value,
        asistencia_estado: document.getElementById('editEstado').value,
        num_invitados: document.getElementById('editInvitados').value
    };

    const btn = document.getElementById('btnGuardarEdicion');
    const spinner = document.getElementById('spinnerEdit');
    const alertDiv = document.getElementById('editAlert');

    if (btn) btn.disabled = true;
    if (spinner) spinner.classList.remove('admin-hidden');
    if (alertDiv) alertDiv.classList.add('d-none');

    try {
        const result = await updateAlumno(formData);
        if (result.success) {
            if (alertDiv) {
                alertDiv.className = 'alert alert-success mt-3';
                alertDiv.textContent = 'Alumno actualizado correctamente.';
                alertDiv.classList.remove('d-none');
            }
            toast.success("Alumno actualizado correctamente.");
            
            setTimeout(() => {
                bootstrap.Modal.getInstance(document.getElementById('editarAlumnoModal')).hide();
                refreshData();
            }, 1000);
        } else {
            if (alertDiv) {
                alertDiv.className = 'alert alert-danger mt-3';
                alertDiv.textContent = result.message || "Error al actualizar.";
                alertDiv.classList.remove('d-none');
            }
            toast.error(result.message || "Error al actualizar.");
        }
    } catch (error) {
        toast.error("Error de conexión.");
    } finally {
        if (btn) btn.disabled = false;
        if (spinner) spinner.classList.add('admin-hidden');
    }
}
