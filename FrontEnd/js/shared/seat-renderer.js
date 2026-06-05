/**
 * Shared seat rendering logic.
 */

/**
 * Applies styles to a seat element based on its status.
 * @param {HTMLElement} element - The seat div.
 * @param {string} idAsiento - Seat ID (e.g., 'A1').
 * @param {Object} config - Configuration (userType, occupiedSeats, studentSeat, groupSeats).
 */
/**
 * Checks if a seat identifier exists in the collection (supports Set.has and Array.includes).
 */
const hasSeat = (collection, id) => {
    if (!collection) return false;
    if (typeof collection.has === 'function') return collection.has(id);
    if (Array.isArray(collection)) return collection.includes(id);
    return false;
};

/**
 * Applies styles to a seat element based on its status.
 * @param {HTMLElement} element - The seat div.
 * @param {string} idAsiento - Seat ID (e.g., 'A1').
 * @param {Object} config - Configuration (userType, occupiedSeats, studentSeat, groupSeats).
 */
export function pintarAsiento(element, idAsiento, config = {}) {
    const { 
        userType = 'alumno', 
        occupiedSeats = null, 
        confirmedSeats = null,
        scannedSeats = null,
        studentSeat = null, 
        groupSeats = null 
    } = config;

    // Reset classes
    element.classList.remove('disponible', 'ocupado', 'mi-asiento', 'grupo', 'confirmado', 'escaneado');
    element.classList.add('disponible');

    if (userType === 'alumno') {
        if (idAsiento === studentSeat) {
            element.classList.remove('disponible');
            element.classList.add('mi-asiento');
        } else if (hasSeat(scannedSeats, idAsiento)) {
            element.classList.remove('disponible');
            element.classList.add('escaneado');
        }
    } else if (userType === 'admin') {
        if (hasSeat(scannedSeats, idAsiento)) {
            element.classList.remove('disponible');
            element.classList.add('escaneado');
        } else if (hasSeat(confirmedSeats, idAsiento)) {
            element.classList.remove('disponible');
            element.classList.add('confirmado');
        } else if (hasSeat(occupiedSeats, idAsiento)) {
            element.classList.remove('disponible');
            element.classList.add('ocupado');
        }
    }
}
