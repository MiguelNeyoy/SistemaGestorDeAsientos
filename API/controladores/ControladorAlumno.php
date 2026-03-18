<?php
require_once(__DIR__ . '/../servicios/ServicioAlumno.php');
require_once(__DIR__ . '/../modelos/AlumnoModelo.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class ControladorAlumno
{
    private $servicioAlumno;
    private $modeloAlumno;

    public function __construct()
    {
        $this->servicioAlumno = new ServicioAlumno();
        $this->modeloAlumno = new AlumnoModel();
    }

    public function obtenerAlumnos()
    {
        $alumnos = $this->modeloAlumno->obtenerAlumnos();
        echo json_encode($alumnos);
    }

    /**
     * Recibe el número de cuenta vía POST (body) y llama al servicio.
     * Si es exitoso, genera un token JWT.
     */
    public function validarAlumno()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $numero_cuenta = $input['numero_cuenta'] ?? '';

        $respuestaDelServicio = $this->servicioAlumno->buscarAlumno(['numero_cuenta' => $numero_cuenta]);

        // Si fue exitoso y el alumno fue encontrado, agregamos el token
        if (isset($respuestaDelServicio['success']) && $respuestaDelServicio['success'] === true) {
            $secret_key = "secreto_super_seguro_asientos";
            $issuer_claim = "http://asientos.local";
            $audience_claim = "http://asientos.local";
            $issuedat_claim = time(); // tiempo de emisión
            $expire_claim = $issuedat_claim + (60 * 60); // expira en 24 horas
            $token = array(
                "iss" => $issuer_claim,
                "aud" => $audience_claim,
                "iat" => $issuedat_claim,
                "exp" => $expire_claim,
                "data" => array(
                    "numero_cuenta" => $numero_cuenta
                )
            );

            $jwt = JWT::encode($token, $secret_key, 'HS256');
            $respuestaDelServicio['token'] = $jwt;
        }

        echo json_encode($respuestaDelServicio);
    }

    /** 
     * Recibe los datos de confirmación de asistencia vía POST y actualiza el estado del alumno.
     * Requiere: id_alumno, asistira, num_invitados, correo
     * Responde 200 OK, 400, 404, 409 o 500 según el resultado.
     */
    public function confirmarAsistencia()
    {
        // Obtener datos del cuerpo de la solicitud
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        // Inyectar el id_alumno desde el token validado por index.php
        if (isset($_SERVER['JWT_NUMERO_CUENTA'])) {
            $input['id_alumno'] = $_SERVER['JWT_NUMERO_CUENTA'];
        }

        // Pasar el arreglo completo al servicio
        $res = $this->servicioAlumno->confirmarAsistencia($input);

        // El servicio ya establece el código HTTP y retorna un arreglo
        echo json_encode($res);
    }

    public function actualizarCorreo()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];

        // Inyectar el id_alumno desde el token validado por index.php
        if (isset($_SERVER['JWT_NUMERO_CUENTA'])) {
            $input['id_alumno'] = $_SERVER['JWT_NUMERO_CUENTA'];
        }

        $res = $this->servicioAlumno->actualizarCorreo($input);
        echo json_encode($res);
    }
}
