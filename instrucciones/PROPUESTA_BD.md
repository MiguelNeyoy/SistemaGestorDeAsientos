# PROPUESTA: Mejoras a la Base de Datos

## Problema Actual

La base de datos actual no contempla el flujo completo de gestión de códigos QR y registro de llegadas al evento.

| Funcionalidad | Estado | Problema |
|---------------|--------|----------|
| Guardar QR generado | ⚠️ Parcial | Solo imagen, no BD |
| Verificar si QR fue usado | ❌ No funciona | Sin control |
| Registrar llegada al teatro | ❌ No existe | Mezcla conceptos |
| Saber si QR fue enviado | ❌ No existe | Sin historial |

---

## Tablas Propuestas

### 1. `qr_codigo` (NUEVA)

**¿Por qué es necesaria?**
Controla el ciclo de vida completo de cada código QR: quién lo generó, cuándo, si fue enviado y si ya fue utilizado.

```sql
CREATE TABLE qr_codigo (
    id_qr INT AUTO_INCREMENT PRIMARY KEY,
    numCuenta VARCHAR(20) NOT NULL,
    token_jwt TEXT NOT NULL,
    ruta_imagen VARCHAR(255),
    fecha_generacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    enviado BOOLEAN DEFAULT FALSE,
    fecha_envio DATETIME,
    usado BOOLEAN DEFAULT FALSE,
    fecha_uso DATETIME,
    metodo_envio ENUM('correo', 'telegram', 'whatsapp', 'manual') DEFAULT NULL,
    FOREIGN KEY (numCuenta) REFERENCES alumno(numCuenta)
);
```

---

### 2. `llegada` (NUEVA)

**¿Por qué es necesaria?**
Separa "confirmación de asistencia" (días antes) de "llegada al evento" (el día de la graduación). Permite registrar cuántos invitados llegaron realmente.

```sql
CREATE TABLE llegada (
    id_llegada INT AUTO_INCREMENT PRIMARY KEY,
    numCuenta VARCHAR(20) NOT NULL,
    fecha_llegada DATETIME DEFAULT CURRENT_TIMESTAMP,
    invitados_confirmados INT DEFAULT 0,
    qr_utilizado VARCHAR(255),
    FOREIGN KEY (numCuenta) REFERENCES alumno(numCuenta)
);
```

---

### 3. Modificar `alumno`

**¿Por qué es necesaria?**
Permite saber rápidamente qué alumnos ya recibieron su QR.

```sql
ALTER TABLE alumno ADD COLUMN qr_enviado BOOLEAN DEFAULT FALSE;
ALTER TABLE alumno ADD COLUMN fecha_qr_enviado DATETIME;
ALTER TABLE alumno ADD COLUMN ultimo_qr_id INT;
```

---

## Lo que NO funcionará sin estos cambios

```php
// En QrModelo.php - métodos comentados:

public function guardarQrGenerado(...) {
    // ERROR: Table 'qr_codigo' doesn't exist
}

public function verificarQrUsado(...) {
    // ERROR: QR puede usarse múltiples veces
}

public function marcarQrComoUsado(...) {
    // ERROR: Sin control de accesos duplicados
}
```

---

## Script SQL Completo

```sql
-- ============================================
-- SCRIPT: Mejoras BD para Sistema de QR
-- ============================================

-- Tabla para almacenar códigos QR
CREATE TABLE IF NOT EXISTS qr_codigo (
    id_qr INT AUTO_INCREMENT PRIMARY KEY,
    numCuenta VARCHAR(20) NOT NULL,
    token_jwt TEXT NOT NULL,
    ruta_imagen VARCHAR(255),
    fecha_generacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    enviado BOOLEAN DEFAULT FALSE,
    fecha_envio DATETIME,
    usado BOOLEAN DEFAULT FALSE,
    fecha_uso DATETIME,
    metodo_envio ENUM('correo', 'telegram', 'whatsapp', 'manual') DEFAULT NULL,
    FOREIGN KEY (numCuenta) REFERENCES alumno(numCuenta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla para registrar llegadas al evento
CREATE TABLE IF NOT EXISTS llegada (
    id_llegada INT AUTO_INCREMENT PRIMARY KEY,
    numCuenta VARCHAR(20) NOT NULL,
    fecha_llegada DATETIME DEFAULT CURRENT_TIMESTAMP,
    invitados_confirmados INT DEFAULT 0,
    qr_utilizado VARCHAR(255),
    FOREIGN KEY (numCuenta) REFERENCES alumno(numCuenta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Agregar campos a tabla alumno
ALTER TABLE alumno 
    ADD COLUMN qr_enviado BOOLEAN DEFAULT FALSE,
    ADD COLUMN fecha_qr_enviado DATETIME,
    ADD COLUMN ultimo_qr_id INT;

-- Índices para búsquedas rápidas
CREATE INDEX idx_alumno_qr_enviado ON alumno(qr_enviado);
CREATE INDEX idx_qr_numCuenta ON qr_codigo(numCuenta);
CREATE INDEX idx_qr_usado ON qr_codigo(usado);
CREATE INDEX idx_llegada_numCuenta ON llegada(numCuenta);
```

---

## Orden de Implementación

1. Ejecutar script SQL
2. Activar métodos en QrModelo.php
3. Modificar ServicioQr.php para usar BD
4. Probar flujo completo

---

## Commit Sugerido

```
DOCS: Agrega propuesta de mejora para tablas de QR y llegadas

Antecedentes:
La BD actual no contempla el ciclo de vida de los QR ni
el registro de llegadas. Métodos en QrModelo comentados.

Solución:
Documenta el script SQL para crear qr_codigo, llegada y
modificar alumno. Permite control de QR usados.
```
