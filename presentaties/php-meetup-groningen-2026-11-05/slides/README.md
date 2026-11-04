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
- Dia 33 is een galerij van 10 klikken. De modelnamen linken naar de
  bronbestanden in de map `3dprinter/` van dit pakket.

## Inhoud

- `presentatie/` — de dia's (reveal.js, lokaal meegeleverd)
- `3dprinter/` — OpenSCAD- en STL-bronnen van de ontwerpgalerij
- `PLAN.md` — opbouw, tijdsindeling en uitgangspunten van de talk

## Fotoverantwoording

De foto `img/esp32-c3-supermini-photo.png` op dia 13 is van Dr. Tobias Weltner,
[done.land](https://done.land/components/microcontroller/families/esp/esp32/developmentboards/esp32-c3/c3supermini/).
De [oorspronkelijke foto](https://done.land/assets/img/c3_anglefront_overview_t.png)
is ongewijzigd lokaal opgenomen onder
[CC BY-ND 4.0](https://creativecommons.org/licenses/by-nd/4.0/).
Bronvermelding en licentie staan ook direct onder de foto.

De foto `img/drv8833-module-photo.jpg` is de door Rein aangeleverde
[AliExpress-productfoto](https://ae-pic-a1.aliexpress-media.com/kf/S74f2a70c6db24777afee9bef22fa2001N.jpg?width=1000&height=1000&hash=2000),
ongewijzigd lokaal opgenomen. Hiervoor is geen vrije licentie vastgesteld;
bronvermelding is geen hergebruikstoestemming. Controleer de gebruiksrechten
vóór verdere openbare verspreiding.

De productfoto's van de [N20-motor](https://ae-pic-a1.aliexpress-media.com/kf/He59f980f66ec4a4baa1d3b447724f1e0S.jpg)
en het [wiel](https://ae-pic-a1.aliexpress-media.com/kf/S4df94380f31f496cb5fecc3295f65284O.jpg)
zijn eveneens door Rein aangeleverd. Lokaal: `img/n20-motor-photo.jpg` (uitsnede
tot één complete motor) en `img/n20-wheel-photo.jpg` (ongewijzigd).
Ook hiervoor is geen vrije licentie vastgesteld; controleer de gebruiksrechten
vóór verdere openbare verspreiding.

## Waar ik feedback op zoek

- Is de rode draad (één joystickcommando van telefoon tot wiel) te volgen?
- Klopt het niveau voor PHP-developers zonder hardware-ervaring?
- Taal, tempo, te volle dia's, onduidelijke of onjuiste claims.
- De sprekersnotities (`S`) geven per dia de bedoeling en timing. Bij klikstappen zie je alleen de actuele spreektekst, ook bij terugklikken of rechtstreeks naar een stap springen. Dia's zonder klikstappen houden hun volledige notities.

Reacties graag per dianummer (rechtsonder in beeld).
