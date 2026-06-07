<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .container { max-width: 520px; margin: 0 auto; background: #fff; border-radius: 12px; padding: 40px; }
        .logo { font-size: 22px; font-weight: bold; color: #111827; margin-bottom: 24px; }
        h1 { font-size: 20px; color: #111827; margin: 0 0 8px; }
        p { color: #6b7280; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .btn { display: inline-block; background: #f59e0b; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 8px; font-weight: bold; font-size: 14px; }
        .note { font-size: 12px; color: #9ca3af; margin-top: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🔧 TallerFácil</div>
        <h1>Restablecer contraseña</h1>
        <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta. Hacé clic en el botón para continuar:</p>
        <a href="{{ $resetUrl }}" class="btn">Restablecer contraseña</a>
        <p class="note">
            Este link expira en 60 minutos. Si no solicitaste el restablecimiento, podés ignorar este email.
        </p>
    </div>
</body>
</html>
