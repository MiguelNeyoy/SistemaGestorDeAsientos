# Propuesta: Coexistencia de Sesiones (Admin y Alumno)

Actualmente, el sistema limpia la sesión del rol opuesto para evitar conflictos. Si en el futuro se desea que un Administrador pueda mantener su sesión activa mientras prueba el flujo de un Alumno en la misma pestaña/navegador, se sugieren las siguientes estrategias técnicas.

## Opción A: Espacios de Nombres en `$_SESSION` (Recomendado)

En lugar de tener variables sueltas en la raíz de `$_SESSION`, agrupamos todo bajo una llave específica para cada rol.

### 1. Modificar Inicio de Sesión Alumno
```php
// En index.php
$_SESSION['auth_alumno'] = [
    'token' => $data['token'],
    'tipo' => 'alumno'
];
```

### 2. Modificar Inicio de Sesión Admin
```php
// En loginAdmin.php
$_SESSION['auth_admin'] = [
    'token' => $data['data']['token'],
    'tipo' => 'admin'
];
```

### 3. Actualizar `auth_middleware.php`
La lógica de `verify_access` cambiaría para buscar en la "caja" correspondiente:

```php
function verify_access($rolesPermitidos = ['alumno', 'admin']) {
    // Si la página busca un alumno
    if (in_array('alumno', $rolesPermitidos) && isset($_SESSION['auth_alumno'])) {
        return [
            'isLoggedIn' => true,
            'tipo' => 'alumno',
            'token' => $_SESSION['auth_alumno']['token']
        ];
    }
    // Si la página busca un admin
    if (in_array('admin', $rolesPermitidos) && isset($_SESSION['auth_admin'])) {
        return [
            'isLoggedIn' => true,
            'tipo' => 'admin',
            'token' => $_SESSION['auth_admin']['token']
        ];
    }
    // ... redirección si nada coincide
}
```

## Opción B: Diferentes `session_name()`

PHP permite asignar nombres distintos a las cookies de sesión. Podrías definir una sesión diferente para el panel de administración.

### 1. En archivos de Alumno (FrontEnd/*.php)
```php
session_name("SESS_ALUMNO");
session_start();
```

### 2. En archivos de Admin (FrontEnd/admin/*.php)
```php
session_name("SESS_ADMIN");
session_start();
```

**Ventaja:** Son totalmente independientes; cerrar una no afecta a la otra y no hay riesgo de colisión de variables.
**Desventaja:** Requiere asegurar que el `session_name` se defina antes de cada `session_start()` en todo el proyecto.

## Recomendación
La **Opción A (Espacios de Nombres)** es la más limpia y fácil de mantener en este proyecto, ya que permite que `auth_middleware.php` gestione ambos contextos de forma centralizada sin cambiar cómo PHP maneja las cookies.
