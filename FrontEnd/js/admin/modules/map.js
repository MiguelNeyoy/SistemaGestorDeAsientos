import { state } from '../store/state.js';
import { toast } from '../../core/toast.js';

/**
 * Management of the seating map view and its controls.
 */

let currentEvento = 'li';
let zoom = 1;

export function initMap() {
    const btnZoomIn = document.getElementById('btnZoomIn');
    const btnZoomOut = document.getElementById('btnZoomOut');
    const btnResetZoom = document.getElementById('btnResetZoom');
    const selectEvento = document.getElementById('selectEventoAsientos');

    if (btnZoomIn) btnZoomIn.onclick = () => updateZoom(0.1);
    if (btnZoomOut) btnZoomOut.onclick = () => updateZoom(-0.1);
    if (btnResetZoom) btnResetZoom.onclick = () => resetZoom();
    
    if (selectEvento) {
        selectEvento.onchange = (e) => {
            show(e.target.value);
        };
    }
}

export function show(evento = 'li') {
    console.log(`Showing map for event: ${evento}`);
    currentEvento = evento;
    const tableContainer = document.getElementById('table-container');
    const asientosContainer = document.getElementById('asientos-container');
    const asientosControls = document.getElementById('asientos-controls');
    const iframe = document.getElementById('asientosIframe');
    
    if (tableContainer) tableContainer.classList.add('admin-hidden');
    if (asientosControls) asientosControls.classList.remove('admin-hidden');
    if (asientosContainer) asientosContainer.classList.remove('admin-hidden');

    if (iframe) {
        iframe.src = `../asientos.php?evento=${evento}`;
        resetZoom();
    }
}

export function hide() {
    console.log("Hiding map...");
    const tableContainer = document.getElementById('table-container');
    const asientosContainer = document.getElementById('asientos-container');
    const asientosControls = document.getElementById('asientos-controls');

    if (tableContainer) tableContainer.classList.remove('admin-hidden');
    if (asientosContainer) asientosContainer.classList.add('admin-hidden');
    if (asientosControls) asientosControls.classList.add('admin-hidden');
}

function updateZoom(delta) {
    zoom = Math.min(Math.max(zoom + delta, 0.3), 2.0);
    applyTransform();
}

function resetZoom() {
    zoom = 1.0; // Cambiado a 1.0 para asegurar visibilidad inicial
    applyTransform();
}

function applyTransform() {
    const iframe = document.getElementById('asientosIframe');
    const zoomText = document.getElementById('zoomLevel');
    
    if (iframe) {
        iframe.style.transform = `scale(${zoom})`;
        iframe.style.transformOrigin = 'top center';
    }
    
    if (zoomText) {
        zoomText.textContent = `${Math.round(zoom * 100)}%`;
    }
}
