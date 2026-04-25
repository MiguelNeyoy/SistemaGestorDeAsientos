/**
 * LÓGICA DE RENDERIZADO Y SCROLL DEL TEATRO
 * Desktop: Selector de zonas con zoom
 * Móvil: Scroll nativo con asientos grandes
 */

document.addEventListener("DOMContentLoaded", () => {
    
    const envoltura = document.querySelector('.mapa-envoltura');
    const selectZona = document.getElementById('selectZona');

    const CONFIG_ZOOM = {
        "todos": {
            scale: 0.35,
            x: 0,
            y: 0
        },
        "superior": {
            scale: 1.0,
            x: 0,
            y: 50
        },
        "inferior": {
            scale: 0.8,
            x: 0,
            y: -500
        }
    };

    function esDispositivoMovil() {
        return window.innerWidth <= 576;
    }

    function aplicarTransformacion(slug) {
        if (esDispositivoMovil()) {
            envoltura.style.transform = '';
            return;
        }

        const conf = CONFIG_ZOOM[slug] || CONFIG_ZOOM["todos"];
        envoltura.style.transform = `scale(${conf.scale}) translate(${conf.x}px, ${conf.y}px)`;
    }

    if (selectZona) {
        selectZona.addEventListener('change', (e) => {
            aplicarTransformacion(e.target.value);
        });
        
        aplicarTransformacion("todos");
    }

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
                            asiento.classList.add('confirmado');
                        }
                        if (window.TIPO_USUARIO === "admin" && window.ASIENTOS_OCUPADOS?.includes(idAsiento)) {
                            asiento.classList.add('confirmado');
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
    asiento.classList.add('mi-asiento'); // verde
}

if (window.TIPO_USUARIO === "admin" && window.ASIENTOS_OCUPADOS?.includes(idAsiento)) {
    asiento.classList.add('ocupado'); // rojo
}

                    secDiv.appendChild(asiento);
                }
                filaDiv.appendChild(secDiv);
            });
            teatro.appendChild(filaDiv);
        }
    }
});
