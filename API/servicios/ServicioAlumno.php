<?php

/**
 *  
 * En la carpeta de servicio es donde va la logia de la toda la aplicacion
 * Reglas de la app si no pueden pasar mas de 4 invitados 
 * creacion de qr. ect.
*/
// servicios/ServicioAlumno.php
require_once __DIR__ . '/../modelos/AlumnoModelo.php';

class ServicioAlumno {
    private $modelo;
    
    public function __construct() {
        $this->modelo = new AlumnoModelo();
    }
    
    public function validarAlumno($numero_cuenta) {
        // Validar formato
        if (!preg_match('/^\d{9}$/', $numero_cuenta)) {
            return [
                'exito' => false,
                'error' => 'El número de cuenta debe tener 9 dígitos'
            ];
        }
        
        // Buscar alumno
        $alumno = $this->modelo->buscarPorNumeroCuenta($numero_cuenta);
        
        if (!$alumno) {
            return [
                'exito' => false,
                'error' => 'Número de cuenta no encontrado'
            ];
        }
        
        // Verificar si ya confirmó
        $alumno['ya_confirmo'] = $this->modelo->verificarConfirmacion($alumno['id_alumno']);
        
        return [
            'exito' => true,
            'datos' => $alumno
        ];
    }
    
    public function registrarConfirmacion($id_alumno, $datos) {
        // Validaciones
        if (!in_array($datos['asistira'], ['si', 'no'])) {
            return [
                'exito' => false,
                'error' => 'Respuesta de asistencia no válida'
            ];
        }
        
        if ($datos['asistira'] === 'si') {
            if (!isset($datos['num_invitados']) || $datos['num_invitados'] < 0 || $datos['num_invitados'] > 4) {
                return [
                    'exito' => false,
                    'error' => 'Número de invitados debe ser entre 0 y 4'
                ];
            }
        } else {
            $datos['num_invitados'] = 0;
        }
        
        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            return [
                'exito' => false,
                'error' => 'Correo electrónico no válido'
            ];
        }
        
        // Verificar que no haya confirmado antes
        if ($this->modelo->verificarConfirmacion($id_alumno)) {
            return [
                'exito' => false,
                'error' => 'Este alumno ya confirmó su asistencia'
            ];
        }
        
        // Registrar
        if ($this->modelo->actualizarConfirmacion($id_alumno, $datos)) {
            return [
                'exito' => true,
                'mensaje' => 'Confirmación registrada exitosamente'
            ];
        } else {
            return [
                'exito' => false,
                'error' => 'Error al registrar la confirmación'
            ];
        }
    }
}
?>