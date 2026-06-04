import { pintarAsiento } from './shared/seat-renderer.js';
import { THEATER_CONFIG } from './shared/theater-layout.js';

/**
 * Seat map rendering logic.
 */

document.addEventListener("DOMContentLoaded", () => {
    const data = window.__SEAT_DATA__;
    if (!data) return;

    const envoltura = document.querySelector('.mapa-envoltura');
    const contenedor = document.querySelector('.contenedor-scroll');

    // Inicializar Panzoom
    if (envoltura && contenedor) {
        const panzoom = Panzoom(envoltura, {
            maxScale: 5,
            minScale: 0.1,
            contain: false, 
            cursor: 'default'
        });

        // Zoom con la rueda del mouse + Ctrl
        contenedor.addEventListener('wheel', (event) => {
            if (!event.ctrlKey) return;
            event.preventDefault();
            panzoom.zoomWithWheel(event);
        });

        // Centrar inicialmente con una escala más alejada
        setTimeout(() => {
            panzoom.zoom(0.5, { animate: false });
            panzoom.pan(0, 0);
        }, 100);
    }

    const configRenderer = {
        userType: data.tipoUsuario,
        occupiedSeats: new Set(data.asientosOcupados || []),
        confirmedSeats: new Set(data.asientosConfirmados || []),
        scannedSeats: new Set(data.asientosEscaneados || []),
        studentSeat: data.miAsiento,
        groupSeats: new Set(data.asientosGrupo || [])
    };

    // ===============================
    //  ZONA SUPERIOR
    // ===============================
    const zonaSuperior = document.querySelector('.zona-superior');

    if (zonaSuperior) {
        const configZS = THEATER_CONFIG.zonaSuperior;
        configZS.letras.forEach(letra => {
            const filaDiv = document.createElement('div');
            filaDiv.classList.add('fila');

            configZS.secciones.forEach(sec => {
                const secDiv = document.createElement('div');
                secDiv.classList.add('seccion');

                for (let n = sec.inicio; n <= sec.fin; n++) {
                    const asiento = document.createElement('div');
                    const idAsiento = letra + n;

                    if (configZS.esHueco(letra, n)) {
                        asiento.classList.add('hueco');
                    } else {
                        asiento.classList.add('asiento');
                        asiento.textContent = idAsiento;
                        pintarAsiento(asiento, idAsiento, configRenderer);
                    }

                    secDiv.appendChild(asiento);
                }

                filaDiv.appendChild(secDiv);
            });

            zonaSuperior.appendChild(filaDiv);
        });
    }

    // ===============================
    //  TEATRO
    // ===============================
    const teatro = document.querySelector('.teatro');

    if (teatro) {
        const configT = THEATER_CONFIG.teatro;
        configT.letras.forEach(letra => {
            const filaDiv = document.createElement('div');
            filaDiv.classList.add('fila');

            // Determinar las secciones a utilizar (especial para fila J)
            const secciones = (letra === 'J' && configT.seccionesFilaJ)
                ? configT.seccionesFilaJ
                : configT.seccionesNormales;

            secciones.forEach(sec => {
                const secDiv = document.createElement('div');
                secDiv.classList.add('seccion');

                for (let n = sec.inicio; n <= sec.fin; n++) {
                    const asiento = document.createElement('div');
                    const idAsiento = letra + n;

                    asiento.classList.add('asiento');
                    asiento.textContent = idAsiento;

                    pintarAsiento(asiento, idAsiento, configRenderer);

                    secDiv.appendChild(asiento);
                }

                filaDiv.appendChild(secDiv);
            });

            teatro.appendChild(filaDiv);
        });
    }
});