import { API_URL, TOKEN } from '../config.js';
import { coreFetch } from '../core/api.js';

/**
 * Logic for the student's confirmation/status page.
 * Handles dynamic QR generation.
 */

document.addEventListener('DOMContentLoaded', () => {
    const data = window.__ALUMNO_DATA__;
    if (!data) return;

    const { asistira, numCuenta } = data;

    // Only generate QR if the student is confirmed
    if (asistira == 1 || asistira === "Si") {
        initQR(numCuenta);
    }
});

async function initQR(numCuenta) {
    const container = document.getElementById('contenedor-qr');
    if (!container) return;

    try {
        // Fetch the dynamic QR token from the server
        const response = await coreFetch('/alumnos/qr');
        const result = await response.json();

        if (result.success && result.data && result.data.token) {
            renderQR(container, result.data.token);
        } else {
            renderPilotMessage(container);
        }
    } catch (error) {
        console.error("Error fetching QR token:", error);
        container.innerHTML = '<p class="text-danger">Error al cargar el pase de acceso.</p>';
    }
}

function renderQR(container, token) {
    container.innerHTML = ''; // Clear loading
    
    // Create a title
    const title = document.createElement('h4');
    title.textContent = "Tu Pase de Acceso";
    title.style.textAlign = 'center';
    title.style.marginBottom = '1rem';
    container.appendChild(title);

    // Create the QR target div
    const qrDiv = document.createElement('div');
    qrDiv.id = "qrcode";
    qrDiv.style.display = 'flex';
    qrDiv.style.justifyContent = 'center';
    container.appendChild(qrDiv);

    // Generate QR using the library
    new QRCode(qrDiv, {
        text: token,
        width: 256,
        height: 256,
        colorDark : "#003B71",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    const hint = document.createElement('p');
    hint.textContent = "Presenta este código al ingresar al evento.";
    hint.style.fontSize = '0.85rem';
    hint.style.marginTop = '1rem';
    hint.style.textAlign = 'center';
    hint.style.color = '#666';
    container.appendChild(hint);
}

function renderPilotMessage(container) {
    container.innerHTML = `
        <div class="pilot-message">
            <h4>Pase de Acceso</h4>
            <p>Tu grupo aún no ha sido habilitado para el acceso por QR (Prueba Piloto).</p>
            <p class="small">Se te notificará por correo cuando tu pase esté disponible.</p>
        </div>
    `;
}
