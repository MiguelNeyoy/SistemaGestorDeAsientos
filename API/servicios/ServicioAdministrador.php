<?php

require_once __DIR__ . '/../modelos/AlumnoModelo.php';
require_once __DIR__ . '/../modelos/AdministradorModelo.php';

use Firebase\JWT\JWT;

class ServicioAdministrador
{
    private $modeloAlumno;
    private $modeloAdmin;

    public function __construct()
    {
        $this->modeloAlumno = new AlumnoModel();
        $this->modeloAdmin = new AdministradorModelo();
    }

    public function loginAdmin($data)
    {
        if (empty($data['usuario']) || empty($data['contrasena'])) {
            return $this->respuesta(false, "Usuario y contraseña requeridos", 400);
        }

        $admin = $this->modeloAdmin->verificarAdministrador($data['usuario']);

        if ($admin && password_verify($data['contrasena'], $admin['contrasena'])) {
            // Generate JWT
            $secret_key = $_SERVER['JWT_KEY'];
            $issuer_claim = "http://asientos.local";
            $audience_claim = "http://asientos.local";
            $issuedat_claim = time();
            $expire_claim = $issuedat_claim + (86400); // expira en 24h
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "role" => "admin",
                    "admin_id" => $admin['admin_id']
                )
            );

            $jwt = JWT::encode($token, $secret_key, 'HS256');
            return $this->respuesta(true, "Inicio de sesión exitoso", 200, ['token' => $jwt]);
        }

        return $this->respuesta(false, "Credenciales incorrectas", 401);
    }

    public function obtenerTodosAlumnos()
    {
        $alumnos = $this->modeloAlumno->obtenerAlumnos();
        return $this->respuesta(true, "Listado de alumnos", 200, $alumnos);
    }

    public function obtenerMetricas()
    {
        $metricas = $this->modeloAdmin->obtenerMetricas();
        return $this->respuesta(true, "Métricas obtenidas", 200, $metricas);
    }

    public function editarAlumno($data)
    {
        if (empty($data['numCuenta'])) {
            return $this->respuesta(false, "Número de cuenta es requerido", 400);
        }

        // 1. Validar e actualizar correo si fue proporcionado
        if (isset($data['correo']) && trim($data['correo']) !== '') {
            if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
                return $this->respuesta(false, "Correo inválido", 400);
            }
            $this->modeloAlumno->actualizarCorreo($data['numCuenta'], $data['correo']);
        }

        // 2. Lógica de Asistencia y Número de invitados
        if (isset($data['asistencia_estado']) && isset($data['num_invitados'])) {
            $estado = (int)$data['asistencia_estado']; // 1 o 0
            $invitados = (int)$data['num_invitados'];

            if ($invitados < 0 || $invitados > 4) {
                return $this->respuesta(false, "El límite de invitados es 4", 400);
            }

            if ($estado === 0) {
                $invitados = 0;
            }

            // Actualizar la confirmación/estado y cantidad de invitados directamente en la BD
            $correo = $data['correo'] ?? null;
            $resultado = $this->modeloAlumno->actualizarConfirmacion($data['numCuenta'], $estado, $invitados, $correo);

            if (!$resultado || (is_array($resultado) && !$resultado['success'])) {
                return $this->respuesta(false, "Error al actualizar la asistencia", 500);
            }

            // TODO: LOGICA DE ASIENTOS COMPENDIDA (PENDIENTE)
            // Aqui se requerira instanciar el ServicioAsientos o llamar la logica
            // para asignar o liberar el lugar del alumno y de sus invitados. 
            // Esto depende del cambio de 'estado' (0 a 1 o viceversa), así que quedará en espera.
            // Ejemplo de uso a futuro:
            // if ($estado === 1) {
            //     $this->reservarAsientosTeatro($data['numCuenta'], $invitados + 1);
            // } else {
            //     $this->liberarAsientosTeatro($data['numCuenta']);
            // }
        }

        return $this->respuesta(true, "Datos del alumno actualizados correctamente", 200);
    }

    private function respuesta($success, $message, $code, $data = null)
    {
        http_response_code($code);
        $res = [
            "success" => $success,
            "message" => $message
        ];
        if ($data !== null) {
            $res["data"] = $data;
        }
        return $res;
    }
}
