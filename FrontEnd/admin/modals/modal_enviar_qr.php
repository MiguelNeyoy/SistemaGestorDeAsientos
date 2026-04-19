<!-- Modal Enviar QRs -->
<div class="modal fade" id="enviarQRModal" tabindex="-1" aria-labelledby="enviarQRModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="enviarQRModalLabel"><i class="bi bi-envelope-paper me-2"></i>Enviar Correos QR</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEnviarQRs">
                <div class="modal-body">
                    <p class="text-muted small mb-3">Selecciona los criterios para enviar los códigos QR de acceso a los alumnos.</p>
                    
                    <div class="mb-3">
                        <label for="selectCarreraEnvio" class="form-label fw-bold">Carrera</label>
                        <select class="form-select" id="selectCarreraEnvio" required>
                            <option value="">-- Seleccionar --</option>
                            <option value="ALL">Todas las carreras</option>
                            <option value="ING">Ingeniería</option>
                            <option value="INF">Informática</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="selectTurnoEnvio" class="form-label fw-bold">Turno</label>
                        <select class="form-select" id="selectTurnoEnvio" required>
                            <option value="">-- Seleccionar --</option>
                            <option value="ALL">Ambos turnos</option>
                            <option value="M">Matutino</option>
                            <option value="V">Vespertino</option>
                        </select>
                    </div>

                    <div id="envioAlert" class="alert d-none" role="alert"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnProcesarEnvio">
                        <span class="spinner-border spinner-border-sm d-none me-2" id="spinnerEnvio" role="status" aria-hidden="true"></span>
                        <i class="bi bi-send-fill me-1" id="iconEnvio"></i> Enviar Correos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
