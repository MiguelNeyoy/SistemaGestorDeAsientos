import { state } from '../store/state.js';
import { fetchGrupos } from './api.js';
import { hide as hideMap } from './map.js';

let gruposList = [];

export async function initSidebarGrupos() {
    try {
        gruposList = await fetchGrupos();
    } catch (e) {
        gruposList = [];
    }
    renderGrupos();
}

function renderGrupos() {
    const container = document.getElementById('gruposFilterContainer');
    if (!container) return;

    container.innerHTML = gruposList.map(grupo => `
        <li class="admin-sidebar__item">
            <a href="javascript:void(0)" class="admin-sidebar__link" data-filter="${grupo}">
                <span class="admin-icon admin-icon--student"></span>
                <span>${grupo} (<span id="metric-grupo-${grupo}">0</span>)</span>
            </a>
        </li>
    `).join('');

    container.querySelectorAll('[data-filter]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.admin-sidebar__link').forEach(l =>
                l.classList.remove('admin-sidebar__link--active'));
            link.classList.add('admin-sidebar__link--active');
            hideMap();
            state.setFilterType(link.dataset.filter);
        });
    });
}

export function getGrupos() {
    return gruposList;
}
