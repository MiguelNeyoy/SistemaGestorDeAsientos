<?php
require_once(__DIR__ . '/../servicios/ServicioAlumno.php');
require_once(__DIR__ . '/../modelos/AlumnoModelo.php');

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
     * Recibe el número de cuenta y llama al servicio que busca al alumno por número de cuenta.
     * Responde 200 con datos si se encuentra, 404 en caso contrario.
     */
    public function validarAlumno($numero_cuenta)
    {
        $respuestaDelServicio = $this->servicioAlumno->buscarAlumno(['numero_cuenta' => $numero_cuenta]);

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
        $input = json_decode(file_get_contents('php://input'), true);

        // Pasar el arreglo completo al servicio
        $res = $this->servicioAlumno->confirmarAsistencia($input ?? []);

        // El servicio ya establece el código HTTP y retorna un arreglo
        echo json_encode($res);
    }

    public function actualizarCorreo()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $res = $this->servicioAlumno->actualizarCorreo($input ?? []);
        echo json_encode($res);
    }
}
