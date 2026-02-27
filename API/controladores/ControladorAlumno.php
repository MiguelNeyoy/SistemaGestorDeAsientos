<?php

require_once(__DIR__ . '/../servicios/ServicioAlumno.php');

class AlumnoController{
    private $servicioAlumno;

    public function __construct()
    {
        $this->servicioAlumno = new AlumnoServicio();
    }

    /**
     * Recibe el número de cuenta y llama al servicio que busca al alumno por número de cuenta.
     * Responde 200 con datos si se encuentra, 404 en caso contrario.
     */
    public function validarAlumno($numero_cuenta) {
        $isValid = $this->servicioAlumno->validarAlumno($numero_cuenta);
        if ($isValid) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "numero_cuenta" => $numero_cuenta
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Alumno no encontrado"]);
        }
    }

    /** 
     * Recibe el número de cuenta y llama al servicio para registrar la asistencia.
     * El servicio valida que el alumno exista y actualiza su estado.
     * Responde 200 OK o 400 en caso de fallo.
     */
    public function confirmarAsistencia($numero_de_cuenta){
        $res = $this->servicioAlumno->registrarAsistencia($numero_de_cuenta);
        if ($res) {
            http_response_code(200);
            echo json_encode([
                "success" => true,
                "mensaje" => "Asistencia registrada"
            ]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "No fue posible registrar la asistencia"]);
        }
    }

    /**
     * Obtiene el estado actual del alumno (por ejemplo estado de confirmación).
     * Devuelve 200 con el estado o 404 si el alumno no existe.
     */
   public function obtenerEstado($numero_de_cuenta){
        $estado = $this->servicioAlumno->obtenerEstado($numero_de_cuenta);
        if ($estado !== null) {
            http_response_code(200);
            echo json_encode([
                "numero_cuenta" => $numero_de_cuenta,
                "estado" => $estado
            ]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Alumno no encontrado"]);
        }
   }

}
