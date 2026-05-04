<header class="admin-topbar">
    <div class="admin-topbar__header">
        <button class="admin-btn admin-btn--icon-only admin-hide-desktop" id="btnToggleSidebar" style="padding: 0.5rem; background: transparent; border: none;">
            <span class="admin-icon admin-icon--menu admin-icon--white" style="width: 24px; height: 24px;"></span>
        </button>
        <div class="admin-topbar__title-wrapper">
            <h1 class="admin-topbar__title">Directorio de Alumnos</h1>
            <div class="admin-topbar__clock">
                <span class="admin-icon admin-icon--update" style="width: 16px; height: 16px;"></span>
                <span id="lastUpdated">--:--:--</span>
            </div>
        </div>
        <a href="?logout=true" class="admin-btn admin-btn--icon-only admin-hide-desktop admin-topbar__logout" style="margin-left: auto; background: transparent; border: none; padding: 0.5rem;">
            <span class="admin-icon admin-icon--logout admin-icon--white" style="width: 24px; height: 24px;"></span>
        </a>
    </div>
    <div class="admin-topbar__actions">
        <button class="admin-btn admin-btn--primary" id="btnEscanearQR">
            <span class="admin-icon admin-icon--scan admin-icon--white"></span>
            <span class="d-none d-md-inline">Escanear QR</span>
        </button>
        <button class="admin-btn admin-btn--primary" id="btnEnviarQR">
            <span class="admin-icon admin-icon--send admin-icon--white"></span>
            <span class="d-none d-md-inline">Enviar QRs</span>
        </button>
        <button class="admin-btn admin-btn--primary" id="btnAgregarAlumno">
            <span class="admin-icon admin-icon--add admin-icon--white"></span>
            <span class="d-none d-md-inline">Agregar Alumno</span>
        </button>
    </div>
</header>