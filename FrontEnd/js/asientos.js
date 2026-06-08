import { pintarAsiento } from './shared/seat-renderer.js';

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

    const configRenderer = {
        userType: data.tipoUsuario,
        occupiedSeats: data.asientosOcupados || [],
        confirmedSeats: data.asientosConfirmados || [],
        scannedSeats: data.asientosEscaneados || [],
        studentSeat: data.miAsiento,
        groupSeats: data.asientosGrupo || []
    };

        // ===============================
        //  ZONA SUPERIOR
        // ===============================
        const zonaSuperior = document.querySelector('.zona-superior');
        const letrasSuperior = "KLM";

        if (zonaSuperior) {
            if (data.tipoUsuario === 'alumno') {
                zonaSuperior.style.display = 'none';
            } else {
                letrasSuperior.split("").reverse().forEach(letra => {
                    const filaDiv = document.createElement('div');
                    filaDiv.classList.add('fila');

                    const secciones = [
                        { inicio: 1, fin: 11 },
                        { inicio: 12, fin: 27 },
                        { inicio: 28, fin: 38 }
                    ];

                    secciones.forEach(sec => {
                        const secDiv = document.createElement('div');
                        secDiv.classList.add('seccion');

                        for (let n = sec.inicio; n <= sec.fin; n++) {
                            const asiento = document.createElement('div');
                            const idAsiento = letra + n;

                            if ((letra === "M" && (n < 12 || n > 27)) ||
                                (letra === "L" && (n >= 17 && n <= 22)) ||
                                (letra === "M" && (n >= 16 && n <= 23))) {

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
        }

        // ===============================
        //  TEATRO
        // ===============================
        const teatro = document.querySelector('.teatro');
        const letrasTeatro = "JIHGFEDCBA";

        if (teatro) {
            for (let f = 0; f < letrasTeatro.length; f++) {
                const filaDiv = document.createElement('div');
                filaDiv.classList.add('fila');

                let secciones = [
                    { inicio: 1, fin: 7 },
                    { inicio: 8, fin: 23 },
                    { inicio: 24, fin: 30 }
                ];

                if (f === (letrasTeatro.length - 10)) {
                    secciones = [{ inicio: 1, fin: 34 }];
                }

                if (data.tipoUsuario === 'alumno') {
                    secciones = [{ inicio: 8, fin: 23 }];
                }

                secciones.forEach(sec => {
                    const secDiv = document.createElement('div');
                    secDiv.classList.add('seccion');

                    for (let n = sec.inicio; n <= sec.fin; n++) {
                        const asiento = document.createElement('div');
                        const idAsiento = letrasTeatro[f] + n;

                        asiento.classList.add('asiento');
                        asiento.textContent = idAsiento;

                        pintarAsiento(asiento, idAsiento, configRenderer);

                        secDiv.appendChild(asiento);
                    }

                    filaDiv.appendChild(secDiv);
                });

                teatro.appendChild(filaDiv);
            }
        }
});