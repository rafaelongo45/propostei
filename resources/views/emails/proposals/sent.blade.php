<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Proposta comercial</title>
</head>
<body style="background: #f3f4f6; color: #111827; font-family: Arial, sans-serif; margin: 0; padding: 24px;">
    <div style="background: #ffffff; border: 1px solid #e5e7eb; margin: 0 auto; max-width: 600px; padding: 28px;">
        <p style="color: #6b7280; font-size: 13px; margin: 0 0 16px;">Proposta comercial</p>

        <h1 style="font-size: 22px; line-height: 1.3; margin: 0 0 16px;">
            {{ $proposal->title }}
        </h1>

        <p style="font-size: 15px; line-height: 1.6; margin: 0 0 14px;">
            Olá, {{ $proposal->client->name }}.
        </p>

        <p style="font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
            {{ $senderName ?: 'O responsável pela proposta' }} enviou uma proposta comercial para sua análise.
        </p>

        <p style="margin: 0 0 24px;">
            <a href="{{ $publicUrl }}" style="background: #111827; color: #ffffff; display: inline-block; font-size: 14px; font-weight: 700; padding: 12px 18px; text-decoration: none;">
                Ver proposta
            </a>
        </p>

        <p style="color: #4b5563; font-size: 13px; line-height: 1.6; margin: 0;">
            Se o botão não funcionar, copie e cole este link no navegador:<br>
            <a href="{{ $publicUrl }}" style="color: #1d4ed8;">{{ $publicUrl }}</a>
        </p>
    </div>
</body>
</html>
