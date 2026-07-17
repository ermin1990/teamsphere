<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nova prijava za takmičenje</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 10px;">
            🙋 Nova prijava za takmičenje
        </h1>

        <p>
            <strong>{{ $joinRequest->user->name }}</strong> ({{ $joinRequest->user->email }}) se prijavio/la
            za takmičenje <strong>{{ $joinRequest->competition->name }}</strong>
            ({{ $joinRequest->competition->organization->name }}) na MojTurnir platformi.
        </p>

        @if($joinRequest->message)
            <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <strong>Poruka igrača:</strong><br>
                {{ $joinRequest->message }}
            </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $manageUrl }}" style="background: #4f46e5; color: #ffffff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                Pregledaj prijavu
            </a>
        </div>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="color: #6b7280; font-size: 14px;">
            Ova poruka je automatski poslana kad se igrač prijavi za jedno od vaših takmičenja na MojTurnir.
        </p>
    </div>
</body>
</html>
