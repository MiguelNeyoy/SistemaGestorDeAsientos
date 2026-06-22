<section class="admin-directory">
    <div class="admin-directory__header d-flex justify-content-start align-items-center gap-4">
        <h2 class="admin-directory__title">
            <span class="admin-icon admin-icon--chair"></span> Directorio de Alumnos
        </h2>

        <div class="admin-directory__controls d-flex align-items-center gap-2">
            <div id="bulkQrActionContainer" class="admin-hidden"></div>

            <button id="btnMostrarTodo" class="admin-btn admin-btn--outline admin-hidden">
                <span class="admin-icon admin-icon--chair" style="width:14px;height:14px;"></span> Mostrar Todo
            </button>

            <div class="admin-search">
                <span class="admin-icon admin-search__icon"
                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 fill=%22none%22 viewBox=%220 0 24 24%22 stroke=%22currentColor%22%3E%3Cpath stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%222%22 d=%22M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z%22 /%3E%3C/svg%3E');"></span>
                <input type="text" id="searchAlumno" class="admin-search__input"
                    placeholder="Buscar por nombre, cuenta o asiento...">
            </div>
        </div>
    </div>

    <div class="admin-directory__body">
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkboxToggleAll"></th>
                        <th>No. Cuenta</th>
                        <th>Nombre Completo</th>
                        <th>Carrera / Turno</th>
                        <th>Invitados</th>
                        <th>Correo Contacto</th>
                        <th>Asiento</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="alumnosTableBody">
                    <tr>
                        <td colspan="7" class="admin-table__loading">
                            <div class="admin-loader"></div>
                            <span>Cargando directorio...</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>