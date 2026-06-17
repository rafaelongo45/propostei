<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Contato pelo site</title>
</head>
<body style="background: #f3f4f6; color: #111827; font-family: Arial, sans-serif; margin: 0; padding: 24px;">
    <div style="background: #ffffff; border: 1px solid #e5e7eb; margin: 0 auto; max-width: 620px; padding: 28px;">
        <p style="color: #0f766e; font-size: 13px; font-weight: 700; margin: 0 0 16px;">Nova mensagem pelo site</p>

        <h1 style="font-size: 22px; line-height: 1.3; margin: 0 0 20px;">
            {{ $subject ?: 'Contato pelo propostei' }}
        </h1>

        <p style="font-size: 15px; line-height: 1.6; margin: 0 0 8px;">
            <strong>Nome:</strong> {{ $name }}
        </p>
        <p style="font-size: 15px; line-height: 1.6; margin: 0 0 20px;">
            <strong>E-mail:</strong> <a href="mailto:{{ $email }}" style="color: #0f766e;">{{ $email }}</a>
        </p>

        <div style="border-top: 1px solid #e5e7eb; padding-top: 20px;">
            <p style="font-size: 15px; line-height: 1.7; margin: 0; white-space: pre-line;">{{ $messageText }}</p>
        </div>
    </div>
</body>
</html>
