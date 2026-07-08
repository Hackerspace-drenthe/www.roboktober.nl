<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>Bewerk je teamaanmelding</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.5; color: #111827;">
    <h1 style="margin-bottom: 12px;">Bewerk je teamaanmelding</h1>

    <p>Hoi {{ $team->contactpersoon }},</p>

    <p>
        Je hebt team <strong>{{ $team->naam }}</strong> aangemeld voor Roboktober.
        Via onderstaande link kun je je aanmelding aanpassen.
    </p>

    <p style="margin: 20px 0;">
        <a href="{{ $bewerkUrl }}" style="display: inline-block; background: #f97316; color: #ffffff; text-decoration: none; padding: 10px 14px; border-radius: 6px; font-weight: 700;">
            Aanmelding bewerken
        </a>
    </p>

    <p>
        Deze link is 30 dagen geldig.
        Bewaar deze e-mail zodat je later nog wijzigingen kunt doorgeven.
    </p>

    <p>Groet,<br>Roboktober organisatie</p>
</body>
</html>
