<?php

require_once __DIR__ . '/../modelos/AlumnoModelo.php';
require_once __DIR__ . '/../modelos/QrModelo.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class ServicioQr
{
    private $alumnoModelo;
    private $qrModelo;
    private $QR_IMAGES_PATH = __DIR__ . '/../qr_images/';
    private $SECRET_KEY;

    public function __construct()
    {
        $this->alumnoModelo = new AlumnoModel();
        $this->qrModelo = new QrModelo();
        $this->SECRET_KEY = $_SERVER['JWT_KEY'] ?? 'secret_key_por_defecto';
        
        // Crear carpeta de imágenes si no existe
        if (!file_exists($this->QR_IMAGES_PATH)) {
            mkdir($this->QR_IMAGES_PATH, 0777, true);
        }
    }

    /**
     * Genera un QR único (JWT) para un alumno
     */
    public function generarQrAlumno($data)
    {
        try {
            if (empty($data['numero_cuenta'])) {
                return $this->respuesta(false, "Número de cuenta requerido", 400);
            }

            $numCuenta = $data['numero_cuenta'];
            $alumno = $this->alumnoModelo->buscarPorNumeroCuenta($numCuenta);

            if (!$alumno) {
                return $this->respuesta(false, "Alumno no encontrado", 404);
            }

            // Generar Payload JWT (Sin expiración para uso único basado en BD)
            $payload = [
                'numero_cuenta' => $numCuenta,
                'tipo' => 'qr_acceso',
                'iat' => time()
            ];

            $jwt = JWT::encode($payload, $this->SECRET_KEY, 'HS256');

            // Generar Imagen QR (endroid/qr-code)
            // Nota: Se asume que la librería está instalada
            $qrCode = QrCode::create($jwt);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            $fileName = $numCuenta . '.png';
            $filePath = $this->QR_IMAGES_PATH . $fileName;
            $result->saveToFile($filePath);

            /* 
            TODO: Descomentar cuando exista la tabla 'qr_codigo'
            $this->qrModelo->guardarQrGenerado($numCuenta, $jwt, $filePath);
            */

            return $this->respuesta(true, "QR generado con éxito", 200, [
                'token' => $jwt,
                'imagen_path' => 'API/qr_images/' . $fileName
            ]);

        } catch (Exception $e) {
            return $this->respuesta(false, "Error al generar QR: " . $e->getMessage(), 500);
        }
    }

    /**
     * Valida el token del QR y retorna los datos del alumno
     */
    public function validarQrToken($data)
    {
        try {
            if (empty($data['token'])) {
                return $this->respuesta(false, "Token de QR no proporcionado", 400);
            }

            $token = $data['token'];

            // Decodificar JWT
            try {
                $decoded = JWT::decode($token, new Key($this->SECRET_KEY, 'HS256'));
            } catch (Exception $e) {
                return $this->respuesta(false, "Token inválido o expirado", 401);
            }

            if ($decoded->tipo !== 'qr_acceso') {
                return $this->respuesta(false, "Tipo de token no válido", 401);
            }

            $numCuenta = $decoded->numero_cuenta;

            /* 
            TODO: Descomentar cuando exista la tabla 'qr_codigo'
            if ($this->qrModelo->verificarQrUsado($token)) {
                return $this->respuesta(false, "Este código QR ya fue utilizado", 409);
            }
            */

            // Obtener datos del alumno
            $alumno = $this->alumnoModelo->buscarPorNumeroCuenta($numCuenta);
            if (!$alumno) {
                return $this->respuesta(false, "Alumno no encontrado en la base de datos", 404);
            }

            // Obtener asiento
            $asiento = $this->qrModelo->obtenerAsientoAlumno($numCuenta);
            
            // Obtener estado de asistencia
            $estadoConfirmacion = $this->alumnoModelo->verificarConfirmacion($numCuenta);
            
            $dataResponse = [
                'numCuenta' => $alumno['numCuenta'],
                'nombre' => $alumno['nombre'],
                'apellido' => $alumno['apellido'],
                'cantInvitado' => $alumno['cantInvitado'],
                'asiento' => $asiento ? $asiento['letra'] . $asiento['numero'] : 'No asignado',
                'estadoAsistencia' => ($estadoConfirmacion == 1) ? "Si" : (($estadoConfirmacion == 0 && $estadoConfirmacion !== false) ? "No" : "Pendiente")
            ];

            return $this->respuesta(true, "Token válido", 200, $dataResponse);

        } catch (Exception $e) {
            return $this->respuesta(false, "Error en validación: " . $e->getMessage(), 500);
        }
    }

    /**
     * Confirma la llegada del alumno
     */
    public function confirmarLlegadaQr($data)
    {
        try {
            if (empty($data['numero_cuenta'])) {
                return $this->respuesta(false, "Número de cuenta requerido", 400);
            }

            $numCuenta = $data['numero_cuenta'];
            $alumno = $this->alumnoModelo->buscarPorNumeroCuenta($numCuenta);

            if (!$alumno) {
                return $this->respuesta(false, "Alumno no encontrado", 404);
            }

            // Verificar si ya confirmó
            $estadoActual = $this->alumnoModelo->verificarConfirmacion($numCuenta);
            
            if ($estadoActual === 1) {
                return $this->respuesta(false, "El alumno ya fue registrado anteriormente", 409);
            }

            // Si no existe registro o el estado es 0, actualizamos a 1 (Llegó)
            // Reutilizamos actualizarConfirmacion o creamos una lógica específica de llegada
            // Según la tabla asistencia (numCuenta, estado), estado 1 es "Llegó/Confirmado"
            
            // Usamos AlumnoModelo->actualizarConfirmacion pero adaptado si es necesario
            // En AlumnoModelo: actualizarConfirmacion($idAlumno, $asistira, $numInvitados)
            // Le pasamos asistira = true (1), y mantenemos sus invitados actuales
            $resultado = $this->alumnoModelo->actualizarConfirmacion($numCuenta, true, (int)$alumno['cantInvitado']);

            if (!$resultado || (is_array($resultado) && !$resultado['success'])) {
                return $this->respuesta(false, "Fallo al registrar llegada", 500);
            }

            /* 
            TODO: Descomentar cuando exista la tabla 'qr_codigo'
            $this->qrModelo->marcarQrComoUsado($numCuenta);
            */

            return $this->respuesta(true, "Llegada registrada exitosamente", 200);

        } catch (Exception $e) {
            return $this->respuesta(false, "Error al confirmar llegada: " . $e->getMessage(), 500);
        }
    }

    private function respuesta($success, $message, $code, $data = null)
    {
        http_response_code($code);
        return [
            "success" => $success,
            "message" => $message,
            "data" => $data
        ];
    }
}
