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

class ServicioAlumno
{

    private $modelo;

    public function __construct()
    {
        $this->modelo = new AlumnoModel();
    }

    public function buscarAlumno($data)
    {

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

        // Consultar si el alumno ya confirmó su asistencia en la tabla 'asistencia'
        $estadoConfirmacion = $this->modelo->verificarConfirmacion($data['numero_cuenta']);

        // Si verificarConfirmacion retorna false, significa que no hay registro → "Pendiente"
        // Si retorna 1 → "Si" (confirmó que asiste)
        // Si retorna 0 → "No" (confirmó que no asiste)
        if ($estadoConfirmacion === false) {
            $alumno['asistencia'] = "Pendiente";
        } else {
            $alumno['asistencia'] = ($estadoConfirmacion == 1) ? "Si" : "No";
        }

        return $this->respuesta(true, "Alumno encontrado", 200, $alumno);
    }

    public function confirmarAsistencia($data)
    {
        // Validar datos obligatorios
        if (!isset($data['id_alumno'], $data['asistira'], $data['correo'])) {
            return $this->respuesta(false, "Datos incompletos", 400);
        }

        // Validar correo (solo exigirlo si sí van a asistir)
        if ($data['asistira'] == 1 && !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            return $this->respuesta(false, "Correo inválido", 400);
        }

        //Validar si ya confirmo asistencia
        $estadoConfirmacion = $this->modelo->verificarConfirmacion($data['id_alumno']);
        if ($estadoConfirmacion !== false || $estadoConfirmacion === 1) {
            return $this->respuesta(false, "El alumno ya confirmó asistencia", 409);
        }

        // Validar asistencia
        if (!in_array($data['asistira'], [0, 1, true, false], true)) {
            return $this->respuesta(false, "Valor de asistencia inválido", 400);
        }

        // Verificar que el alumno existe antes de continuar
        $alumno = $this->modelo->buscarPorNumeroCuenta($data['id_alumno']);
        if (!$alumno) {
            return $this->respuesta(false, "Alumno no encontrado", 404);
        }
        // Verificar si el correo es diferente al del alumno y actualizarlo (solo si asisten)
        if ($data['asistira'] == 1) {
            if (empty($alumno['email']) || $alumno['email'] !== $data['correo']) {
                // Actualizar el correo y continuar con la confirmación
                $this->modelo->actualizarCorreo($data['id_alumno'], $data['correo']);
            }
        }

        $asistira = $data['asistira'] ? 1 : 0;
        $numInvitados = isset($data['num_invitados']) ? (int) $data['num_invitados'] : 0;

        if ($numInvitados < 0 || $numInvitados > 6) {
            return $this->respuesta(false, "Máximo 6 invitados", 400);
        }

        if ($asistira === 0) {
            $numInvitados = 0;
        }

        // Verificar si el alumno ya confirmó su asistencia
        // Si retorna algo diferente de false, significa que ya existe un registro
        $estadoConfirmacion = $this->modelo->verificarConfirmacion($data['id_alumno']);
        if ($estadoConfirmacion !== false) {
            return $this->respuesta(false, "El alumno ya confirmó asistencia", 409);
        }

        $guardado = $this->modelo->actualizarConfirmacion(
            $data['id_alumno'],
            $asistira,
            $numInvitados,
            $data['correo']
        );

        if (!$guardado || (is_array($guardado) && !$guardado['success'])) {
            $mensaje = is_array($guardado) ? $guardado['error'] : "Error al guardar confirmación";
            return $this->respuesta(false, $mensaje, 500);
        }

        return $this->respuesta(true, "Confirmación guardada correctamente", 200);
    }

    private function respuesta($success, $message, $code, $data = null)
    {
        http_response_code($code);

        // Retornar arreglo en lugar de JSON codificado
        return [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ];
    }
    public function actualizarCorreo($data)
    {
        if (!isset($data['id_alumno'], $data['correo'])) {
            return $this->respuesta(false, "Datos incompletos", 400);
        }
        $estadoConfirmacion = $this->modelo->verificarConfirmacion($data['id_alumno']);
        if ($estadoConfirmacion !== false || $estadoConfirmacion === 1) {
            return $this->respuesta(false, "El alumno ya confirmó asistencia", 409);
        }

        if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
            return $this->respuesta(false, "Correo inválido", 400);
        }

        // $alumnos = $this->modelo->obtenerAlumnos();

        // foreach ($alumnos as $alumno) {
        //     if ($alumno['email'] == $data['correo']) {
        //         return $this->respuesta(false, "El correo ya existe", 409);
        //     }
        // }

        $actualizado = $this->modelo->actualizarCorreo($data['id_alumno'], $data['correo']);
        if (!$actualizado) {
            return $this->respuesta(false, "Error al actualizar correo", 500);
        }
        return $this->respuesta(true, "Correo actualizado correctamente", 200);
    }
}
