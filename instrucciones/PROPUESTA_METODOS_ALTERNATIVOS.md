# PROPUESTA: Métodos Alternativos de Envío de QR

## Opciones Disponibles

| Opción | Estado | Descripción |
|--------|--------|-------------|
| 📧 Correo electrónico | ✅ Implementado | PHPMailer + SMTP |
| 📱 Telegram Bot | ⏳ Pendiente | Bot de Telegram |
| 💬 WhatsApp | ⏳ Pendiente | API de terceros |
| 🔗 Link de descarga | ⏳ Pendiente | Enlace público |

---

## 1. Telegram Bot

### Ventajas
- Envío instantáneo
- Gratuito
- Sin costo por mensaje
- Confiable

### Desventajas
- Requiere crear bot
- Admin debe compartir QR manualmente si es para pruebas

### Configuración

```
1. Crear bot: @BotFather → /newbot → Nombre → Username
2. Obtener Token: Provided por BotFather
3. Obtener Chat ID: @userinfobot → Enviar mensaje → Copiar Chat ID
```

### Variables .env

```env
# ============================================
# TELEGRAM BOT (Pendiente)
# ============================================
TELEGRAM_BOT_TOKEN=1234567890:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_CHAT_ID=123456789
```

### Código Preparado

```php
<?php
class ServicioTelegram
{
    private string $token;
    private string $chatId;

    public function __construct()
    {
        $this->token = $_ENV['TELEGRAM_BOT_TOKEN'] ?? '';
        $this->chatId = $_ENV['TELEGRAM_CHAT_ID'] ?? '';
    }

    public function enviarDocumento(string $rutaImagen, string $caption = ''): array
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendPhoto";

        $postFields = [
            'chat_id' => $this->chatId,
            'photo' => new CURLFile($rutaImagen),
            'caption' => $caption,
            'parse_mode' => 'HTML'
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
```

---

## 2. WhatsApp

### Ventajas
- Amplio alcance (todos usan WhatsApp)
- Notificaciones instantáneas

### Desventajas
- API oficial no disponible
- Requiere servicios de terceros (Twilio, MessageBird, etc.)
- Costos por mensaje
- Número verificado requerido

### Opciones de API

| Servicio | Costo | Notas |
|----------|-------|-------|
| Twilio | Pago | $0.005-0.01 por mensaje |
| MessageBird | Pago | Similar a Twilio |
| Meta Business API | Gratuito* | Requiere aprobación |

### Variables .env (preparado)

```env
# ============================================
# WHATSAPP (Pendiente - Requiere API de terceros)
# ============================================
# WHATSAPP_API_URL=https://api.twilio.com/2010-04-01
# WHATSAPP_ACCOUNT_SID=
# WHATSAPP_AUTH_TOKEN=
# WHATSAPP_FROM_NUMBER=+
```

---

## 3. Link de Descarga

### Ventajas
- Sin configuración adicional
- Funciona con cualquier método de contacto
- El admin puede compartir por cualquier medio

### Desventajas
- Menos formal
- Requiere que el admin comparta manualmente

### Endpoint Preparado

```php
// GET /qr/descargar/{token}
public function descargarQr()
{
    $token = $parametros[0]; // token del QR
    $ruta = __DIR__ . '/../../qr_images/' . $token . '.png';
    
    if (!file_exists($ruta)) {
        http_response_code(404);
        echo json_encode(["error" => "QR no encontrado"]);
        return;
    }
    
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="codigo_qr.png"');
    readfile($ruta);
}
```

### Variables .env

```env
# ============================================
# LINK DE DESCARGA (Pendiente)
# ============================================
QR_PUBLIC_URL=http://localhost/SistemaGestorDeAsientos/API/qr_images/
```

---

## Preguntas

1. ¿Deseas implementar Telegram además del correo?
2. ¿Prefieres el link de descarga como alternativa?
3. ¿Necesitas WhatsApp (requiere costos adicionales)?

---

## Commit Sugerido

```
DOCS: Agrega propuestas de métodos alternativos de envío QR

Antecedentes:
El sistema ya soporta envío por correo. Se documentan opciones
alternativas para pruebas y diferentes escenarios.

Solución:
Se incluyen configuraciones y código preparado para:
- Telegram Bot (instantáneo, gratuito)
- WhatsApp (requiere API de terceros)
- Link de descarga (alternativa simple)
```
