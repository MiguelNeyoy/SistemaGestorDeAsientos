<?php

require_once __DIR__ . '/../modelos/AlumnoModelo.php';
require_once __DIR__ . '/../modelos/AdministradorModelo.php';
require_once __DIR__ . '/../modelos/ModeloAsiento.php';

use Firebase\JWT\JWT;

class ServicioAdministrador
{
    private $modeloAlumno;
    private $modeloAdmin;
    private $modeloAsiento;

    public function __construct()
    {
        $this->modeloAlumno = new AlumnoModel();
        $this->modeloAdmin = new AdministradorModelo();
        $this->modeloAsiento = new ModeloAsiento();
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
        $alumnosConfirmados = $this->modeloAdmin->obtenerAlumnosConfirmados();

        $total_invitados = 0;
        $por_grupo = [
            'LI4-1' => 0,
            'LI4-2' => 0,
            'LISI4-1' => 0,
            'LISI4-2' => 0
        ];
        $individual = [];

        foreach ($alumnosConfirmados as $alumno) {
            $invitados = (int) $alumno['cantInvitado'];
            $total_invitados += $invitados;

            $grupo = $this->calcularGrupo($alumno['carrera'], $alumno['turno']);
            if (isset($por_grupo[$grupo])) {
                $por_grupo[$grupo] += $invitados;
            }

            $individual[] = [
                'numCuenta' => $alumno['numCuenta'],
                'nombre' => trim($alumno['nombre']) . ' ' . trim($alumno['apellido']),
                'grupo' => $grupo,
                'invitados' => $invitados
            ];
        }

        return $this->respuesta(true, "Métricas obtenidas", 200, [
            'total_confirmados' => count($alumnosConfirmados),
            'total_invitados' => $total_invitados,
            'total_asientos' => count($alumnosConfirmados) + $total_invitados,
            'por_grupo' => $por_grupo,
            'individual' => $individual
        ]);
    }

    private function calcularGrupo($carrera, $turno)
    {
        $carLower = strtolower(trim($carrera));
        $turnoUpper = strtoupper(trim($turno));

        $prefix = 'LISI'; // Por defecto Licenciatura en Ingeniería (Sistemas)
        if (strpos($carLower, 'informática') !== false || strpos($carLower, 'informatica') !== false) {
            $prefix = 'LI';
        }

        $turnoNum = ($turnoUpper === 'M' || $turnoUpper === '1') ? '1' : '2';

        return "{$prefix}4-{$turnoNum}";
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
            $prevEstado = $this->modeloAlumno->verificarConfirmacion($data['numCuenta']);

            $dropdownVal = (int) $data['asistencia_estado'];
            $estado = ($dropdownVal === 1) ? 1 : 0;
            $invitados = ($estado === 0) ? 0 : (int) $data['num_invitados'];

            if ($invitados < 0 || $invitados > 4) {
                return $this->respuesta(false, "El límite de invitados es 4", 400);
            }

            $resultado = $this->modeloAlumno->actualizarConfirmacion($data['numCuenta'], $estado, $invitados);

            if (!$resultado || (is_array($resultado) && !$resultado['success'])) {
                return $this->respuesta(false, "Error al actualizar la asistencia", 500);
            }

            if ($estado === 1 && ($prevEstado === false || $prevEstado === 0)) {
                $servicioAsientos = new ServicioAsientos();
                $servicioAsientos->reAsignarEvento($data['numCuenta']);
            } elseif ($estado === 0 && $prevEstado === 1) {
                $this->modeloAsiento->liberarAsientoPorAlumno($data['numCuenta']);
            }
        }

        return $this->respuesta(true, "Datos del alumno actualizados correctamente", 200);
    }

    public function resetearConfirmaciones()
    {
        try {
            $this->modeloAlumno->resetearConfirmaciones();
            return $this->respuesta(true, "Confirmaciones reseteadas correctamente", 200);
        } catch (Exception $e) {
            return $this->respuesta(false, "Error al resetear: " . $e->getMessage(), 500);
        }
    }

    public function obtenerEscaneados($evento)
    {
        $eventosPermitidos = ['li', 'lisi'];
        if (!in_array($evento, $eventosPermitidos)) {
            return $this->respuesta(false, "Evento no válido. Use 'li' o 'lisi'.", 400);
        }

        try {
            $alumnos = $this->modeloAlumno->obtenerEscaneadosPorEvento($evento);

            $tituloEvento = ($evento === 'li')
                ? 'Licenciatura en Informática (LI)'
                : 'Lic. en Ingeniería en Sistemas (LISI)';

            return $this->respuesta(true, "Alumnos escaneados obtenidos", 200, [
                'evento' => $tituloEvento,
                'total' => count($alumnos),
                'alumnos' => $alumnos
            ]);
        } catch (\Exception $e) {
            return $this->respuesta(false, "Error al obtener escaneados: " . $e->getMessage(), 500);
        }
    }

    public function eliminarAlumnos($data)
    {
        if (empty($data['alumnos']) || !is_array($data['alumnos'])) {
            return $this->respuesta(false, "Debe proporcionar un array de números de cuenta", 400);
        }

        $alumnos = $data['alumnos'];
        $eliminados = [];
        $errores = [];

        foreach ($alumnos as $numCuenta) {
            try {
                $db = $this->modeloAlumno->getDb();
                $db->beginTransaction();

                $this->modeloAsiento->liberarAsientoPorAlumno($numCuenta);
                $this->modeloAlumno->eliminarAsistencia($numCuenta);
                $this->modeloAlumno->eliminarQr($numCuenta);
                $resultado = $this->modeloAlumno->eliminarAlumno($numCuenta);

                if ($resultado) {
                    $db->commit();
                    $eliminados[] = $numCuenta;
                } else {
                    $db->rollBack();
                    $errores[] = ['numCuenta' => $numCuenta, 'error' => 'Alumno no encontrado'];
                }
            } catch (Exception $e) {
                $db?->rollBack();
                $errores[] = ['numCuenta' => $numCuenta, 'error' => $e->getMessage()];
            }
        }

        if (count($eliminados) > 0) {
            $msg = count($errores) > 0
                ? "Se eliminaron " . count($eliminados) . " de " . count($alumnos) . " alumno(s)"
                : "Se eliminaron " . count($eliminados) . " alumno(s) permanentemente";
            return $this->respuesta(true, $msg, 200, [
                'eliminados' => $eliminados,
                'errores' => $errores
            ]);
        }

        return $this->respuesta(false, "No se pudo eliminar ningún alumno", 400, [
            'eliminados' => $eliminados,
            'errores' => $errores
        ]);
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
