document.addEventListener("DOMContentLoaded", () => {
    const envoltura = document.querySelector('.mapa-envoltura');
    const selectEvento = document.getElementById('selectEvento');

    const CONFIG_ZOOM_COMPLETO = { scale: 0.6, x: 0, y: 0 };

    function esDispositivoMovil() {
        return window.innerWidth <= 576;
    }

    function aplicarVistaCompleta() {
        if (esDispositivoMovil()) {
            envoltura.style.transform = '';
            return;
        }
        envoltura.style.transform = `scale(${CONFIG_ZOOM_COMPLETO.scale}) translate(${CONFIG_ZOOM_COMPLETO.x}px, ${CONFIG_ZOOM_COMPLETO.y}px)`;
    }

    if (selectEvento && window.TIPO_USUARIO === "admin") {
        selectEvento.addEventListener('change', (e) => {
            let evento = "li"; // por defecto
            if (e.target.value === "superior") {
                evento = "li";   // Evento 1 = LI
            } else if (e.target.value === "inferior") {
                evento = "lisi"; // Evento 2 = LISI
            }
            window.location.href = "asientos.php?evento=" + evento;
        });
    }

    aplicarVistaCompleta();

    // === Renderizado de asientos ===
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

                        if (window.TIPO_USUARIO === "alumno" && idAsiento === window.MI_ASIENTO) {
                            asiento.classList.add('mi-asiento');
                        }

                        if (window.TIPO_USUARIO === "admin") {
                            const info = window.ASIENTOS_INFO.find(a => a.id_asiento === idAsiento);
                            if (info) {
                                if (info.estado === "ocupado") {
                                    if (info.id_asiento.startsWith("A") || info.id_asiento.startsWith("B")) {
                                        asiento.classList.add('grupo-li');   // azul
                                    } else {
                                        asiento.classList.add('grupo-lisi'); // naranja
                                    }
                                }
                                if (info.asignado) {
                                    asiento.classList.add('mi-asiento'); // verde para el asiento propio
                                }
                            }
                        }
                    }
                    secDiv.appendChild(asiento);
                }
                filaDiv.appendChild(secDiv);
            });
            zonaSuperior.appendChild(filaDiv);
        });
    }

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

                    if (window.TIPO_USUARIO === "alumno" && idAsiento === window.MI_ASIENTO) {
                        asiento.classList.add('mi-asiento');
                    }

                    if (window.TIPO_USUARIO === "admin") {
                        const info = window.ASIENTOS_INFO.find(a => a.id_asiento === idAsiento);
                        if (info) {
                            if (info.estado === "ocupado") {
                                if (info.id_asiento.startsWith("A") || info.id_asiento.startsWith("B")) {
                                    asiento.classList.add('grupo-li');   // azul
                                } else {
                                    asiento.classList.add('grupo-lisi'); // naranja
                                }
                            }
                            if (info.asignado) {
                                asiento.classList.add('mi-asiento'); // verde para el asiento propio
                            }
                        }
                    }

                    secDiv.appendChild(asiento);
                }
                filaDiv.appendChild(secDiv);
            });
            teatro.appendChild(filaDiv);
        }
    }
});

