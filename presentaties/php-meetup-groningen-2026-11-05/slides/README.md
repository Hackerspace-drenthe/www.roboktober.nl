# Slides — PHP Meetup Groningen, 5 november 2026

Versie 1 (prototype) van de slides bij [PLAN.md](../PLAN.md). reveal.js, statische HTML, geen build-stap.

## Openen

- Open `index.html` in de browser (werkt vanaf `file://`).
- Zonder `vendor/reveal.js` laadt reveal.js van de CDN (internet nodig). Voor offline gebruik eenmalig:

  ```bash
  ./scripts/vendor-reveal.sh
  ```

## Tijdens de talk

| Toets | Functie |
| --- | --- |
| `S` | Speaker view (notities + timer) |
| `F` | Volledig scherm |
| `Esc` / `O` | Overzicht |
| `B` | Scherm zwart |

## PDF-backup

Open `index.html?print-pdf` in Chrome → Afdrukken → Opslaan als PDF.

## Structuur

- `index.html` — alle slides; sprekersnotities in `<aside class="notes">` met sectie + timing uit het plan.
- `css/roboktober.css` — donker thema in Roboktober-huisstijl.
- `js/visuals.js` — terugkerend commando-schema, PWM- en H-brugdiagrammen.
- `img/` — beelden (kopieën uit `fotos/` en `roboktober-frontend/src/assets/`).
- Ontbrekende beelden/gegevens staan als oranje **TODO**-vlakken op de slides.
