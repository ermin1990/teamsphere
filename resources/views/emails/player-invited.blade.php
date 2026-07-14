<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pozivnica za ligu</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 10px;">
            🏆 Pozvani ste da igrate!
        </h1>

        <p>
            <strong>{{ $invitation->inviter->name }}</strong> vas je pozvao/la da se pridružite takmičenju
            <strong>{{ $invitation->competition->name }}</strong>
            ({{ $invitation->organization->name }}) na MojTurnir platformi.
        </p>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $acceptUrl }}" style="background: #4f46e5; color: #ffffff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                Prihvati pozivnicu
            </a>
        </div>

        <p style="color: #6b7280; font-size: 14px;">
            Ako još nemate nalog, link će vas provesti kroz registraciju. Pozivnica ističe {{ $invitation->expires_at->format('d.m.Y H:i') }}.
        </p>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="color: #6b7280; font-size: 12px;">
            Ako ne prepoznajete ovaj poziv, slobodno ignorišite ovaj email.
        </p>
    </div>
</body>
</html>
