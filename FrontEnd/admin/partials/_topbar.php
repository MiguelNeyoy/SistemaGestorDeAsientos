<header class="admin-topbar">
    <div class="admin-topbar__header">
        <button class="admin-btn admin-btn--icon-only admin-hide-desktop" id="btnToggleSidebar" style="padding: 0.5rem; background: transparent; border: none;">
            <span class="admin-icon admin-icon--menu" style="width: 24px; height: 24px;"></span>
        </button>
        <div class="admin-topbar__title-wrapper">
            <h1 class="admin-topbar__title">Directorio de Alumnos</h1>
            <div class="admin-topbar__clock">
                <span class="admin-icon admin-icon--update" style="width: 16px; height: 16px;"></span>
                <span id="lastUpdated">Actualizado: --:--:--</span>
            </div>
        </div>
    </div>
    <div class="admin-topbar__actions">
        <button class="admin-btn admin-btn--secundary" onclick="abrirModalEscanerQR()">
            <span class="admin-icon admin-icon--scan"></span>
            <span>Escanear QR</span>
        </button>
        <button class="admin-btn admin-btn--secundary" onclick="abrirModalEnviarQR()">
            <span class="admin-icon admin-icon--send"></span>
            <span>Enviar QRs</span>
        </button>
        <button class="admin-btn admin-btn--secundary" onclick="abrirModalAgregarAlumno()">
            <span class="admin-icon admin-icon--add"></span>
            <span>Agregar Alumno</span>
        </button>
    </div>
</header>