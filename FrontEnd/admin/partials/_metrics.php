<div class="admin-metrics-grid">
    <!-- Alumnos Confirmados -->
    <div class="admin-metric-card" onclick="window.setFilterType('CONFIRMADOS')" title="Filtrar por Confirmados">
        <div class="admin-metric-card__icon admin-metric-card__icon--success">
            <span class="admin-icon admin-icon--chair"></span>
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
    <div class="admin-metric-card" onclick="window.setFilterType('INVITADOS')" title="Filtrar por alumnos con invitados">
        <div class="admin-metric-card__icon admin-metric-card__icon--primary">
            <span class="admin-icon admin-icon--add"></span>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">Total Invitados</span>
            <span id="metric-total" class="admin-metric-card__value">0</span>
        </div>
    </div>

    <!-- LI 4-1 -->
    <div class="admin-metric-card" onclick="window.setFilterType('LI4-1')" title="Filtrar por LI 4-1 (Matutino)">
        <div class="admin-metric-card__icon admin-metric-card__icon--info">
            <span class="admin-icon admin-icon--student"></span>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">LI 4-1</span>
            <span id="metric-li41" class="admin-metric-card__value">0</span>
            <small class="admin-metric-card__subtext">+<span id="guests-li41">0</span> invitados</small>
        </div>
    </div>

    <!-- LI 4-2 -->
    <div class="admin-metric-card" onclick="window.setFilterType('LI4-2')" title="Filtrar por LI 4-2 (Vespertino)">
        <div class="admin-metric-card__icon admin-metric-card__icon--purple">
            <span class="admin-icon admin-icon--student"></span>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">LI 4-2</span>
            <span id="metric-li42" class="admin-metric-card__value">0</span>
            <small class="admin-metric-card__subtext">+<span id="guests-li42">0</span> invitados</small>
        </div>
    </div>

    <!-- LISI 4-1 -->
    <div class="admin-metric-card" onclick="window.setFilterType('LISI4-1')" title="Filtrar por LISI 4-1 (Matutino)">
        <div class="admin-metric-card__icon admin-metric-card__icon--warning">
            <span class="admin-icon admin-icon--student"></span>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">LISI 4-1</span>
            <span id="metric-lisi41" class="admin-metric-card__value">0</span>
            <small class="admin-metric-card__subtext">+<span id="guests-lisi41">0</span> invitados</small>
        </div>
    </div>

    <!-- LISI 4-2 -->
    <div class="admin-metric-card" onclick="window.setFilterType('LISI4-2')" title="Filtrar por LISI 4-2 (Vespertino)">
        <div class="admin-metric-card__icon admin-metric-card__icon--danger">
            <span class="admin-icon admin-icon--student"></span>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">LISI 4-2</span>
            <span id="metric-lisi42" class="admin-metric-card__value">0</span>
            <small class="admin-metric-card__subtext">+<span id="guests-lisi42">0</span> invitados</small>
        </div>
    </div>

    <!-- No Asistirán -->
    <div class="admin-metric-card" onclick="window.setFilterType('RECHAZADOS')" title="Filtrar por Rechazados">
        <div class="admin-metric-card__icon admin-metric-card__icon--dark">
            <span class="admin-icon admin-icon--student-disable"></span>
        </div>
        <div class="admin-metric-card__content">
            <span class="admin-metric-card__label">No Asistirán</span>
            <span id="metric-rechazados" class="admin-metric-card__value">0</span>
        </div>
    </div>
</div>
