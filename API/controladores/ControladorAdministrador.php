<?php

require_once(__DIR__ . '/../servicios/ServicioAdministrador.php');
require_once(__DIR__ . '/../servicios/ServicioAsientos.php');

class ControladorAdministrador
{
    private $servicioAdmin;

    public function __construct()
    {
        $this->servicioAdmin = new ServicioAdministrador();
    }

    public function loginAdmin()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $respuesta = $this->servicioAdmin->loginAdmin($input);
        echo json_encode($respuesta);
    }

    public function obtenerTodosAlumnos()
    {
        $respuesta = $this->servicioAdmin->obtenerTodosAlumnos();
        echo json_encode($respuesta);
    }

    public function obtenerMetricas()
    {
        $respuesta = $this->servicioAdmin->obtenerMetricas();
        echo json_encode($respuesta);
    }

    public function editarAlumno()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $respuesta = $this->servicioAdmin->editarAlumno($input);
        echo json_encode($respuesta);
    }

    public function resetearConfirmaciones()
    {
        $respuesta = $this->servicioAdmin->resetearConfirmaciones();
        echo json_encode($respuesta);
    }

    public function exportarEscaneados($evento)
    {
        $respuesta = $this->servicioAdmin->obtenerEscaneados($evento);
        echo json_encode($respuesta);
    }

    public function eliminarAlumnos()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $respuesta = $this->servicioAdmin->eliminarAlumnos($input);
        echo json_encode($respuesta);
    }

    public function limpiarAsignaciones()
    {
        $servicio = new ServicioAsientos();
        $resultado = $servicio->limpiarAsignaciones();
        echo json_encode($resultado);
    }

    public function ejecutarAsignacion()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $dryRun = isset($data['dry_run']) ? (bool)$data['dry_run'] : false;
        $servicio = new ServicioAsientos();
        $resultado = $servicio->ejecutarAsignacion($dryRun);
        echo json_encode($resultado);
    }

    public function estadoAsignacion()
    {
        $servicio = new ServicioAsientos();
        $resultado = $servicio->obtenerEstadoAsignacion();
        echo json_encode($resultado);
    }

    public function publicarResultados()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $publicado = isset($data['publicado']) ? (bool)$data['publicado'] : false;
        $servicio = new ServicioAsientos();
        $resultado = $servicio->publicarResultados($publicado);
        echo json_encode($resultado);
    }

    public function enviarQRs()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        require_once __DIR__ . '/../servicios/ServicioCorreo.php';
        $servicio = new ServicioCorreo();
        $respuesta = $servicio->enviarQRsPorGrupo($input['carrera'] ?? '', $input['turno'] ?? '');
        echo json_encode($respuesta);
    }

    public function enviarQRIndividual()
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        require_once __DIR__ . '/../servicios/ServicioCorreo.php';
        $servicio = new ServicioCorreo();
        $respuesta = $servicio->enviarQRIndividual($input['numCuenta'] ?? '');
        echo json_encode($respuesta);
    }

    public function testConexion()
    {
        $resultados = [];

        // 1. PHP curl extension
        $resultados['curl_extension'] = extension_loaded('curl');
        $resultados['curl_version'] = $resultados['curl_extension'] ? curl_version()['version'] : null;

        // 2. Raw curl a Resend API
        if ($resultados['curl_extension']) {
            $ch = curl_init('https://api.resend.com/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $resp = curl_exec($ch);
            $resultados['curl_resend_http'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $resultados['curl_resend_error'] = curl_error($ch) ?: null;
            $resultados['curl_resend_body'] = mb_substr($resp, 0, 200);
            curl_close($ch);
        }

        // 3. fopen / stream socket (posible fallback de Guzzle)
        $ctx = stream_context_create(['http' => ['timeout' => 10]]);
        $fp = @fopen('https://api.resend.com/', 'r', false, $ctx);
        $resultados['stream_socket'] = $fp !== false;
        if ($fp) {
            fclose($fp);
        }

        // 4. DNS resolution
        $ip = @gethostbyname('api.resend.com');
        $resultados['dns_resolve'] = $ip !== 'api.resend.com' ? $ip : null;

        // 5. Config PHP relevante
        $resultados['allow_url_fopen'] = ini_get('allow_url_fopen');
        $resultados['open_basedir'] = ini_get('open_basedir') ?: 'sin restriccion';
        $resultados['disable_functions'] = ini_get('disable_functions') ?: 'ninguna';
        $resultados['php_version'] = PHP_VERSION;

        echo json_encode(['success' => true, 'data' => $resultados], JSON_PRETTY_PRINT);
    }
}
