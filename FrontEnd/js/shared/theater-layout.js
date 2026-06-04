/**
 * Configuration of the theater seat layout geometry.
 */
export const THEATER_CONFIG = {
    zonaSuperior: {
        letras: ["M", "L", "K"], // Se renderizan de atrás hacia adelante
        secciones: [
            { inicio: 1, fin: 11 },
            { inicio: 12, fin: 27 },
            { inicio: 28, fin: 38 }
        ],
        // Condiciones para los asientos invisibles ("huecos")
        esHueco: (letra, n) => {
            return (letra === "M" && (n < 12 || n > 27)) ||
                   (letra === "L" && (n >= 17 && n <= 22)) ||
                   (letra === "M" && (n >= 16 && n <= 23));
        }
    },
    teatro: {
        letras: ["J", "I", "H", "G", "F", "E", "D", "C", "B", "A"], // Filas de atrás hacia adelante
        seccionesNormales: [
            { inicio: 1, fin: 7 },
            { inicio: 8, fin: 23 },
            { inicio: 24, fin: 30 }
        ],
        // La fila J tiene una configuración excepcional
        seccionesFilaJ: [
            { inicio: 1, fin: 34 }
        ]
    }
};
