import { eliminarAlumnos } from './api.js';
import { refreshData } from './dashboard.js';
import { toast } from '../../core/toast.js';

export function initEliminar() {
    const btnEliminar = document.getElementById('btnEliminarAlumnos');
    const modalEl = document.getElementById('eliminarAlumnoModal');
    if (!btnEliminar || !modalEl) return;

    const modal = new bootstrap.Modal(modalEl);
    const confirmInput = document.getElementById('confirmDeleteInput');
    const btnConfirmar = document.getElementById('btnConfirmarEliminar');
    const deleteCount = document.getElementById('deleteCount');
    const deleteList = document.getElementById('deleteList');
    const spinner = document.getElementById('spinnerDelete');
    const deleteAlert = document.getElementById('deleteAlert');

    document.addEventListener('change', (e) => {
        if (e.target.classList.contains('alumno-checkbox')) {
            updateSelectedCount();
        }
    });

    document.addEventListener('click', (e) => {
        if (e.target.id === 'checkboxToggleAll') {
            const checkboxes = document.querySelectorAll('.alumno-checkbox');
            checkboxes.forEach(cb => cb.checked = e.target.checked);
            updateSelectedCount();
        }
    });

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.alumno-checkbox:checked');
        const count = checked.length;
        const span = document.getElementById('selectedCount');
        if (span) span.textContent = count;
        btnEliminar.disabled = count === 0;
    }

    btnEliminar.onclick = () => {
        const checked = document.querySelectorAll('.alumno-checkbox:checked');
        const items = [];

        checked.forEach(cb => {
            const numCuenta = cb.dataset.numcuenta;
            const row = cb.closest('tr');
            const nameCell = row?.querySelector('td:nth-child(3)');
            items.push(`${numCuenta} — ${nameCell?.textContent?.trim() || ''}`);
        });

        deleteCount.textContent = items.length;
        deleteList.innerHTML = items.map(n => `<div class="small">${n}</div>`).join('');

        confirmInput.value = '';
        btnConfirmar.disabled = true;
        deleteAlert.classList.add('d-none');
        spinner?.classList.add('d-none');

        modal.show();
    };

    confirmInput.addEventListener('input', () => {
        btnConfirmar.disabled = confirmInput.value !== 'CONFIRMAR';
    });

    btnConfirmar.onclick = async () => {
        const checked = document.querySelectorAll('.alumno-checkbox:checked');
        const alumnos = Array.from(checked).map(cb => cb.dataset.numcuenta);

        btnConfirmar.disabled = true;
        spinner?.classList.remove('d-none');

        try {
            const result = await eliminarAlumnos(alumnos);
            if (result.success) {
                toast.success(result.message);
                modal.hide();
                document.querySelectorAll('.alumno-checkbox').forEach(cb => cb.checked = false);
                const toggleAll = document.getElementById('checkboxToggleAll');
                if (toggleAll) toggleAll.checked = false;
                updateSelectedCount();
                refreshData();
            } else {
                deleteAlert.classList.remove('d-none');
                deleteAlert.classList.add('alert-danger');
                deleteAlert.textContent = result.message || 'Error al eliminar alumnos';
            }
        } catch (error) {
            deleteAlert.classList.remove('d-none');
            deleteAlert.classList.add('alert-danger');
            deleteAlert.textContent = 'Error de conexión al servidor.';
        } finally {
            spinner?.classList.add('d-none');
            btnConfirmar.disabled = confirmInput.value !== 'CONFIRMAR';
        }
    };

    modalEl.addEventListener('hidden.bs.modal', () => {
        confirmInput.value = '';
        btnConfirmar.disabled = true;
        deleteAlert.classList.add('d-none');
    });
}
