/**
 * Shared seat rendering logic.
 */

/**
 * Applies styles to a seat element based on its status.
 * @param {HTMLElement} element - The seat div.
 * @param {string} idAsiento - Seat ID (e.g., 'A1').
 * @param {Object} config - Configuration (userType, occupiedSeats, studentSeat, groupSeats).
 */
export function pintarAsiento(element, idAsiento, config = {}) {
    const { 
        userType = 'alumno', 
        occupiedSeats = [], 
        studentSeat = null, 
        groupSeats = [] 
    } = config;

    // Reset classes
    element.classList.remove('disponible', 'ocupado', 'mi-asiento', 'grupo');
    element.classList.add('disponible');

    if (userType === 'alumno') {
        // Group seats
        if (groupSeats.includes(idAsiento)) {
            element.classList.remove('disponible');
            element.classList.add('grupo');
        }

        // Student's own seat (priority)
        if (idAsiento === studentSeat) {
            element.classList.remove('grupo', 'disponible');
            element.classList.add('mi-asiento');
        }
    } else if (userType === 'admin') {
        if (occupiedSeats.includes(idAsiento)) {
            element.classList.remove('disponible');
            element.classList.add('ocupado');
        }
    }
}
