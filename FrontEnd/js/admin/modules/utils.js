/**
 * Determina el grupo basado en la carrera y el turno.
 * LI = Ingeniería / Otros
 * LISI = Informática
 * Turno 1 = Matutino, 2 = Vespertino
 */
export function getGrupo(carrera, turno) {
    const carLower = (carrera || "").toLowerCase();
    const turnoUpper = (turno || "").toUpperCase();
    
    let prefix = 'LI'; // Por defecto Licenciatura (Ingeniería/Sistemas)
    if (carLower.includes('informática') || carLower.includes('informatica')) {
        prefix = 'LISI';
    }
    
    const turnoNum = (turnoUpper === 'M' || turnoUpper === '1') ? '1' : '2';
    
    return `${prefix}4-${turnoNum}`;
}
