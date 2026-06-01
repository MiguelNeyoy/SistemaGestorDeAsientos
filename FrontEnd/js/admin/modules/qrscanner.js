import { validarQR as apiValidarQR, marcarQR as apiMarcarQR } from './api.js';
import { toast } from '../../core/toast.js';
import { refreshData } from './dashboard.js';

/**
 * QR Scanner module.
 * Delegates validation to the server.
 */

let scanner = null;
let scannerPaused = false;

export function initQRScanner() {
    const modal = document.getElementById('qrScannerModal');
    if (!modal) return;

    modal.addEventListener('shown.bs.modal', startScanner);
    modal.addEventListener('hidden.bs.modal', pauseScanner);
}

function startScanner() {
    const statusEl = document.getElementById('qrScannerStatus');

    if (scanner) {
        try {
            if (scanner.getState) {
                const state = scanner.getState();
                if (state === Html5QrcodeScannerState.PAUSED) {
                    scanner.resume();
                    scannerPaused = false;
                    return;
                }

                if (state === Html5QrcodeScannerState.SCANNING) {
                    return;
                }
            } else if (scannerPaused) {
                scanner.resume();
                scannerPaused = false;
                return;
            }
        } catch (err) {
            console.warn("Error resuming scanner:", err);
        }
    }

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
    ).then(() => {
        scannerPaused = false;
    }).catch(err => {
        console.error("Error starting scanner:", err);
        if (statusEl) statusEl.textContent = "Error al iniciar la cámara.";
        toast.error("No se pudo acceder a la cámara.");
    });
}

function pauseScanner() {
    if (scanner) {
        try {
            scanner.pause(true);
            scannerPaused = true;
            console.log("Scanner paused.");
        } catch (err) {
            console.warn("Error pausing scanner:", err);
        }
    }
}

function stopScanner() {
    if (scanner) {
        scanner.stop().then(() => {
            scanner = null;
            scannerPaused = false;
            console.log("Scanner stopped.");
        }).catch(err => console.warn("Error stopping scanner:", err));
    }
}

async function onScanSuccess(decodedText) {
    // 1. Pause scanner immediately to avoid multiple reads
    pauseScanner();

    // 2. Hide scanner modal robustly
    const modalEl = document.getElementById('qrScannerModal');
    if (modalEl) {
        const bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
        bsModal.hide();
    }

    toast.info("Validando código...");

    try {
        // 3. Validate on server (only get data, don't mark yet)
        const result = await apiValidarQR(decodedText);

        if (result.success) {
            showResultModal(result.data, decodedText);
        } else {
            toast.error(result.message || "Código inválido o ya utilizado.");
        }
    } catch (error) {
        toast.error("Error de comunicación con el servidor.");
    }
}

function onScanFailure(error) {
    // Silently ignore normal scan failures
}

let tokenActual = null;

function showResultModal(alumno, token) {
    tokenActual = token;
    const resModal = new bootstrap.Modal(document.getElementById('qrResultModal'));

    document.getElementById('qrResNombre').textContent = `${alumno.apellido} ${alumno.nombre}`;
    document.getElementById('qrResNumCuenta').textContent = alumno.numCuenta;
    document.getElementById('qrResAsiento').textContent = alumno.idAsiento || 'Sin asignar';
    document.getElementById('qrResInvitados').textContent = alumno.cantInvitado || 0;

    resModal.show();
}

export async function confirmarLlegada() {
    if (!tokenActual) return;

    try {
        const result = await apiMarcarQR(tokenActual);
        
        if (result.success) {
            toast.success("Pase marcado como utilizado");
            const resModal = bootstrap.Modal.getInstance(document.getElementById('qrResultModal'));
            resModal.hide();
            refreshData();
            tokenActual = null;
        } else {
            toast.error(result.message || "Error al marcar el pase");
        }
    } catch (error) {
        toast.error("Error de comunicación con el servidor");
    }
}

export function reescanearQR() {
    const resModal = bootstrap.Modal.getInstance(document.getElementById('qrResultModal'));
    resModal.hide();
    tokenActual = null;
    
    const scannerModal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
    scannerModal.show();
}

window.confirmarLlegada = confirmarLlegada;
window.reescanearQR = reescanearQR;
