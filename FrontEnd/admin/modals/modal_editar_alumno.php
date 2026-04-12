<!-- Modal Editar Alumno -->
<div class="modal fade" id="editarAlumnoModal" tabindex="-1" aria-labelledby="editarAlumnoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editarAlumnoModalLabel"><i class="bi bi-pencil-square me-2"></i>Editar Alumno</h5>
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
                            <option value="1">Sí Asiste</option>
                            <option value="0">No Asistirá</option>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarEdicion">
                        <span class="spinner-border spinner-border-sm d-none me-2" id="spinnerEdit" role="status" aria-hidden="true"></span>
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
