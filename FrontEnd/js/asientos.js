import { pintarAsiento } from './shared/seat-renderer.js';

/**
 * Seat map rendering logic.
 */

document.addEventListener("DOMContentLoaded", () => {
    const data = window.__SEAT_DATA__;
    if (!data) return;

    const envoltura = document.querySelector('.mapa-envoltura');
    const CONFIG_ZOOM_COMPLETO = { scale: 0.6, x: 0, y: 0 };

    function esDispositivoMovil() {
        return window.innerWidth <= 576;
    }

    function aplicarVistaCompleta() {
        if (!envoltura) return;
        if (esDispositivoMovil()) {
            envoltura.style.transform = '';
            return;
        }
        envoltura.style.transform = `scale(${CONFIG_ZOOM_COMPLETO.scale}) translate(${CONFIG_ZOOM_COMPLETO.x}px, ${CONFIG_ZOOM_COMPLETO.y}px)`;
    }

    aplicarVistaCompleta();

    const configRenderer = {
        userType: data.tipoUsuario,
        occupiedSeats: data.asientosOcupados || [],
        studentSeat: data.miAsiento,
        groupSeats: data.asientosGrupo || []
    };

    // ===============================
    //  ZONA SUPERIOR
    // ===============================
    const zonaSuperior = document.querySelector('.zona-superior');
    const letrasSuperior = "KLM";

    if (zonaSuperior) {
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