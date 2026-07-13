#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

foreach ($_ENV as $key => $value) {
    $_SERVER[$key] = $value;
}

require_once __DIR__ . '/../configuracion/ConexionDB.php';
require_once __DIR__ . '/../modelos/AlumnoModelo.php';
require_once __DIR__ . '/../modelos/QrModelo.php';
require_once __DIR__ . '/../servicios/ServicioCorreo.php';

// --- Parse args ---
$numCuenta = null;
$emailForzado = null;

for ($i = 1; $i < $argc; $i++) {
    if (str_starts_with($argv[$i], '--email=')) {
        $emailForzado = substr($argv[$i], 8);
    } elseif (!str_starts_with($argv[$i], '--')) {
        $numCuenta = $argv[$i];
    }
}

if (!$numCuenta) {
    echo "Uso: php bin/probar-correo.php <num_cuenta> [--email=...]\n";
    exit(1);
}

// --- Helpers ---
function pregunta($mensaje, $default = 's')
{
    $opciones = $default === 's' ? '[S/n]' : '[s/N]';
    echo "  >> {$mensaje}? {$opciones}: ";
    $input = trim(fgets(STDIN));
    if ($input === '') {
        return $default === 's';
    }
    return strtolower($input) === 's';
}

function paso($label, $status, $detalle = '')
{
    $icono = $status ? 'OK' : 'NO';
    echo "     [{$icono}] {$label}{$detalle}\n";
}

echo "\n";
echo "  =====================================================\n";
echo "     Probar envio de correo QR\n";
echo "  =====================================================\n";
echo "\n";

$alumnoModelo = new AlumnoModel();
$qrModelo = new QrModelo();

// [1] Buscar alumno
echo " [1/5] Buscando alumno {$numCuenta}...\n";
$alumno = $alumnoModelo->buscarPorNumeroCuenta($numCuenta);
if (!$alumno) {
    paso("Alumno no encontrado", false);
    exit(1);
}
$grupo = strtoupper($alumno['carrera']) . '-' . strtoupper($alumno['turno']);
paso("{$alumno['nombre']} {$alumno['apellido']} ({$grupo})", true);
echo "\n";

// [2] Email
echo " [2/5] Verificando email...\n";
$email = $alumno['email'] ?? '';
if (!empty($emailForzado) && $emailForzado !== $email) {
    $email = $emailForzado;
    $alumnoModelo->actualizarCorreo($numCuenta, $email);
    paso("Email actualizado: {$email}", true);
} elseif (!empty($email)) {
    paso("Email: {$email}", true);
} else {
    paso("No tiene email registrado", false);
    $sugerido = $emailForzado ?: "{$numCuenta}@test.com";
    echo "     >> Se suguiere: {$sugerido}\n";
    if (pregunta("Asignar este email")) {
        $email = $sugerido;
        $alumnoModelo->actualizarCorreo($numCuenta, $email);
        paso("Email asignado: {$email}", true);
    } else {
        echo "     >> Escriba el email manualmente: ";
        $manual = trim(fgets(STDIN));
        if (!empty($manual)) {
            $email = $manual;
            $alumnoModelo->actualizarCorreo($numCuenta, $email);
            paso("Email asignado: {$email}", true);
        } else {
            paso("No se asigno email. Abortando.", false);
            exit(1);
        }
    }
}
echo "\n";

// [3] QR token
echo " [3/5] Verificando QR token...\n";
$qr = $qrModelo->obtenerPorNumCuenta($numCuenta);
$qrToken = null;
if (!$qr) {
    paso("No tiene QR", false);
    if (pregunta("Crear QR token")) {
        $qrToken = bin2hex(random_bytes(16));
        $qrModelo->crear($numCuenta, $qrToken);
        paso("QR token creado: {$qrToken}", true);
    } else {
        paso("No se creo QR. Abortando.", false);
        exit(1);
    }
} else {
    $qrToken = $qr['token'];
    paso("QR token: {$qrToken}", true);
}
echo "\n";

// [4] Confirmacion
echo " [4/5] Verificando confirmacion de asistencia...\n";
$confirmacion = $alumnoModelo->verificarConfirmacion($numCuenta);
$confirmado = null;
if ($confirmacion === false) {
    paso("No ha confirmado asistencia", false);
    if (pregunta("Confirmar asistencia ahora")) {
        $resultado = $alumnoModelo->actualizarConfirmacion($numCuenta, true, $alumno['cantInvitado'] ?? 0);
        if ($resultado === true) {
            $confirmado = true;
            paso("Asistencia confirmada", true);
        } else {
            paso("Error: " . ($resultado['error'] ?? 'desconocido'), false);
            exit(1);
        }
    } else {
        paso("No se confirmo. Abortando.", false);
        exit(1);
    }
} elseif ($confirmacion == 1) {
    $confirmado = true;
    paso("Asistencia: Confirmado", true);
} else {
    paso("Asistencia: No asistira", false);
    if (pregunta("Cambiar a confirmado")) {
        $resultado = $alumnoModelo->actualizarConfirmacion($numCuenta, true, $alumno['cantInvitado'] ?? 0);
        if ($resultado === true) {
            $confirmado = true;
            paso("Asistencia cambiada a confirmado", true);
        } else {
            paso("Error: " . ($resultado['error'] ?? 'desconocido'), false);
            exit(1);
        }
    } else {
        paso("No se modifico. Abortando.", false);
        exit(1);
    }
}
echo "\n";

// [5] Resumen y enviar
echo " [5/5] Resumen:\n";
echo "        Alumno:      {$numCuenta} - {$alumno['nombre']} {$alumno['apellido']}\n";
echo "        Email:       {$email}\n";
echo "        QR:          {$qrToken}\n";
echo "        Asistencia:  " . ($confirmado ? 'Confirmado' : 'Pendiente') . "\n";
echo "\n";

if (pregunta("Enviar correo ahora")) {
    echo "     Enviando...\n";
    $servicioCorreo = new ServicioCorreo();
    $resultado = $servicioCorreo->enviarQRIndividual($numCuenta);

    if ($resultado['success']) {
        echo "     [OK] Correo enviado a {$email}\n";
    } else {
        echo "     [NO] Error: {$resultado['message']}\n";
        exit(1);
    }
} else {
    echo "     Envio cancelado.\n";
}

echo "\n";
