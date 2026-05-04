import { validarQR as apiValidarQR } from './api.js';
import { toast } from '../../core/toast.js';
import { refreshData } from './dashboard.js';

/**
 * QR Scanner module.
 * Delegates validation to the server.
 */

let scanner = null;

export function initQRScanner() {
    const modal = document.getElementById('qrScannerModal');
    if (!modal) return;

    modal.addEventListener('shown.bs.modal', startScanner);
    modal.addEventListener('hidden.bs.modal', stopScanner);
}

function startScanner() {
    const statusEl = document.getElementById('qrScannerStatus');

    scanner = new Html5Qrcode("qrReaderContainer");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    scanner.start(
        { facingMode: "environment" },
        config,
        onScanSuccess,
        onScanFailure
    ).catch(err => {
        console.error("Error starting scanner:", err);
        if (statusEl) statusEl.textContent = "Error al iniciar la cámara.";
        toast.error("No se pudo acceder a la cámara.");
    });
}

function stopScanner() {
    if (scanner) {
        scanner.stop().then(() => {
            scanner = null;
            console.log("Scanner stopped.");
        }).catch(err => console.warn("Error stopping scanner:", err));
    }
}

async function onScanSuccess(decodedText) {
    // 1. Stop scanner immediately to avoid multiple reads
    stopScanner();

    // 2. Play beep or visual feedback
    const modal = document.getElementById('qrScannerModal');
    const bsModal = bootstrap.Modal.getInstance(modal);
    bsModal.hide();

    toast.info("Validando código...");

    try {
        // 3. Validate on server
        const result = await apiValidarQR(decodedText);

        if (result.success) {
            toast.success(`Acceso concedido: ${result.data.nombre}`);
            showResultModal(result.data);
            refreshData(); // Refresh table to show updated status
        } else {
            toast.error(result.message || "Código inválido o ya utilizado.");
            // Allow rescanning after a short delay
            setTimeout(() => bsModal.show(), 1000);
        }
    } catch (error) {
        toast.error("Error de comunicación con el servidor.");
    }
}

function onScanFailure(error) {
    // Silently ignore normal scan failures
}

function showResultModal(alumno) {
    // Implementation of showing the success modal with student info
    const resModal = new bootstrap.Modal(document.getElementById('qrResultModal'));

    document.getElementById('qrResNombre').textContent = `${alumno.apellido} ${alumno.nombre}`;
    document.getElementById('qrResNumCuenta').textContent = alumno.numCuenta;
    document.getElementById('qrResAsiento').textContent = alumno.idAsiento || 'Sin asignar';
    document.getElementById('qrResInvitados').textContent = alumno.cantInvitado || 0;

    resModal.show();
}
