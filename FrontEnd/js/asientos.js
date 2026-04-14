/**
 * LÓGICA DE RENDERIZADO Y ZOOM PAN DEL TEATRO
 * Desarrollado con JavaScript Vanilla - Enfoque Mobile-First
 */

document.addEventListener("DOMContentLoaded", () => {
    
    // --- 1. DICCIONARIO DE CALIBRACIÓN ---
    // Aquí puedes ajustar manualmente los valores para centrar cada sección
    // scale: Nivel de zoom (1.0 = 100%)
    // x: Movimiento horizontal (px o %)
    // y: Movimiento vertical (px o %)
    const CONFIG_ZOOM = {
        "todos": {
            scale: 0.35, // Se ve pequeño para que quepa todo el teatro
            x: 0,
            y: 0
        },
        "superior": {
            scale: 1.0,  // Zoom 1:1
            x: 0,        // Centrado horizontal
            y: 50        // Bajamos un poco la vista para ver KLM
        },
        "inferior": {
            scale: 0.8,
            x: 0,
            y: -500      // Subimos el mapa para centrar el área de abajo (Teatro)
        }
    };

    const envoltura = document.querySelector('.mapa-envoltura');
    const selectZona = document.getElementById('selectZona');

    /**
     * Aplica la transformación fluida al contenedor del mapa
     */
    function aplicarTransformacion(slug) {
        const conf = CONFIG_ZOOM[slug] || CONFIG_ZOOM["todos"];
        
        // Aplicamos el transform: Combinamos escala y traslación
        // Nota: La transición suave ya está definida en el CSS (.mapa-envoltura)
        envoltura.style.transform = `scale(${conf.scale}) translate(${conf.x}px, ${conf.y}px)`;
    }

    // Escuchar cambios en el selector
    if (selectZona) {
        selectZona.addEventListener('change', (e) => {
            aplicarTransformacion(e.target.value);
        });
        
        // Aplicar vista inicial por defecto
        aplicarTransformacion("todos");
    }

    // --- 2. RENDERIZADO DINÁMICO DE ASIENTOS ---

    // Zona Superior (KLM)
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

                        // Lógica de estados (Uso de variables globales inyectadas desde PHP)
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

    // Planta Baja (Teatro A-J)
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

            if (f === (letrasTeatro.length - 1)) { // Fila A es especial
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
                        asiento.classList.add('confirmado');
                    }
                    if (window.TIPO_USUARIO === "admin" && window.ASIENTOS_OCUPADOS?.includes(idAsiento)) {
                        asiento.classList.add('confirmado');
                    }

                    secDiv.appendChild(asiento);
                }
                filaDiv.appendChild(secDiv);
            });
            teatro.appendChild(filaDiv);
        }
    }
});
