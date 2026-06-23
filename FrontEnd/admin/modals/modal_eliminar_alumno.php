<!-- Modal Eliminar Alumnos -->
<div class="modal fade" id="eliminarAlumnoModal" tabindex="-1" aria-labelledby="eliminarAlumnoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="eliminarAlumnoModalLabel">
                    <i class="bi bi-trash3-fill me-2"></i>
                    Eliminar Alumnos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <div>
                        <strong>&#9888;&#65039; ESTA ACCIÓN ES PERMANENTE.</strong> Se eliminarán físicamente <strong id="deleteCount">0</strong> alumno(s) de la base de datos. Esta operación <strong>no se puede deshacer</strong>.
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Alumnos seleccionados:</label>
                    <div id="deleteList" class="border rounded p-2 bg-light" style="max-height: 150px; overflow-y: auto;">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Escribe <strong>CONFIRMAR</strong> para habilitar la eliminación:</label>
                    <input type="text" class="form-control" id="confirmDeleteInput" placeholder="CONFIRMAR">
                </div>

                <div id="deleteAlert" class="alert d-none" role="alert"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar" disabled>
                    <span class="spinner-border spinner-border-sm d-none me-2" id="spinnerDelete" role="status" aria-hidden="true"></span>
                    Eliminar permanentemente
                </button>
            </div>
        </div>
    </div>
</div>
