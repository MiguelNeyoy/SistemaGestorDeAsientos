/**
 * Shared utility functions for both Admin and Student views.
 */

/**
 * Calculates the group identifier (siglas) based on carrera and turno.
 * @param {string} carrera - Full name of the career.
 * @param {string} turno - 'M' (Matutino) or 'V' (Vespertino).
 * @returns {string} - e.g., 'LI4-1', 'LISI4-2'.
 */
export function getGrupo(carrera, turno) {
    const carLower = (carrera || '').toLowerCase().trim();
    const turnoUpper = (turno || '').toUpperCase().trim();

    let prefix = 'LISI'; // Default
    if (carLower.includes('informática') || carLower.includes('informatica')) {
        prefix = 'LI';
    }
    if (carLower.includes('virtual')) {
        return prefix === 'LI' ? 'LI-V' : 'LISI-V';
    }
    const turnoNum = (turnoUpper === 'M' || turnoUpper === '1') ? '1' : '2';

    return `${prefix}4-${turnoNum}`;
}
