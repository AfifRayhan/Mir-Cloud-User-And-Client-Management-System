<!DOCTYPE html>
<html>
<head>
    <title>{{ $subjectLine }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <p><strong>From:</strong> {{ $senderUser->name }} ({{ $senderUser->email }})</p>
        <hr>
        <div style="white-space: pre-wrap;">{{ $bodyContent }}</div>
        <br>
        <hr>
        <p style="font-size: 12px; color: #777;">This email was sent via Mir Cloud Internal Mail System.</p>
    </div>
</body>
</html>
