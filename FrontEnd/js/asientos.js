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
            cursor: 'default',
            touchAction: 'none'
        });

        // Zoom con la rueda del mouse + Ctrl
        contenedor.addEventListener('wheel', (event) => {
            if (!event.ctrlKey) return;
            event.preventDefault();
            panzoom.zoomWithWheel(event);
        }, { passive: false });

        // Centrar inicialmente con auto-foco en asiento del alumno
        requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            let panX = 0, panY = 0;

            if (data.miAsiento && data.tipoUsuario === 'alumno') {
                const seatEl = Array.from(document.querySelectorAll('.asiento'))
                    .find(el => el.textContent.trim() === data.miAsiento);

                if (seatEl) {
                    const envolturaRect = envoltura.getBoundingClientRect();
                    const seatRect = seatEl.getBoundingClientRect();

                    const seatCenterX = seatRect.left + seatRect.width / 2 - envolturaRect.left;
                    const seatCenterY = seatRect.top + seatRect.height / 2 - envolturaRect.top;

                    const envCenterX = envolturaRect.width / 2;
                    const envCenterY = envolturaRect.height / 2;

                    panX = envCenterX - seatCenterX;
                    panY = envCenterY - seatCenterY;
                }
            }

            panzoom.zoom(1.2, { animate: true });
            panzoom.pan(panX, panY);
        });
        });
    }

    const isPublished = data.asignacionPublicada === true || data.asignacionPublicada === '1';
    const configRenderer = {
        userType: data.tipoUsuario,
        occupiedSeats: new Set(data.asientosOcupados || []),
        confirmedSeats: new Set(data.asientosConfirmados || []),
        scannedSeats: new Set(data.asientosEscaneados || []),
        studentSeat: isPublished ? data.miAsiento : null,
        groupSeats: new Set(data.asientosGrupo || [])
    };

    function addSectionHeaders(container, secciones, labels) {
        const fila = document.createElement('div');
        fila.classList.add('fila');
        secciones.forEach((sec, i) => {
            const div = document.createElement('div');
            div.classList.add('seccion', 'section-header');
            for (let n = sec.inicio; n <= sec.fin; n++) {
                const h = document.createElement('span');
                h.classList.add('hueco');
                div.appendChild(h);
            }
            const label = document.createElement('span');
            label.classList.add('section-label');
            label.textContent = labels[i];
            div.appendChild(label);
            fila.appendChild(div);
        });
        container.insertBefore(fila, container.firstChild);
    }

    // ===============================
    //  ZONA SUPERIOR
    // ===============================
    const zonaSuperior = document.querySelector('.zona-superior');

    if (zonaSuperior) {
        const configZS = THEATER_CONFIG.zonaSuperior;
        addSectionHeaders(zonaSuperior, configZS.secciones, [
            'Sup. Izq.', 'Sup. Central', 'Sup. Der.'
        ]);
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
        addSectionHeaders(teatro, configT.seccionesNormales, [
            'Izquierda', 'Central', 'Derecha'
        ]);
        configT.letras.forEach(letra => {
            const filaDiv = document.createElement('div');
            filaDiv.classList.add('fila');

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