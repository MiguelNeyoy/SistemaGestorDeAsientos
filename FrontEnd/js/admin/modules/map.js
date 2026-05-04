import { state } from '../store/state.js';
import { toast } from '../../core/toast.js';

/**
 * Management of the seating map view and its controls.
 */

let currentEvento = 'li';
let zoom = 1;

export function initMap() {
    const selectEvento = document.getElementById('selectEventoAsientos');
    
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
        // Preservar hideNavbar=1 para evitar redundancia en el panel admin
        iframe.src = `../asientos.php?evento=${evento}&hideNavbar=1`;
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
