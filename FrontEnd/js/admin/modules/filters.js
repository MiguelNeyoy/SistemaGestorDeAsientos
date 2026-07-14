import { getGrupo } from '../../shared/utils.js';

/**
 * Pure filtering logic for the student list.
 */

export function filterStudents(students, filterType, searchText = '') {
    let filtered = students;

    // Apply categorical filter
    if (filterType !== 'ALL') {
        filtered = students.filter(s => {
            const studentGrupo = getGrupo(s.carrera, s.turno) || '';
            
            switch(filterType) {
                case 'LI':
                    return studentGrupo.startsWith('LI4-');
                case 'LISI':
                    return studentGrupo.startsWith('LISI');
                case 'CONFIRMADOS':
                    return s.asistencia_estado == 1;
                case 'RECHAZADOS':
                    return s.asistencia_estado == 2;
                case 'INVITADOS':
                    return parseInt(s.cantInvitado) > 0;
                default:
                    return studentGrupo === filterType;
            }
        });
    }

    // Apply search text filter
    if (searchText.trim() !== '') {
        const query = searchText.toLowerCase().trim();
        filtered = filtered.filter(s => {
            const nombre = String(s.nombre || '').toLowerCase();
            const apellido = String(s.apellido || '').toLowerCase();
            const numCuenta = String(s.numCuenta || '');
            const asientoFull = s.letra ? String(`${s.letra}-${s.numero}`).toLowerCase() : '';
            const idAsiento = String(s.idAsiento || '').toLowerCase();

            return nombre.includes(query) || 
                   apellido.includes(query) || 
                   numCuenta.includes(query) ||
                   asientoFull.includes(query) ||
                   idAsiento.includes(query);
        });
    }

    return filtered;
}
