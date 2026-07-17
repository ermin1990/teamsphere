<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $approved ? 'Prijava prihvaćena' : 'Prijava odbijena' }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        @if($approved)
            <h1 style="color: #16a34a; border-bottom: 2px solid #16a34a; padding-bottom: 10px;">
                ✅ Prijava prihvaćena
            </h1>

            <p>
                Zdravo {{ $joinRequest->user->name }},<br><br>
                Vaša prijava za takmičenje <strong>{{ $joinRequest->competition->name }}</strong>
                ({{ $joinRequest->competition->organization->name }}) je <strong>prihvaćena</strong>.
                Dodani ste na takmičenje i možete pratiti raspored i rezultate.
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $competitionUrl }}" style="background: #16a34a; color: #ffffff; padding: 12px 28px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                    Pogledaj takmičenje
                </a>
            </div>
        @else
            <h1 style="color: #dc2626; border-bottom: 2px solid #dc2626; padding-bottom: 10px;">
                ❌ Prijava odbijena
            </h1>

            <p>
                Zdravo {{ $joinRequest->user->name }},<br><br>
                Vaša prijava za takmičenje <strong>{{ $joinRequest->competition->name }}</strong>
                ({{ $joinRequest->competition->organization->name }}) je <strong>odbijena</strong> od strane organizatora.
            </p>
        @endif

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="color: #6b7280; font-size: 14px;">
            Ova poruka je automatski poslana kad organizator obradi vašu prijavu na MojTurnir.
        </p>
    </div>
</body>
</html>
