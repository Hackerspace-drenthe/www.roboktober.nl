<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Nieuwe teamaanmelding</title>
</head>
<body style="font-family: sans-serif; color: #1e293b; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h1 style="color: #f97316; margin-bottom: 4px;">Nieuwe teamaanmelding</h1>
    <p style="color: #64748b; margin-top: 0;">Roboktober 2026</p>
    <hr style="border: 1px solid #e2e8f0; margin: 16px 0;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="padding: 8px 0; font-weight: bold; width: 160px;">Teamnaam</td>
            <td style="padding: 8px 0;">{{ $team->naam }}</td>
        </tr>
        <tr style="background: #f8fafc;">
            <td style="padding: 8px 0; font-weight: bold;">Contactpersoon</td>
            <td style="padding: 8px 0;">{{ $team->contactpersoon }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">E-mail</td>
            <td style="padding: 8px 0;"><a href="mailto:{{ $team->email }}">{{ $team->email }}</a></td>
        </tr>
        <tr style="background: #f8fafc;">
            <td style="padding: 8px 0; font-weight: bold;">Volwassenen</td>
            <td style="padding: 8px 0;">{{ $team->volwassenen }}</td>
        </tr>
        @if($team->kinderen)
        <tr>
            <td style="padding: 8px 0; font-weight: bold;">Kinderen</td>
            <td style="padding: 8px 0;">{{ $team->kinderen }}</td>
        </tr>
        @endif
        @if($team->opmerkingen)
        <tr style="background: #f8fafc;">
            <td style="padding: 8px 0; font-weight: bold; vertical-align: top;">Opmerkingen</td>
            <td style="padding: 8px 0;">{{ $team->opmerkingen }}</td>
        </tr>
        @endif
    </table>
    <hr style="border: 1px solid #e2e8f0; margin: 24px 0;">
    <p style="font-size: 14px; color: #64748b;">
        Beoordeel de aanmelding via het
        <a href="{{ config('app.url') }}/admin/teams" style="color: #f97316;">Filament admin-paneel</a>.
    </p>
</body>
</html>
