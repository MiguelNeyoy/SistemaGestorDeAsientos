<header class="admin-topbar">
    <div class="admin-topbar__header">
        <button class="admin-btn admin-btn--icon-only admin-hide-desktop admin-hidden" id="btnToggleSidebar" style="padding: 0.5rem; background: transparent; border: none;">
            <span class="admin-icon admin-icon--menu" style="width: 24px; height: 24px;"></span>
        </button>
        <div class="admin-topbar__title-wrapper">
            <h1 class="admin-topbar__title">Directorio de Alumnos</h1>
            <div class="admin-topbar__clock">
                <span class="admin-icon admin-icon--update" style="width: 16px; height: 16px;"></span>
                <span id="lastUpdated">--:--:--</span>
            </div>
        </div>
        <a href="?logout=true" class="admin-btn admin-btn--icon-only admin-hide-desktop" style="margin-left: auto; background: transparent; border: none; padding: 0.5rem;">
            <span class="admin-icon admin-icon--logout" style="width: 24px; height: 24px; filter: brightness(0) invert(1);"></span>
        </a>
    </div>
    <div class="admin-topbar__actions">
        <button class="admin-btn admin-btn--primary" id="btnEscanearQR">
            <span class="admin-icon admin-icon--scan admin-icon--white"></span>
            <span>Escanear QR</span>
        </button>
        <button class="admin-btn admin-btn--primary" id="btnEnviarQR">
            <span class="admin-icon admin-icon--send admin-icon--white"></span>
            <span>Enviar QRs</span>
        </button>
        <button class="admin-btn admin-btn--primary" id="btnAgregarAlumno">
            <span class="admin-icon admin-icon--add admin-icon--white"></span>
            <span>Agregar Alumno</span>
        </button>
    </div>
</header>