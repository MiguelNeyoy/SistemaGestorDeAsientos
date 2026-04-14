# SistemaGestorDeAsientos

## Setup
```bash
composer install && cd API && composer install  # Two separate installs needed
cp API/.env.example API/.env                    # Configure DB + JWT_KEY
```

## Dev URLs
- Frontend: `http://localhost/SistemaGestorDeAsientos/FrontEnd/`
- API: `http://localhost/SistemaGestorDeAsientos/API/publico/`

## Architecture
- **API entry**: `API/publico/index.php` - routing, JWT validation, CORS headers
- **Controllers**: `API/controladores/` (Alumno, Administrador, Asientos, Qr)
- **Frontend entry**: `FrontEnd/index.php` → `view_confirmacion.php` → `asientos.php`

## Rules
- Reglas par crear un Enpoint usa el siguiente archivo: '/instrucciones/instruccionesEndPoint.md'

## API Auth
- JWT tokens expire in 1 hour
- Student JWT: `$_SERVER['JWT_NUMERO_CUENTA']` injected after validation
- Admin JWT: `$_SERVER['JWT_ADMIN_ID']` injected; requires `role: admin` in payload
- Protected student routes: `/alumnos/asistencia`, `/alumnos/correo`, `/alumnos/estado`
- Protected admin routes: `/admin/alumnos`, `/admin/metricas`, `/admin/alumnos/correo`

## Testing
```bash
bash API/test_api.sh   # Update CUENTA_TEST + ADMIN_USER/PASS first
```

## Quirks
- `CURLOPT_SSL_VERIFYPEER => false` in dev (cURL calls)
- `FrontEnd/config.php` auto-detects local vs production (Hostinger)
- API base URL in `FrontEnd/config.php` line 7-11

