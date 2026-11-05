# Roboktober — van joystick tot wiel · reviewpakket

**Projectstatus — 5 november 2026:** Roboktober 2026 is afgerond, bevestigd door Rein. De website is operationeel en stabiel en elektronica v4 werkt en is akkoord. Dit pakket blijft beschikbaar voor presentatie en hergebruik; de review is voltooid. Praktische controles voor een volgende vertoning blijven nodig.

Talk voor de PHP Meetup Groningen, 5 november 2026 (±47 min + vragen).
Centrale vraag: wat gebeurt er als je op je telefoon de joystick naar voren duwt?

**Status — 4 november 2026:** review afgerond en akkoord; alle opmerkingen,
inclusief die van Wallie, zijn verwerkt. Rein bevestigt dat alle benodigde
gebruiksrechten, licentievoorwaarden en toestemmingen voor deze presentatie
zijn geregeld. Bronvermeldingen en licenties blijven behouden.

## Openen

1. Pak de ZIP volledig uit (niet openen vanuit de ZIP-preview).
2. Open `presentatie/index.html` in Chrome, Edge of Firefox.
3. Alles werkt offline; er is geen internet of webserver nodig.

Alle weergave-assets zijn lokaal meegeleverd: afbeeldingen en productfoto's,
video, reveal.js, code-highlighting, scripts en CSS. Lettertypen gebruiken de
lokale systeemfonts; er wordt geen webfont gedownload. Er is geen CDN-terugval.
AliExpress- en andere externe links zijn alleen aanklikbare bronvermeldingen,
geen afbeeldingsbronnen. Alleen het openen van externe websites en de pagina
achter de QR-code vereist internet. Pak het pakket altijd volledig uit,
inclusief `vendor/`, `js/` en `img/`.

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
- Dia 34 behandelt code in twee klikstappen met exacte voorbeeldresultaten. Dia 35 is daarna een aparte ontwerpgalerij van 9 klikken, zonder code. De modelnamen linken naar de
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
ongewijzigd lokaal opgenomen. Rein heeft de benodigde gebruiksrechten voor
deze presentatie bevestigd; dit verleent geen vrije licentie aan derden.

De productfoto's van de [N20-motor](https://ae-pic-a1.aliexpress-media.com/kf/He59f980f66ec4a4baa1d3b447724f1e0S.jpg)
en het [wiel](https://ae-pic-a1.aliexpress-media.com/kf/S4df94380f31f496cb5fecc3295f65284O.jpg)
zijn eveneens door Rein aangeleverd. Lokaal: `img/n20-motor-photo.jpg` (uitsnede
tot één complete motor) en `img/n20-wheel-photo.jpg` (ongewijzigd).
Rein heeft ook hiervoor de benodigde gebruiksrechten voor deze presentatie
bevestigd; dit verleent geen vrije licentie aan derden.

## Review afgerond

- Rode draad, niveau voor PHP-developers, taal, tempo en inhoud zijn gereviewd en akkoord.
- Wallies opmerkingen en alle overige reviewfeedback zijn verwerkt.
- De sprekersnotities (`S`) geven per dia de bedoeling en timing. Bij klikstappen zie je alleen de actuele spreektekst, ook bij terugklikken of rechtstreeks naar een stap springen. Dia's zonder klikstappen houden hun volledige notities.

Er staan geen reviewacties meer open.
