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

    public function actualizarCorreoAlumno($data)
    {
        if (empty($data['numCuenta']) || empty($data['correo'])) {
            return $this->respuesta(false, "Número de cuenta y correo son requeridos", 400);
        }

        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            return $this->respuesta(false, "Correo inválido", 400);
        }

        $actualizado = $this->modeloAlumno->actualizarCorreo($data['numCuenta'], $data['correo']);
        if ($actualizado) {
            return $this->respuesta(true, "Correo actualizado correctamente", 200);
        } else {
            return $this->respuesta(false, "No se pudo actualizar el correo o el alumno no existe", 404);
        }
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
