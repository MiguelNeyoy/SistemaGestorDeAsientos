/**
 * Determina el grupo basado en la carrera y el turno.
 * LISI = Ingeniería / Otros
 * LI = Informática
 * Turno 1 = Matutino, 2 = Vespertino
 */
export function getGrupo(carrera, turno) {
    const carLower = (carrera || "").toLowerCase();
    const turnoUpper = (turno || "").toUpperCase();
    
    let prefix = 'LISI'; // Por defecto Sistemas (Ingeniería)
    if (carLower.includes('informática') || carLower.includes('informatica')) {
        prefix = 'LI';
    }
    
    const turnoNum = (turnoUpper === 'M' || turnoUpper === '1') ? '1' : '2';
    
    return `${prefix}4-${turnoNum}`;
}
