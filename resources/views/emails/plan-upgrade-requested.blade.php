<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Zahtjev za veći plan</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #d97706; border-bottom: 2px solid #d97706; padding-bottom: 10px;">
            📈 Zahtjev za veći plan
        </h1>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Korisnik:</strong> {{ $requestingUser->name }}<br>
            <strong>Email:</strong> {{ $requestingUser->email }}<br>
            <strong>Trenutni plan:</strong> {{ $requestingUser->currentPlan()?->name ?? 'Free' }}<br>
            <strong>Organizacija:</strong> {{ $requestingUser->organizations->count() }}<br>
            <strong>Datum:</strong> {{ now()->format('d.m.Y H:i') }}
        </div>

        @if($requestNote)
            <h2 style="color: #374151;">Poruka korisnika:</h2>
            <div style="background: #ffffff; border: 1px solid #e5e7eb; padding: 15px; border-radius: 5px; white-space: pre-wrap;">
                {{ $requestNote }}
            </div>
        @endif

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="color: #6b7280; font-size: 14px;">
            Ova poruka je automatski poslana kad korisnik zatraži veći plan na MojTurnir.
        </p>
    </div>
</body>
</html>
