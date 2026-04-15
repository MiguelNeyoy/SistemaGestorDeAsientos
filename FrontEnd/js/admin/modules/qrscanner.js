import { state } from './state.js';
import { validarQrToken, confirmarLlegadaQr } from './api.js';

let html5QrCode = null;
let qrScannerModal = null;
let qrResultModal = null;
let qrEndpointModal = null;

const audioBeep = new Audio('https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3');

export function initQRModule() {
    console.log("Iniciando Módulo QR funcional...");

    qrScannerModal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
    qrResultModal = new bootstrap.Modal(document.getElementById('qrResultModal'));
    qrEndpointModal = new bootstrap.Modal(document.getElementById('qrEndpointModal'));

    // Botón principal para abrir escáner
    const btnScanner = document.getElementById("btnEscanearQR");
    if (btnScanner) {
        btnScanner.onclick = openQRScanner;
    }

    // Botones de cierre
    const btnsCerrar = ["btnCerrarScanner", "btnDetenerCamara", "btnCerrarEscannerTop", "btnCerrarEscannerCompleto"];
    btnsCerrar.forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.onclick = () => {
                stopQRScanner();
                qrScannerModal.hide();
                qrResultModal.hide();
                qrEndpointModal.hide();
            };
        }
    });

    // Botón de confirmar llegada en modal de resultado
    const btnConfirmar = document.getElementById("btnQrConfirmar");
    if (btnConfirmar) {
        btnConfirmar.onclick = handleConfirmarLlegada;
    }
    
    // Botón opcional para reanudar sin confirmar
    const btnReanudar = document.getElementById("btnReanudarEscanner");
    if (btnReanudar) {
        btnReanudar.onclick = () => {
            qrResultModal.hide();
            if (html5QrCode) html5QrCode.resume();
        };
    }
}

async function openQRScanner() {
    detectDevice();
    qrScannerModal.show();

    // Pequeño delay para que el modal cargue antes de pedir cámara
    setTimeout(() => {
        startCamera();
    }, 500);
}

function detectDevice() {
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    const warning = document.getElementById("qrDesktopWarning");
    if (warning) {
        if (!isMobile) {
            warning.classList.remove("d-none");
        } else {
            warning.classList.add("d-none");
        }
    }
}

function startCamera() {
    if (html5QrCode && html5QrCode.isScanning) {
        return; // Ya está iniciado
    }
    initHtml5Scanner();
}

function initHtml5Scanner() {
    const statusText = document.getElementById("qrScannerStatus");
    if (statusText) statusText.innerText = "Alinea el código QR dentro del recuadro";
    
    html5QrCode = new Html5Qrcode("qrReaderContainer");
    
    const config = { 
        fps: 10, 
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    html5QrCode.start(
        { facingMode: "environment" }, 
        config, 
        onScanSuccess
    ).catch(err => {
        console.error("Error al iniciar cámara:", err);
        if (statusText) statusText.innerText = "Error: No se pudo acceder a la cámara.";
    });
}

function onScanSuccess(decodedText, decodedResult) {
    console.log(`Código escaneado: ${decodedText}`);
    audioBeep.play().catch(e => console.log("Audio interact required"));

    // PAUSAR el escáner (no detener cámara) para mostrar resultado
    if (html5QrCode) {
        html5QrCode.pause();
    }
    
    processQRData(decodedText);
}

function stopQRScanner() {
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
            console.log("Cámara detenida completamente.");
            html5QrCode.clear();
        }).catch(err => console.error("Error al detener cámara:", err));
    }
}

async function processQRData(token) {
    // Mostrar cargando en el modal de resultado si es necesario, 
    // pero idealmente validar antes de abrirlo.
    
    try {
        const tokenAdmin = window.ADMIN_TOKEN;
        const res = await validarQrToken(token, tokenAdmin);

        if (res.success) {
            showStudentResultModal(res.data);
        } else {
            showInlineFeedback(res.message || "Token inválido", "danger");
            // Esperar un poco y reanudar
            setTimeout(() => {
                if (html5QrCode) html5QrCode.resume();
            }, 2000);
        }
    } catch (error) {
        console.error("Error procesando QR:", error);
        alert("Error de conexión al validar QR.");
        if (html5QrCode) html5QrCode.resume();
    }
}

function showStudentResultModal(alumno) {
    document.getElementById("qrResNombre").innerText = `${alumno.nombre} ${alumno.apellido}`.trim();
    document.getElementById("qrResNumCuenta").innerText = alumno.numCuenta;
    document.getElementById("qrResAsiento").innerText = alumno.asiento || "Sin asignar";
    document.getElementById("qrResInvitados").innerText = alumno.cantInvitado || "0";

    // Resetear feedback
    const feedback = document.getElementById("qrFeedback");
    feedback.classList.add("d-none");

    qrResultModal.show();
}

async function handleConfirmarLlegada() {
    const numCuenta = document.getElementById("qrResNumCuenta").innerText;
    const nombre = document.getElementById("qrResNombre").innerText;
    const asiento = document.getElementById("qrResAsiento").innerText;
    
    const btn = document.getElementById("btnQrConfirmar");
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Confirmando...';

    try {
        const tokenAdmin = window.ADMIN_TOKEN;
        const res = await confirmarLlegadaQr(numCuenta, tokenAdmin);

        if (res.success) {
            // Éxito: Mostrar modal de endpoint/confirmación final
            qrResultModal.hide();
            
            // Llenar datos de la modal de éxito
            document.getElementById("endpointNombre").innerText = nombre;
            document.getElementById("endpointAsiento").innerText = asiento;
            
            // Reset de barra de progreso
            const progressBar = document.getElementById("endpointProgress");
            progressBar.style.width = "0%";
            
            qrEndpointModal.show();
            
            // Iniciar animación de barra y cierre automático
            setTimeout(() => {
                progressBar.style.width = "100%";
            }, 50);

            setTimeout(() => {
                qrEndpointModal.hide();
                // REANUDAR escáner para el siguiente alumno
                if (html5QrCode) {
                    html5QrCode.resume();
                    console.log("Escáner reanudado.");
                }
            }, 1500);

        } else {
            showInlineFeedback(res.message, "danger");
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Confirmar Llegada';
        }
    } catch (error) {
        console.error("Error al confirmar:", error);
        showInlineFeedback("Error de servidor al confirmar llegada", "danger");
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Confirmar Llegada';
    }
}

function showInlineFeedback(message, type) {
    const feedback = document.getElementById("qrFeedback");
    if (!feedback) return;

    feedback.innerText = message;
    feedback.className = `alert alert-${type} animate__animated animate__shakeX`;
    feedback.classList.remove("d-none");
    
    // Si es error, auto-ocultar después de un tiempo si se desea
}
