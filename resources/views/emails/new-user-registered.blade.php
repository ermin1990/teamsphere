<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nova registracija</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #16a34a; border-bottom: 2px solid #16a34a; padding-bottom: 10px;">
            👤 Nova registracija
        </h1>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Ime:</strong> {{ $registeredUser->name }}<br>
            <strong>Email:</strong> {{ $registeredUser->email }}<br>
            <strong>Datum:</strong> {{ $registeredUser->created_at->format('d.m.Y H:i') }}
        </div>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="color: #6b7280; font-size: 14px;">
            Ova poruka je automatski poslana kad se novi korisnik registruje na MojTurnir.
        </p>
    </div>
</body>
</html>
