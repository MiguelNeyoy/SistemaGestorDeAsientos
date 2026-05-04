/**
 * Sorting utilities for the student list.
 */

/**
 * Sorts students by name (Name + Surname) using localeCompare for iOS compatibility.
 */
export function sortByName(students) {
    return [...students].sort((a, b) => {
        const nameA = `${a.apellido} ${a.nombre}`.trim();
        const nameB = `${b.apellido} ${b.nombre}`.trim();
        return nameA.localeCompare(nameB, 'es', { sensitivity: 'base' });
    });
}

/**
 * Sorts students by seat ID.
 */
export function sortBySeat(students) {
    return [...students].sort((a, b) => {
        if (!a.letra && !b.letra) return 0;
        if (!a.letra) return 1;
        if (!b.letra) return -1;

        // Sort by letter
        const letterComp = a.letra.localeCompare(b.letra);
        if (letterComp !== 0) return letterComp;

        // Sort by number
        return parseInt(a.numero) - parseInt(b.numero);
    });
}
