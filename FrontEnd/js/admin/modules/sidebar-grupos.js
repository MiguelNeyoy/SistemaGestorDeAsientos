import { state } from '../store/state.js';
import { fetchGrupos } from './api.js';
import { hide as hideMap } from './map.js';

let gruposList = [];
let resolveReady;
const readyPromise = new Promise(r => { resolveReady = r; });

export async function initSidebarGrupos() {
    try {
        gruposList = await fetchGrupos();
    } catch (e) {
        gruposList = [];
    }
    renderGrupos();
    if (resolveReady) resolveReady();
}

function renderGrupos() {
    const placeholder = document.getElementById('gruposFilterContainer');
    if (!placeholder) return;

    const parentUl = placeholder.parentElement;

    placeholder.outerHTML = gruposList.map(grupo => `
        <li class="admin-sidebar__item">
            <a href="javascript:void(0)" class="admin-sidebar__link" data-filter="${grupo}">
                <span class="admin-icon admin-icon--student"></span>
                <span>${grupo} (<span id="metric-grupo-${grupo}">0</span>)</span>
            </a>
        </li>
    `).join('');

    if (!parentUl) return;

    parentUl.querySelectorAll('[data-filter]').forEach(link => {
        if (link.onclick) return;
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

export async function getGrupos() {
    await readyPromise;
    return gruposList;
}
