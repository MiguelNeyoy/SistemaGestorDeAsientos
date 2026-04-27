<div class="admin-metrics-grid">
    <!-- Alumnos Confirmados -->
    <div class="admin-metric-card" onclick="setFilterType('CONFIRMADOS')" title="Filtrar por Confirmados">
        <div class="admin-metric-card__icon admin-metric-card__icon--success">
            <i class="bi bi-person-check-fill"></i>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">Alumnos Confirmados</span>
            <div class="admin-metric-card__value-group">
                <span id="metric-confirmados" class="admin-metric-card__value">0</span>
                <span class="admin-metric-card__total">/ <span id="metric-total-alumnos">0</span></span>
            </div>
        </div>
    </div>

    <!-- Total Invitados -->
    <div class="admin-metric-card" onclick="setFilterType('INVITADOS')" title="Filtrar por alumnos con invitados">
        <div class="admin-metric-card__icon admin-metric-card__icon--primary">
            <i class="bi bi-people-fill"></i>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">Total Invitados</span>
            <span id="metric-total" class="admin-metric-card__value">0</span>
        </div>
    </div>

    <!-- LI 4-1 -->
    <div class="admin-metric-card" onclick="setFilterType('LI4-1')" title="Filtrar por LI 4-1 (Matutino)">
        <div class="admin-metric-card__icon admin-metric-card__icon--info">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">LI 4-1</span>
            <span id="metric-li41" class="admin-metric-card__value">0</span>
            <small class="admin-metric-card__subtext">+<span id="guests-li41">0</span> invitados</small>
        </div>
    </div>

    <!-- LI 4-2 -->
    <div class="admin-metric-card" onclick="setFilterType('LI4-2')" title="Filtrar por LI 4-2 (Vespertino)">
        <div class="admin-metric-card__icon admin-metric-card__icon--purple">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">LI 4-2</span>
            <span id="metric-li42" class="admin-metric-card__value">0</span>
            <small class="admin-metric-card__subtext">+<span id="guests-li42">0</span> invitados</small>
        </div>
    </div>

    <!-- LISI 4-1 -->
    <div class="admin-metric-card" onclick="setFilterType('LISI4-1')" title="Filtrar por LISI 4-1 (Matutino)">
        <div class="admin-metric-card__icon admin-metric-card__icon--warning">
            <i class="bi bi-pc-display"></i>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">LISI 4-1</span>
            <span id="metric-lisi41" class="admin-metric-card__value">0</span>
            <small class="admin-metric-card__subtext">+<span id="guests-lisi41">0</span> invitados</small>
        </div>
    </div>

    <!-- LISI 4-2 -->
    <div class="admin-metric-card" onclick="setFilterType('LISI4-2')" title="Filtrar por LISI 4-2 (Vespertino)">
        <div class="admin-metric-card__icon admin-metric-card__icon--danger">
            <i class="bi bi-pc-display"></i>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">LISI 4-2</span>
            <span id="metric-lisi42" class="admin-metric-card__value">0</span>
            <small class="admin-metric-card__subtext">+<span id="guests-lisi42">0</span> invitados</small>
        </div>
    </div>

    <!-- No Asistirán -->
    <div class="admin-metric-card" onclick="setFilterType('RECHAZADOS')" title="Filtrar por Rechazados">
        <div class="admin-metric-card__icon admin-metric-card__icon--dark">
            <i class="bi bi-person-x-fill"></i>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">No Asistirán</span>
            <span id="metric-rechazados" class="admin-metric-card__value">0</span>
        </div>
    </div>

    <!-- Acceso Mapa (Shortcut) -->
    <a href="../asientos.php" class="admin-metric-card admin-metric-card--link">
        <div class="admin-metric-card__icon admin-metric-card__icon--black">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">Mapa de Asientos</span>
            <span class="admin-metric-card__link-text">Ver Mapa</span>
        </div>
    </a>
</div>
