<!-- Modal de Resultado del Alumno Escaneado -->
<div class="modal fade" id="qrResultModal" tabindex="-1" aria-labelledby="qrResultModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="qrResultModalLabel"><i class="bi bi-person-badge-fill me-2"></i>Alumno Identificado</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="display-6 fw-bold text-primary mb-1" id="qrResNombre">-</div>
                    <span class="badge bg-light text-dark border p-2" id="qrResNumCuenta">-</span>
                </div>

                <div class="list-group list-group-flush border rounded-3 mb-4">
                    <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div><i class="bi bi-grid-3x3-gap me-2 text-muted"></i>Asiento</div>
                        <span class="fw-bold fs-5" id="qrResAsiento">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                        <div><i class="bi bi-people me-2 text-muted"></i>Invitados</div>
                        <span class="fw-bold fs-5" id="qrResInvitados">0</span>
                    </div>
                </div>

                <div class="form-check form-switch mb-4 p-3 bg-light rounded-3 border">
                    <input class="form-check-input ms-0 me-3" type="checkbox" role="switch" id="qrToggleInvitado" style="width: 3em; height: 1.5em; cursor: pointer;">
                    <label class="form-check-label fw-bold" for="qrToggleInvitado" style="cursor: pointer;">¿Llega con acompañante?</label>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary btn-lg py-3 fw-bold" id="btnQrConfirmar">
                        <i class="bi bi-check-circle-fill me-2"></i>Confirmar Llegada
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnQrRescan">
                        <i class="bi bi-arrow-repeat me-1"></i>Volver a Escanear
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
