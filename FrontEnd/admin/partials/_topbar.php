<header class="admin-topbar">
    <div class="admin-topbar__title-wrapper">
        <h1 class="admin-topbar__title">Directorio de Alumnos</h1>
        <div class="admin-topbar__clock">
            <i class="bi bi-clock"></i>
            <span id="lastUpdated">Actualizado: --:--:--</span>
        </div>
    </div>
    <div class="admin-topbar__actions">
        <button class="admin-btn admin-btn--secondary" onclick="window.location.href='modals/modal_escaner_qr.php'">
            <i class="bi bi-qr-code-scan"></i>
            <span>Escanear QR</span>
        </button>
        <button class="admin-btn admin-btn--primary" onclick="window.location.href='modals/modal_enviar_qr.php'">
            <i class="bi bi-envelope-at"></i>
            <span>Enviar QRs</span>
        </button>
        <button class="admin-btn admin-btn--accent" onclick="abrirModalAgregarAlumno()">
            <i class="bi bi-person-plus"></i>
            <span>Agregar Alumno</span>
        </button>
    </div>
</header>