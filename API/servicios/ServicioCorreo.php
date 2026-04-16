<?php

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ServicioCorreo
{
    private PHPMailer $mail;
    private string $smtpHost;
    private string $smtpUser;
    private string $smtpPass;
    private string $smtpPort;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com';
        $this->smtpUser = $_ENV['SMTP_USER'] ?? '';
        $this->smtpPass = $_ENV['SMTP_PASS'] ?? '';
        $this->smtpPort = $_ENV['SMTP_PORT'] ?? '587';
        $this->fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'no-reply@institucion.edu';
        $this->fromName = $_ENV['SMTP_FROM_NAME'] ?? 'Facultad de Informática';
    }

    public function enviarQrAlumno(array $alumno, string $rutaQr): bool
    {
        try {
            $this->configurarServidor();
            $this->configurarMensaje($alumno, $rutaQr);
            
            return $this->mail->send();
        } catch (Exception $e) {
            error_log("Error enviando correo a {$alumno['email']}: " . $this->mail->ErrorInfo);
            return false;
        } finally {
            $this->limpiar();
        }
    }

    private function configurarServidor(): void
    {
        $this->mail->SMTPDebug = SMTP::DEBUG_OFF;
        $this->mail->isSMTP();
        $this->mail->Host = $this->smtpHost;
        $this->mail->SMTPAuth = true;
        $this->mail->Username = $this->smtpUser;
        $this->mail->Password = $this->smtpPass;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port = (int) $this->smtpPort;
        $this->mail->CharSet = 'UTF-8';
    }

    private function configurarMensaje(array $alumno, string $rutaQr): void
    {
        $this->mail->setFrom($this->fromEmail, $this->fromName);
        $this->mail->addAddress($alumno['email'], $alumno['nombre'] . ' ' . $alumno['apellido']);
        $this->mail->addReplyTo($this->fromEmail, $this->fromName);
        
        if (file_exists($rutaQr)) {
            $this->mail->addAttachment($rutaQr, 'Codigo_QR_Graduacion.png');
        }
        
        $this->mail->isHTML(true);
        $this->mail->Subject = '🎓 Tu código QR para la Ceremonia de Graduación';
        $this->mail->Body = $this->generarPlantillaHtml($alumno);
        $this->mail->AltBody = $this->generarPlantillaTexto($alumno);
    }

    private function generarPlantillaHtml(array $alumno): string
    {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align: center; background: linear-gradient(135deg, #1a5276, #2980b9); color: white; padding: 30px 20px; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; text-align: center; }
                .content h2 { color: #2c3e50; margin-top: 0; }
                .qr-info { background: #eaf2f8; padding: 20px; border-radius: 10px; margin: 20px 0; }
                .qr-info p { margin: 10px 0; color: #34495e; }
                .qr-info strong { color: #1a5276; }
                .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 8px; margin: 20px 0; }
                .warning strong { color: #856404; }
                .footer { text-align: center; color: #7f8c8d; font-size: 12px; padding: 20px; background: #f8f9fa; }
                .btn { display: inline-block; background: #1a5276; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎓 Ceremonia de Graduación</h1>
                    <p>Facultad de Informática</p>
                </div>
                <div class="content">
                    <h2>¡Hola, ' . htmlspecialchars($alumno['nombre']) . '!</h2>
                    <p>Te enviamos tu código QR de acceso para la ceremonia de graduación.</p>
                    
                    <div class="qr-info">
                        <p><strong>📋 Número de cuenta:</strong> ' . htmlspecialchars($alumno['numCuenta']) . '</p>
                        <p><strong>👤 Nombre:</strong> ' . htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']) . '</p>
                        <p><strong>📅 Fecha:</strong> [FECHA DEL EVENTO]</p>
                        <p><strong>📍 Lugar:</strong> [LUGAR DEL EVENTO]</p>
                        <p><strong>👥 Invitados registrados:</strong> ' . (int)$alumno['cantInvitado'] . '</p>
                    </div>
                    
                    <div class="warning">
                        <strong>⚠️ Importante:</strong> Presenta este código QR el día del evento para registrar tu entrada. El código está adjunto en este correo.
                    </div>
                    
                    <p style="color: #7f8c8d; font-size: 14px;">
                        Si no puedes ver la imagen adjunta, muéstrala en tu teléfono el día del evento.
                    </p>
                </div>
                <div class="footer">
                    <p>Este es un correo automático. No respondas a este mensaje.</p>
                    <p>Facultad de Informática - [NOMBRE DE LA INSTITUCIÓN]</p>
                </div>
            </div>
        </body>
        </html>';
    }

    private function generarPlantillaTexto(array $alumno): string
    {
        return "
        Hola {$alumno['nombre']} {$alumno['apellido']},
        
        Te enviamos tu código QR de acceso para la ceremonia de graduación.
        
        Número de cuenta: {$alumno['numCuenta']}
        Fecha: [FECHA DEL EVENTO]
        Lugar: [LUGAR DEL EVENTO]
        Invitados registrados: {$alumno['cantInvitado']}
        
        IMPORTANTE: Presenta este código QR el día del evento para registrar tu entrada.
        El código QR está adjunto en este correo.
        
        Saludos,
        Facultad de Informática
        ";
    }

    private function limpiar(): void
    {
        $this->mail->clearAddresses();
        $this->mail->clearAttachments();
    }

    public function getConfig(): array
    {
        return [
            'host' => $this->smtpHost,
            'user' => $this->smtpUser,
            'port' => $this->smtpPort,
            'from_email' => $this->fromEmail,
            'from_name' => $this->fromName
        ];
    }

    public function probarConexion(): array
    {
        try {
            $this->configurarServidor();
            $this->mail->setFrom($this->fromEmail, 'Test');
            $this->mail->addAddress($this->smtpUser);
            $this->mail->isHTML(false);
            $this->mail->Subject = 'Test';
            $this->mail->Body = 'Prueba de conexión SMTP';
            $this->mail->send();
            
            $this->limpiar();
            return ['success' => true, 'message' => 'Conexión exitosa'];
        } catch (Exception $e) {
            $this->limpiar();
            return ['success' => false, 'message' => $this->mail->ErrorInfo];
        }
    }
}
