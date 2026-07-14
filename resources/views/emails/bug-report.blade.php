<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $data['type'] === 'bug' ? 'Bug Report' : 'Feature Suggestion' }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 10px;">
            {{ $data['type'] === 'bug' ? '🐛 Bug Report' : '💡 Feature Suggestion' }}
        </h1>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Type:</strong> {{ ucfirst($data['type']) }}<br>
            <strong>Subject:</strong> {{ $data['subject'] }}<br>
            @if($data['name'])
                <strong>Name:</strong> {{ $data['name'] }}<br>
            @endif
            @if($data['email'])
                <strong>Email:</strong> {{ $data['email'] }}<br>
            @endif
            <strong>Date:</strong> {{ now()->format('Y-m-d H:i:s') }}
        </div>

        <h2 style="color: #374151;">Description:</h2>
        <div style="background: #ffffff; border: 1px solid #e5e7eb; padding: 15px; border-radius: 5px; white-space: pre-wrap;">
            {{ $data['description'] }}
        </div>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

        <p style="color: #6b7280; font-size: 14px;">
            This feedback was submitted through MojTurnir feedback system.
        </p>
    </div>
</body>
</html>