<aside class="admin-sidebar">
    <div class="admin-sidebar__header">
        <a href="javascript:void(0)" id="link-logo-dashboard" style="display: block; width: 100%; text-align: center;">
            <img src="../img/convision.png" alt="Convision Logo" class="admin-sidebar__logo" style="max-height: 50px; object-fit: contain;">
        </a>
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
                <li class="admin-sidebar__item admin-sidebar__item--divider">
                    <a href="javascript:void(0)" class="admin-sidebar__link admin-sidebar__link--warning-custom" id="btnResetQrLi">
                        <span class="admin-icon admin-icon--warning-custom"></span>
                        <span>Restablecer QRs LI</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="javascript:void(0)" class="admin-sidebar__link admin-sidebar__link--warning-custom" id="btnResetQrLisi">
                        <span class="admin-icon admin-icon--warning-custom"></span>
                        <span>Restablecer QRs LISI</span>
                    </a>
                </li>
                <li class="admin-sidebar__item mt-1">
                    <a href="javascript:void(0)" class="admin-sidebar__link admin-sidebar__link--danger-custom" id="btnResetConfirmaciones">
                        <span class="admin-icon admin-icon--danger-custom"></span>
                        <span>Resetear Confirmaciones</span>
                    </a>
                </li>
                <!-- Asignacion Dinamica -->
                <li class="admin-sidebar__item admin-sidebar__item--divider">
                    <a href="javascript:void(0)" class="admin-sidebar__link admin-sidebar__link--warning-custom" id="btnLimpiarAsignaciones">
                        <span class="admin-icon admin-icon--warning-custom"></span>
                        <span>Limpiar asignaciones</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="javascript:void(0)" class="admin-sidebar__link admin-sidebar__link--info" id="btnAsignarAsientos">
                        <span class="admin-icon admin-icon--chair"></span>
                        <span>Asignar asientos</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <div class="admin-sidebar__link sidebar-switch-item">
                        <span>Publicar resultados</span>
                        <label class="switch">
                            <input type="checkbox" id="switchPublicar">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </li>
                <!-- Exportar PDF -->
                <li class="admin-sidebar__item admin-sidebar__item--divider">
                    <a href="javascript:void(0)" class="admin-sidebar__link admin-sidebar__link--info" id="btnExportarPdfLi">
                        <span class="admin-icon admin-icon--pdf"></span>
                        <span>Exportar PDF LI</span>
                    </a>
                </li>
                <li class="admin-sidebar__item">
                    <a href="javascript:void(0)" class="admin-sidebar__link admin-sidebar__link--info" id="btnExportarPdfLisi">
                        <span class="admin-icon admin-icon--pdf"></span>
                        <span>Exportar PDF LISI</span>
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
                <li id="gruposFilterContainer"></li>
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