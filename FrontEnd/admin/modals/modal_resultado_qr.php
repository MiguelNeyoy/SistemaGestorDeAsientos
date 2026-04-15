<!-- Modal de Resultado del Alumno Escaneado -->
<div class="modal fade" id="qrResultModal" tabindex="-1" aria-labelledby="qrResultModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fw-bold" id="qrResultModalLabel">
                    <i class="bi bi-person-badge-fill me-2"></i>Alumno Identificado
                </h5>
                <button type="button" class="btn-close btn-close-white" id="btnCerrarEscannerTop" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="display-6 fw-bold text-dark mb-1" id="qrResNombre">-</div>
                    <span class="badge bg-light text-dark border p-2" id="qrResNumCuenta">-</span>
                </div>

                <div class="row g-3 mb-4 text-center">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small mb-1"><i class="bi bi-grid-3x3-gap me-1"></i>Asiento</div>
                            <div class="fw-bold fs-3 text-primary" id="qrResAsiento">-</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 border">
                            <div class="text-muted small mb-1"><i class="bi bi-people me-1"></i>Invitados</div>
                            <div class="fw-bold fs-3 text-secondary" id="qrResInvitados">0</div>
                        </div>
                    </div>
                </div>
                
                <!-- Feedback Inline -->
                <div id="qrFeedback" class="alert d-none mb-4 animate__animated animate__fadeIn"></div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg py-3 fw-bold shadow-sm" id="btnQrConfirmar">
                        <i class="bi bi-check-circle-fill me-2"></i>Confirmar Llegada
                    </button>
                    <button type="button" class="btn btn-dark btn-lg py-3 fw-bold" id="btnCerrarEscannerCompleto">
                        Cerrar Escáner
                    </button>
                    <button type="button" class="btn btn-link text-muted mt-2" id="btnReanudarEscanner" style="display: none;">
                        <i class="bi bi-arrow-repeat me-1"></i>Ignorar y Seguir Escaneando
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
#qrResultModal .modal-content {
    background: #fff;
}
#qrResultModal .bg-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%) !important;
}
#qrResultModal .btn-success {
    background: linear-gradient(135deg, #198754 0%, #20c997 100%) !important;
    border: none;
}
</style>
