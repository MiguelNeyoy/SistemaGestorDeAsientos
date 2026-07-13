<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bienvenida.css?v=<?= filemtime(__DIR__ . '/css/bienvenida.css') ?>">
    <title>Registro cerrado — Sistema gestor de asistencia</title>
</head>

<body>

    <header class="pantalla-azul">

        <img src="img/logouas.png" alt="UAS" class="icon">

        <div class="titulo-header">

            <h1>
                Ceremonia de Graduación 2026
            </h1>

        </div>

        <img src="img/convision.png" alt="Vision" class="icon">

    </header>

    <div class="formulario">

        <div class="form-box" style="text-align: center;">

            <img src="img/logofimaz.png" alt="FiMAZ" class="icon-pequeño">

            <h2 style="color: #c00000; font-size: 26px; margin-bottom: 20px;">
                Período de confirmación cerrado
            </h2>

            <p style="font-size: 16px; line-height: 1.7; color: #333; margin-bottom: 14px;">
                El período para confirmar tu asistencia a la
                <strong>Ceremonia de Clausura de Graduación 2026</strong>
                ha finalizado.
            </p>

            <p style="font-size: 16px; line-height: 1.7; color: #333; margin-bottom: 14px;">
                Si tienes alguna duda, acude al departamento de
                Control Escolar de tu facultad.
            </p>

            <a href="index"
               style="display: inline-block; margin-top: 20px; color: #1f4f8f; font-weight: bold; text-decoration: none;">
                &larr; Volver al inicio
            </a>

        </div>

    </div>

</body>
</html>
