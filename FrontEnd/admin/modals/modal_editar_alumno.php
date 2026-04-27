<!-- Modal Editar Alumno -->
<div class="modal fade" id="editarAlumnoModal" tabindex="-1" aria-labelledby="editarAlumnoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editarAlumnoModalLabel">
                    <span class="admin-icon admin-icon--edit me-2" style="filter: brightness(0) invert(1);"></span>
                    Editar Alumno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarAlumno">
                <div class="modal-body">
                    <input type="hidden" id="editNumCuenta">

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Número de Cuenta</label>
                        <p id="lblNumCuenta" class="mb-0 fw-bold fs-5"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Nombre</label>
                        <p id="lblNombre" class="mb-0 fs-6"></p>
                    </div>

                    <div class="mb-3">
                        <label for="editCorreo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="editCorreo" placeholder="correo@ejemplo.com">
                    </div>

                    <div class="mb-3">
                        <label for="editEstado" class="form-label">Estado de Asistencia</label>
                        <select class="form-select" id="editEstado">
                            <option value="1">Confirmado</option>
                            <option value="0">Rechazado</option>
                        </select>
                    </div>

                    <div class="mb-3" id="containerInvitados">
                        <label for="editInvitados" class="form-label">Número de Invitados</label>
                        <input type="number" class="form-control" id="editInvitados" min="0" max="4" value="0">
                        <small class="text-muted">Máximo 4 invitados permitidos.</small>
                    </div>

                    <div id="editAlert" class="alert d-none" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="admin-btn admin-btn--outline" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="admin-btn admin-btn--primary" id="btnGuardarEdicion">
                        <div class="admin-loader admin-hidden me-2" id="spinnerEdit" style="width:14px;height:14px;border-width:2px;"></div>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
