<?php

/**
 *  
 * En la carpeta de servicio es donde va la logica de la toda la aplicacion
 * Reglas de la app si no pueden pasar mas de 4 invitados 
 * creacion de QR
 * Servicios para el administracion
*/
// servicios/ServicioAlumno.php

require_once __DIR__ . '/../modelos/AlumnoModelo.php';

class ServicioAlumno {

    private $modelo;

    public function __construct() {
        $this->modelo = new AlumnoModel();
    }

    public function buscarAlumno($data) {

        if (empty($data['numero_cuenta'])) {
            return $this->respuesta(false, "Número de cuenta requerido", 400);
        }

        if (!ctype_digit($data['numero_cuenta'])) {
            return $this->respuesta(false, "Número de cuenta inválido", 400);
        }

        $alumno = $this->modelo->buscarPorNumeroCuenta($data['numero_cuenta']);

        if (!$alumno) {
            return $this->respuesta(false, "Alumno no encontrado", 404);
        }

        return $this->respuesta(true, "Alumno encontrado", 200, $alumno);
    }

 public function confirmarAsistencia($data) {

    if (!isset($data['id_alumno'], $data['asistira'], $data['num_invitados'], $data['correo'])) {
        return $this->respuesta(false, "Datos incompletos", 400);
    }

    if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
        return $this->respuesta(false, "Correo inválido", 400);
    }

    if (!in_array($data['asistira'], [0, 1, true, false], true)) {
        return $this->respuesta(false, "Valor de asistencia inválido", 400);
    }

    $asistira = $data['asistira'] ? 1 : 0;

    $numInvitados = (int) $data['num_invitados'];

    if ($numInvitados < 0 || $numInvitados > 4) {
        return $this->respuesta(false, "Máximo 4 invitados", 400);
    }

    if ($asistira === 0) {
        $numInvitados = 0;
    }

    if ($this->modelo->verificarConfirmacion($data['id_alumno'])) {
        return $this->respuesta(false, "El alumno ya confirmó asistencia", 409);
    }

    $guardado = $this->modelo->actualizarConfirmacion(
        $data['id_alumno'],
        $asistira,
        $numInvitados,
        $data['correo']
    );

    if (!$guardado) {
        return $this->respuesta(false, "Error al guardar confirmación", 500);
    }

    return $this->respuesta(true, "Confirmación guardada correctamente", 200);
}

    private function respuesta($success, $message, $code, $data = null) {
        http_response_code($code);

        // Retornar arreglo en lugar de JSON codificado
        return [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ];
    }
}
