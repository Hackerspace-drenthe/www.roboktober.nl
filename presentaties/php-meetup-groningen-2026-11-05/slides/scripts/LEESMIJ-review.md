# Roboktober — van joystick tot wiel · reviewpakket

Talk voor de PHP Meetup Groningen, 5 november 2026 (±47 min + vragen).
Centrale vraag: wat gebeurt er als je op je telefoon de joystick naar voren duwt?

## Openen

1. Pak de ZIP volledig uit (niet openen vanuit de ZIP-preview).
2. Open `presentatie/index.html` in Chrome, Edge of Firefox.
3. Alles werkt offline; er is geen internet of webserver nodig.

## Bediening

| Toets | Actie |
|---|---|
| → / spatie | volgende stap (veel dia's bouwen in klikken op) |
| ← | vorige stap |
| `S` | sprekersnotities in een apart venster; wisselen automatisch per klik (sta de pop-up toe) |
| `Esc` / `O` | overzicht van alle dia's |
| `F` | volledig scherm |

- Dia 8 bevat een video van Wallie met geluid; klik op play.
- Dia 20 bevat de volledige openbare ESP-NOW-firmware: acht klikken lichten de codeblokken uit met telkens eigen uitleg ernaast (wat, waarom en effect op de robot); scroll in het codevenster voor alle 205 regels. Bronversie `fb3afa886117`, © 2026 wallieonline, GPL-3.0-or-later; de licentie is lokaal meegeleverd. Dit is niet de WebUI-broncode.
- Dia 34 behandelt code in twee klikstappen met exacte voorbeeldresultaten. Dia 35 is daarna een aparte ontwerpgalerij van 10 klikken, zonder code. De modelnamen linken naar de
  bronbestanden in de map `3dprinter/` van dit pakket.

## Inhoud

- `presentatie/` — de dia's (reveal.js, lokaal meegeleverd)
- `3dprinter/` — OpenSCAD- en STL-bronnen van de ontwerpgalerij
- `PLAN.md` — opbouw, tijdsindeling en uitgangspunten van de talk

## Waar ik feedback op zoek

- Is de rode draad (één joystickcommando van telefoon tot wiel) te volgen?
- Klopt het niveau voor PHP-developers zonder hardware-ervaring?
- Taal, tempo, te volle dia's, onduidelijke of onjuiste claims.
- De sprekersnotities (`S`) geven per dia de bedoeling en timing. Bij klikstappen zie je alleen de actuele spreektekst, ook bij terugklikken of rechtstreeks naar een stap springen. Dia's zonder klikstappen houden hun volledige notities.

Reacties graag per dianummer (rechtsonder in beeld).
