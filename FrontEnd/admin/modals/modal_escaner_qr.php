<!-- Modal de Escáner QR -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" aria-labelledby="qrScannerModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="qrScannerModalLabel"><i class="bi bi-qr-code-scan me-2"></i>Escanear QR Alumno</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" id="btnCerrarScanner"></button>
            </div>
            <div class="modal-body p-0 bg-light">
                <div id="qrDesktopWarning" class="alert alert-warning m-3 shadow-sm d-none">
                    <i class="bi bi-laptop me-2"></i><strong>Recomendación:</strong> Para una mejor experiencia, usa este escáner desde un dispositivo móvil.
                </div>
                <div id="qrReaderContainer" style="width: 100%; min-height: 300px; background: #000;"></div>
                <div class="p-3 text-center">
                    <p id="qrScannerStatus" class="mb-0 text-secondary small fw-bold">Alinea el código QR dentro del recuadro</p>
                </div>
            </div>
        </div>
    </div>
</div>
