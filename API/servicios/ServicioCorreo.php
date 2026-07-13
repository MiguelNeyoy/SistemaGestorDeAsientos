<?php

require_once __DIR__ . '/../modelos/AlumnoModelo.php';
require_once __DIR__ . '/../modelos/QrModelo.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;

class ServicioCorreo
{
    private $alumnoModelo;
    private $qrModelo;

    public function __construct()
    {
        $this->alumnoModelo = new AlumnoModel();
        $this->qrModelo = new QrModelo();
    }

    public function enviarQRIndividual($numCuenta)
    {
        $apiKey = $_SERVER['RESEND_API_KEY'] ?? '';
        $fromName = $_SERVER['MAIL_FROM_NAME'] ?? 'Graduacion';
        $fromEmail = $_SERVER['MAIL_FROM'] ?? '';

        if (!$apiKey || !$fromEmail) {
            return $this->respuesta(false, "Configuración de correo incompleta (RESEND_API_KEY o MAIL_FROM)", 500);
        }

        $alumno = $this->qrModelo->obtenerPorNumCuentaConGrupo($numCuenta);

        if (!$alumno) {
            return $this->respuesta(false, "Alumno no encontrado o sin QR asignado", 404);
        }

        if (empty($alumno['email'])) {
            return $this->respuesta(false, "El alumno no tiene correo registrado", 400);
        }

        $alumno = $this->limpiarUtf8($alumno);

        $options = new QROptions;
        $options->outputInterface = QRGdImagePNG::class;
        $options->outputBase64 = false;
        $options->scale = 8;
        $options->eccLevel = EccLevel::M;
        $qrCode = new QRCode($options);

        $resend = \Resend::client($apiKey);
        $from = "{$fromName} <{$fromEmail}>";

        try {
            $pngData = $qrCode->render($alumno['token']);

            $resend->emails->send([
                'from' => $from,
                'to' => [$alumno['email']],
                'subject' => 'Tu Código QR de Acceso - Graduación',
                'html' => $this->plantillaCorreo($alumno),
                'attachments' => [
                    [
                        'filename' => "QR_{$alumno['numCuenta']}.png",
                        'content' => base64_encode($pngData),
                    ],
                ],
            ]);

            $this->qrModelo->marcarEnviado($alumno['numCuenta']);

            return $this->respuesta(true, "Correo enviado correctamente a {$alumno['email']}", 200);
        } catch (\Exception $e) {
            return $this->respuesta(false, "Error al enviar correo: " . $e->getMessage(), 500);
        }
    }

    public function enviarQRsPorGrupo($carrera, $turno)
    {
        $alumnos = $this->alumnoModelo->obtenerConfirmadosConQRPorGrupo($carrera, $turno);

        if (empty($alumnos)) {
            return $this->respuesta(false, "No hay alumnos confirmados con correo en ese grupo", 404);
        }

        $apiKey = $_SERVER['RESEND_API_KEY'] ?? '';
        $fromName = $_SERVER['MAIL_FROM_NAME'] ?? 'Graduacion';
        $fromEmail = $_SERVER['MAIL_FROM'] ?? '';

        if (!$apiKey || !$fromEmail) {
            return $this->respuesta(false, "Configuración de correo incompleta (RESEND_API_KEY o MAIL_FROM)", 500);
        }

        $options = new QROptions;
        $options->outputInterface = QRGdImagePNG::class;
        $options->outputBase64 = false;
        $options->scale = 8;
        $options->eccLevel = EccLevel::M;
        $qrCode = new QRCode($options);

        $resend = \Resend::client($apiKey);
        $from = "{$fromName} <{$fromEmail}>";

        $enviados = 0;
        $errores = [];

        foreach ($alumnos as $alumno) {
            $alumno = $this->limpiarUtf8($alumno);
            try {
                if (empty($alumno['email'])) {
                    $errores[] = $alumno['numCuenta'];
                    continue;
                }

                $pngData = $qrCode->render($alumno['token']);

                $resend->emails->send([
                    'from' => $from,
                    'to' => [$alumno['email']],
                    'subject' => 'Tu Código QR de Acceso - Graduación',
                    'html' => $this->plantillaCorreo($alumno),
                    'attachments' => [
                        [
                            'filename' => "QR_{$alumno['numCuenta']}.png",
                            'content' => base64_encode($pngData),
                        ],
                    ],
                ]);

                $this->qrModelo->marcarEnviado($alumno['numCuenta']);
                $enviados++;
            } catch (\Exception $e) {
                $errores[] = $alumno['numCuenta'];
            }
        }

        if ($enviados > 0) {
            $msg = "Correos enviados: {$enviados}";
            if (count($errores) > 0) {
                $msg .= " (" . count($errores) . " fallaron)";
            }
            return $this->respuesta(true, $msg, 200, [
                'enviados' => $enviados,
                'errores' => $errores,
            ]);
        }

        return $this->respuesta(false, "No se pudo enviar ningún correo", 500, [
            'enviados' => 0,
            'errores' => $errores,
        ]);
    }

    private function limpiarUtf8($data)
    {
        if (is_string($data)) {
            return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->limpiarUtf8($value);
            }
        }
        return $data;
    }

    private function plantillaCorreo($alumno)
    {
        $nombre = htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']);
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"></head>
        <body style="font-family: Arial, sans-serif; color: #333; padding: 20px;">
            <div style="max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                <div style="background: #28a745; color: white; padding: 20px; text-align: center;">
                    <h1 style="margin: 0;">¡Graduación!</h1>
                </div>
                <div style="padding: 20px;">
                    <h2>Hola, {$nombre}</h2>
                    <p>Tu código QR de acceso para la ceremonia de graduación está adjunto a este correo.</p>
                    <p><strong>Instrucciones:</strong></p>
                    <ul>
                        <li>Guarda este código QR, será necesario para ingresar al evento.</li>
                        <li>No compartas tu código QR con otras personas.</li>
                        <li>Preséntalo en la entrada el día de la ceremonia para ser escaneado.</li>
                    </ul>
                    <p>¡Felicidades y nos vemos en la graduación!</p>
                </div>
                <div style="background: #f8f9fa; padding: 10px 20px; text-align: center; font-size: 12px; color: #666;">
                    Este es un mensaje automático, por favor no responder.
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function respuesta($success, $message, $code, $data = null)
    {
        if (PHP_SAPI !== 'cli') {
            http_response_code($code);
        }
        return array_filter([
            "success" => $success,
            "message" => $message,
            "data" => $data,
        ], function ($v) { return $v !== null; });
    }
}
