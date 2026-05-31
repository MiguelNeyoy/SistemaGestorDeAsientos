<aside class="admin-sidebar">
    <div class="admin-sidebar__header">
        <img src="../img/logo-vector-uas.webp" alt="UAS Logo" class="admin-sidebar__logo">
    </div>
    <nav class="admin-sidebar__nav">
        <div class="admin-sidebar__section-title">Navegación</div>
        <ul class="admin-sidebar__list">
            <li class="admin-sidebar__item">
                <a href="#" class="admin-sidebar__link admin-sidebar__link--active" id="link-filter-all" data-filter="ALL">
                    <span class="admin-icon admin-icon--dashboard"></span>
                    <span>Dashboard General</span>
                </a>
            </li>
            <li class="admin-sidebar__item">
                <a href="javascript:void(0)" class="admin-sidebar__link" id="btnMapaAsientos">
                    <span class="admin-icon admin-icon--chair"></span>
                    <span>Mapa de Asientos</span>
                </a>
            </li>
        </ul>

        <section class="admin-sidebar__section--collapsible">
            <div class="admin-sidebar__section-header">
                <p class="admin-sidebar__section-title">Evento</p>
                <span class="admin-sidebar__chevron"></span>
            </div>
            <ul class="admin-sidebar__list">
                <li class="admin-sidebar__item">
                    <a href="javascript:void(0)" class="admin-sidebar__link" id="link-filter-li" data-filter="LI">
                        <span class="admin-icon admin-icon--chair"></span>
                        <span>LI (Informática)</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="javascript:void(0)" class="admin-sidebar__link" id="link-filter-lisi" data-filter="LISI">
                        <span class="admin-icon admin-icon--chair"></span>
                        <span>LISI (Sistemas)</span>
                    </a>
                </li>
            </ul>
        </section>

        <section class="admin-sidebar__section--collapsible admin-sidebar__section--collapsed">
            <div class="admin-sidebar__section-header">
                <p class="admin-sidebar__section-title">Estadísticas y Filtros</p>
                <span class="admin-sidebar__chevron"></span>
            </div>
            <ul class="admin-sidebar__list">
                <li class="admin-sidebar__item">
                    <a href="#" class="admin-sidebar__link" id="link-filter-confirmados" data-filter="CONFIRMADOS">
                        <span class="admin-icon admin-icon--student"
                            style="filter: hue-rotate(90deg) brightness(1.5);"></span>
                        <span>Confirmados (<span id="metric-confirmados">0</span>)</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="#" class="admin-sidebar__link" id="link-filter-invitados" data-filter="INVITADOS">
                        <span class="admin-icon admin-icon--add"
                            style="filter: hue-rotate(180deg) brightness(1.5);"></span>
                        <span>Invitados (<span id="metric-invitados">0</span>)</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <div class="admin-sidebar__link">
                        <span class="admin-icon admin-icon--chair" style="filter: brightness(1.2);"></span>
                        <span>Asientos Ocupados (<span id="metric-asientos">0</span>)</span>
                    </div>
                </li>
                <li class="admin-sidebar__item">
                    <a href="#" class="admin-sidebar__link" id="link-filter-li41" data-filter="LI4-1">
                        <span class="admin-icon admin-icon--student"></span>
                        <span>LI 4-1 (<span id="metric-grupo-LI4-1">0</span>)</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="#" class="admin-sidebar__link" id="link-filter-li42" data-filter="LI4-2">
                        <span class="admin-icon admin-icon--student"></span>
                        <span>LI 4-2 (<span id="metric-grupo-LI4-2">0</span>)</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="#" class="admin-sidebar__link" id="link-filter-lisi41" data-filter="LISI4-1">
                        <span class="admin-icon admin-icon--student"></span>
                        <span>LISI 4-1 (<span id="metric-grupo-LISI4-1">0</span>)</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="#" class="admin-sidebar__link" id="link-filter-lisi42" data-filter="LISI4-2">
                        <span class="admin-icon admin-icon--student"></span>
                        <span>LISI 4-2 (<span id="metric-grupo-LISI4-2">0</span>)</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="#" class="admin-sidebar__link" id="link-filter-rechazados" data-filter="RECHAZADOS">
                        <span class="admin-icon admin-icon--student-disable"
                            style="filter: saturate(0) brightness(0.8);"></span>
                        <span>No Asistirán (<span id="metric-rechazados">0</span>)</span>
                    </a>
                </li>
            </ul>
        </section>
    </nav>
    <div class="admin-sidebar__footer">
        <button id="btnLogout" class="admin-sidebar__logout">
            <span class="admin-icon admin-icon--logout"></span>
            <span class="admin-hide-mobile">Cerrar Sesión</span>
        </button>
    </div>
</aside>