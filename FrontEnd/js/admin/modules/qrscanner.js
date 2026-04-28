import { state } from './state.js?v=5';

let html5QrCode = null;
let qrScannerModal = null;
let qrResultModal = null;

// Fake set of already scanned tokens
const scannedTokens = new Set();
const audioBeep = new Audio('https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3');

export function initQRModule() {
    console.log("Iniciando Módulo QR...");

    qrScannerModal = new bootstrap.Modal(document.getElementById('qrScannerModal'));
    qrResultModal = new bootstrap.Modal(document.getElementById('qrResultModal'));

    const btnScanner = document.getElementById("btnEscanearQR");
    if (btnScanner) {
        btnScanner.addEventListener("click", openQRScanner);
    }

    const btnCerrar = document.getElementById("btnCerrarScanner");
    if (btnCerrar) {
        btnCerrar.addEventListener("click", stopQRScanner);
    }
    
    // Additional stop camera button
    const btnDetenerCamara = document.getElementById("btnDetenerCamara");
    if (btnDetenerCamara) {
        btnDetenerCamara.addEventListener("click", () => {
            stopQRScanner();
            qrScannerModal.hide();
        });
    }

    const btnRescan = document.getElementById("btnQrRescan");
    if (btnRescan) {
        btnRescan.addEventListener("click", () => {
            qrResultModal.hide();
            openQRScanner();
        });
    }

    const btnConfirmar = document.getElementById("btnQrConfirmar");
    if (btnConfirmar) {
        btnConfirmar.addEventListener("click", () => {
            qrResultModal.hide();
            setTimeout(() => {
                openQRScanner();
            }, 300);
        });
    }
}

async function openQRScanner() {
    detectDevice();
    qrScannerModal.show();

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
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
            initHtml5Scanner();
        }).catch(() => {
            initHtml5Scanner();
        });
    } else {
        initHtml5Scanner();
    }
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
    audioBeep.play().catch(e => console.log("Audio prevent by browser"));

    stopQRScanner();
    qrScannerModal.hide();
    
    processQRData(decodedText);
}

function stopQRScanner() {
    if (html5QrCode && html5QrCode.isScanning) {
        html5QrCode.stop().then(() => {
            console.log("Cámara detenida.");
        }).catch(err => console.error("Error al detener cámara:", err));
    }
}

// Helper to mock JWT decoding
function parseJwt(token) {
    try {
        const parts = token.split('.');
        if (parts.length !== 3) return null;
        const base64 = parts[1].replace(/-/g, '+').replace(/_/g, '/');
        const jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));
        return JSON.parse(jsonPayload);
    } catch (e) {
        return null;
    }
}

function processQRData(text) {
    let numCuenta = text;
    
    // Check if the token was already scanned (mock DB check)
    if (scannedTokens.has(text)) {
        alert("Este código QR ya ha sido escaneado y marcado previamente.");
        openQRScanner();
        return;
    }
    
    const jwtPayload = parseJwt(text);
    if (jwtPayload && jwtPayload.numCuenta) {
        numCuenta = jwtPayload.numCuenta;
    }

    const alumno = state.allStudentsCache.find(a => a.numCuenta === numCuenta);

    if (alumno) {
        // Mark as scanned for local mock
        scannedTokens.add(text);
        showStudentResultModal(alumno);
    } else {
        alert("Alumno no encontrado en el sistema con cuenta: " + numCuenta);
        openQRScanner(); 
    }
}

function showStudentResultModal(alumno) {
    document.getElementById("qrResNombre").innerText = `${alumno.nombre} ${alumno.apellidoP} ${alumno.apellidoM}`.trim();
    document.getElementById("qrResNumCuenta").innerText = alumno.numCuenta;
    
    let asientoAsignado = alumno.asiento || "-";
    document.getElementById("qrResAsiento").innerText = asientoAsignado;
    
    document.getElementById("qrResInvitados").innerText = alumno.cantInvitado || "0";
    document.getElementById("qrToggleInvitado").checked = false;

    qrResultModal.show();
}
