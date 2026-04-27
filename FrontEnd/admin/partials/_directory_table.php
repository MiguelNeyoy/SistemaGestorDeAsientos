<section class="admin-directory">
    <div class="admin-directory__header">
        <h2 class="admin-directory__title">
            <i class="bi bi-list-check"></i> Directorio de Asistencia
        </h2>
        
        <div class="admin-directory__controls">
            <button id="btnMostrarTodo" class="admin-btn admin-btn--outline d-none" onclick="setFilterType('ALL')">
                <i class="bi bi-funnel-fill"></i> Mostrar Todo
            </button>
            <div class="admin-search">
                <i class="bi bi-search admin-search__icon"></i>
                <input type="text" id="searchInput" class="admin-search__input" placeholder="Buscar alumno...">
            </div>
        </div>
    </div>

    <div class="admin-directory__body">
        <div id="directorioHintMobile" class="admin-directory__hint" style="display: none;">
            <i class="bi bi-info-circle"></i>
            <p>Utiliza la barra de búsqueda o toca alguna tarjeta métrica para mostrar alumnos.</p>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table" id="alumnosTable">
                <thead>
                    <tr>
                        <th>No. Cuenta</th>
                        <th>Nombre Completo</th>
                        <th>Carrera/Turno</th>
                        <th class="text-center">Invitados</th>
                        <th>Correo Contacto</th>
                        <th class="text-center">Asiento</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody id="alumnosTableBody">
                    <tr>
                        <td colspan="8" class="admin-table__loading">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Cargando datos del servidor...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
