<div class="modal fade" id="enviarQRModal" tabindex="-1" aria-labelledby="enviarQRModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="enviarQRModalLabel">
                    <i class="bi bi-envelope-paper me-2"></i>Enviar Códigos QR por Correo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEnviarQRs">
                <div class="modal-body">
                    <p class="text-muted small mb-3">Selecciona los criterios y elige los alumnos a los que deseas enviar su código QR.</p>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="selectCarreraEnvio" class="form-label fw-bold">Carrera</label>
                            <select class="form-select" id="selectCarreraEnvio">
                                <option value="ALL">Todas</option>
                                <option value="ING">Ingeniería</option>
                                <option value="INF">Informática</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="selectTurnoEnvio" class="form-label fw-bold">Turno</label>
                            <select class="form-select" id="selectTurnoEnvio">
                                <option value="ALL">Ambos</option>
                                <option value="M">Matutino</option>
                                <option value="V">Vespertino</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="selectEstadoEnvio" class="form-label fw-bold">Estado</label>
                            <select class="form-select" id="selectEstadoEnvio">
                                <option value="todos">Todos</option>
                                <option value="confirmados">Solo confirmados</option>
                                <option value="no_confirmados">No confirmados</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnBuscarAlumnos">
                            <i class="bi bi-search me-1"></i>Buscar alumnos
                        </button>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center py-2">
                            <span class="fw-bold">Alumnos</span>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="seleccionarTodos">
                                <label class="form-check-label small" for="seleccionarTodos">
                                    Seleccionar todos
                                </label>
                            </div>
                        </div>
                        <div class="card-body p-0" style="max-height: 250px; overflow-y: auto;">
                            <div id="listaAlumnos" class="list-group list-group-flush">
                                <div class="text-center text-muted py-4">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Usa "Buscar alumnos" para cargar la lista
                                </div>
                            </div>
                        </div>
                        <div class="card-footer py-2">
                            <small class="text-muted" id="contadorSeleccionados">0 alumnos seleccionados</small>
                        </div>
                    </div>

                    <div id="envioAlert" class="alert d-none" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnProcesarEnvio" disabled>
                        <span class="spinner-border spinner-border-sm d-none me-2" id="spinnerEnvio" role="status" aria-hidden="true"></span>
                        <i class="bi bi-send-fill me-1" id="iconEnvio"></i>
                        <span id="textoEnvio">Enviar Correos</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
