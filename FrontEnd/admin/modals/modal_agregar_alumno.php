<!-- Modal Agregar Alumno -->
<div class="modal fade" id="agregarAlumnoModal" tabindex="-1" aria-labelledby="agregarAlumnoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="agregarAlumnoModalLabel"><i class="bi bi-person-plus-fill me-2"></i>Agregar Alumno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAgregarAlumno">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="addNumCuenta" class="form-label">Número de Cuenta</label>
                        <input type="text" class="form-control" id="addNumCuenta" placeholder="Ej: 20241234" required>
                    </div>

                    <div class="mb-3">
                        <label for="addNombre" class="form-label">Nombre Completo</label>
                        <input type="text" class="form-control" id="addNombre" placeholder="Nombre completo del alumno" required>
                    </div>

                    <div class="mb-3">
                        <label for="addCarrera" class="form-label">Carrera/Turno</label>
                        <select class="form-select" id="addCarrera" required>
                            <option value="">Seleccionar Carrera</option>
                            <option value="LI 4-1">LI 4-1 (Matutino)</option>
                            <option value="LI 4-2">LI 4-2 (Vespertino)</option>
                            <option value="LISI 4-1">LISI 4-1 (Matutino)</option>
                            <option value="LISI 4-2">LISI 4-2 (Vespertino)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="addCorreo" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="addCorreo" placeholder="correo@ejemplo.com">
                    </div>

                    <div class="mb-3">
                        <label for="addInvitados" class="form-label">Número de Invitados</label>
                        <input type="number" class="form-control" id="addInvitados" min="0" max="4" value="0">
                        <small class="text-muted">Máximo 4 invitados permitidos.</small>
                    </div>

                    <div id="addAlert" class="alert d-none" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnGuardarNuevo">
                        <span class="spinner-border spinner-border-sm d-none me-2" id="spinnerAdd" role="status" aria-hidden="true"></span>
                        Registrar Alumno
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
