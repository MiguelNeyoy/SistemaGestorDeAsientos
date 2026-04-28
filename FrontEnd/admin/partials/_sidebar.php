<aside class="admin-sidebar">
    <div class="admin-sidebar__header">
        <img src="../img/logo-vector-uas.webp" alt="UAS Logo" class="admin-sidebar__logo">
    </div>
    <nav class="admin-sidebar__nav">
        <div class="admin-sidebar__section-title">Navegación</div>
        <ul class="admin-sidebar__list">
            <li class="admin-sidebar__item">
                <a href="#" class="admin-sidebar__link admin-sidebar__link--active" id="link-filter-all"
                    onclick="window.setFilterType('ALL')">
                    <span class="admin-icon admin-icon--dashboard"></span>
                    <span>Dashboard General</span>
                </a>
            </li>
            <li class="admin-sidebar__item">
                <a href="../asientos.php" class="admin-sidebar__link">
                    <span class="admin-icon admin-icon--chair"></span>
                    <span>Mapa de Asientos</span>
                </a>
            </li>
        </ul>

        <section class="admin-sidebar__section admin-sidebar__section--collapsible admin-sidebar__section--collapsed" id="sectionFilters">
            <button class="admin-sidebar__section-toggle" type="button">
                <span class="admin-sidebar__section-title">Estadísticas y Filtros</span>
                <span class="admin-icon admin-icon--chevron-down"></span>
            </button>
            <div class="admin-sidebar__section-content">
                <ul class="admin-sidebar__list">
                    <li class="admin-sidebar__item">
                        <a href="#" class="admin-sidebar__link" id="link-filter-confirmados"
                            onclick="window.setFilterType('CONFIRMADOS')">
                            <span class="admin-icon admin-icon--student"
                                style="filter: hue-rotate(90deg) brightness(1.5);"></span>
                            <span>Confirmados (<span id="metric-confirmados">0</span>)</span>
                        </a>
                    </li>
                    <li class="admin-sidebar__item">
                        <a href="#" class="admin-sidebar__link" id="link-filter-invitados"
                            onclick="window.setFilterType('INVITADOS')">
                            <span class="admin-icon admin-icon--add"
                                style="filter: hue-rotate(180deg) brightness(1.5);"></span>
                            <span>Invitados (<span id="metric-total">0</span>)</span>
                        </a>
                    </li>
                    <li class="admin-sidebar__item">
                        <a href="#" class="admin-sidebar__link" id="link-filter-li41"
                            onclick="window.setFilterType('LI4-1')">
                            <span class="admin-icon admin-icon--student"></span>
                            <span>LI 4-1 (<span id="metric-li41">0</span>)</span>
                        </a>
                    </li>
                    <li class="admin-sidebar__item">
                        <a href="#" class="admin-sidebar__link" id="link-filter-li42"
                            onclick="window.setFilterType('LI4-2')">
                            <span class="admin-icon admin-icon--student"></span>
                            <span>LI 4-2 (<span id="metric-li42">0</span>)</span>
                        </a>
                    </li>
                    <li class="admin-sidebar__item">
                        <a href="#" class="admin-sidebar__link" id="link-filter-lisi41"
                            onclick="window.setFilterType('LISI4-1')">
                            <span class="admin-icon admin-icon--student"></span>
                            <span>LISI 4-1 (<span id="metric-lisi41">0</span>)</span>
                        </a>
                    </li>
                    <li class="admin-sidebar__item">
                        <a href="#" class="admin-sidebar__link" id="link-filter-lisi42"
                            onclick="window.setFilterType('LISI4-2')">
                            <span class="admin-icon admin-icon--student"></span>
                            <span>LISI 4-2 (<span id="metric-lisi42">0</span>)</span>
                        </a>
                    </li>
                    <li class="admin-sidebar__item">
                        <a href="#" class="admin-sidebar__link" id="link-filter-rechazados"
                            onclick="window.setFilterType('RECHAZADOS')">
                            <span class="admin-icon admin-icon--student-disable"
                                style="filter: saturate(0) brightness(0.8);"></span>
                            <span>No Asistirán (<span id="metric-rechazados">0</span>)</span>
                        </a>
                    </li>
                </ul>
            </div>
        </section>
    </nav>
    <div class="admin-sidebar__footer">
        <button id="btnLogout" class="admin-sidebar__logout">
            <span class="admin-icon admin-icon--logout"></span>
            <span class="admin-hide-mobile">Cerrar Sesión</span>
        </button>
    </div>
</aside>