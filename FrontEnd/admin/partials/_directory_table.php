<section class="admin-directory">
    <div class="admin-directory__header">
        <h2 class="admin-directory__title">
            <span class="admin-icon admin-icon--chair"></span> Directorio de Asistencia
        </h2>

        <div class="admin-directory__controls">
            <button id="btnMostrarTodo" class="admin-btn admin-btn--outline admin-hidden"
                onclick="window.setFilterType('ALL')">
                <span class="admin-icon admin-icon--chair" style="width:14px;height:14px;"></span> Mostrar Todo
            </button>
            <div class="admin-search">
                <i class="bi bi-search admin-search__icon"></i>
                <input type="text" id="searchInput" class="admin-search__input" placeholder="Buscar alumno...">
            </div>
        </div>
    </div>

    <div class="admin-directory__body" id="directorioCardBody">
        <div id="directorioHintMobile" class="admin-directory__hint" style="display: none;">
            <i class="bi bi-info-circle"></i>
            <p>Utiliza la barra de búsqueda o selecciona un filtro en el menú lateral para mostrar alumnos.</p>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table" id="alumnosTable">
                <thead>
                    <tr>
                        <th>No. Cuenta</th>
                        <th>Nombre Completo</th>
                        <th>Carrera/Turno</th>
                        <th class="admin-text-center">Invitados</th>
                        <th class="admin-table__email">Correo Contacto</th>
                        <th class="admin-text-center">Asiento</th>
                        <th class="admin-text-center">Estado</th>
                        <th class="admin-text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="alumnosTableBody">
                    <tr>
                        <td colspan="8" class="admin-table__loading">
                            <div class="admin-loader"></div>
                            <span>Cargando datos del servidor...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>