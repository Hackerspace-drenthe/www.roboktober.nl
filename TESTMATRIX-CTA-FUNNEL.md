# Testmatrix CTA-funnel (anoniem vs ingelogd)

Datum: 2026-07-15
Versie live: 7d85b80
Doel: valideren dat publieke bezoekers naar registreren gaan en ingelogde bezoekers naar aanmelden.

## Verwachte funnel

- Anoniem: CTA-link naar /app/registreren
- Ingelogd: CTA-link naar /app/aanmelden
- Sitemap: wel /registreren, niet /aanmelden

## Resultaten productie

| Scenario | Route | Controlepunt | Verwacht | Gezien | Status |
|---|---|---|---|---|---|
| Anoniem | /app/ | Navigatie Do(e) mee | /app/registreren | /app/registreren | PASS |
| Anoniem | /app/ | Hero CTA | /app/registreren | /app/registreren | PASS |
| Anoniem | /app/programma | Sectie Doe mee als team | /app/registreren | /app/registreren | PASS |
| Anoniem | /app/bouwen | CTA Klaar om mee te doen | /app/registreren | /app/registreren | PASS |
| Anoniem | /app/antweight | CTA Begin met je eigen build | /app/registreren | /app/registreren | PASS |
| Anoniem | /app/teams | Team CTA | /app/registreren | /app/registreren | PASS |
| Anoniem | /app/teams/competitie | Navigatie Do(e) mee | /app/registreren | /app/registreren | PASS |
| Ingelogd | /app/ | Navigatie | /app/aanmelden | /app/aanmelden | PASS |
| Ingelogd | /app/ | Hero CTA | /app/aanmelden | /app/aanmelden | PASS |
| Ingelogd | /app/programma | Sectie Doe mee als team | /app/aanmelden | /app/aanmelden | PASS |
| Ingelogd | /app/bouwen | CTA Klaar om mee te doen | /app/aanmelden | /app/aanmelden | PASS |
| Ingelogd | /app/antweight | CTA Begin met je eigen build | /app/aanmelden | /app/aanmelden | PASS |
| Ingelogd | /app/teams | Team CTA | /app/aanmelden | /app/aanmelden | PASS |
| Ingelogd | /app/teams/competitie | Navigatie | /app/aanmelden | /app/aanmelden | PASS |
| SEO | /sitemap.xml | URL /aanmelden aanwezig | 0 | 0 | PASS |
| SEO | /sitemap.xml | URL /registreren aanwezig | 1 | 1 | PASS |

## Snelle regressietest (handmatig)

1. Open /app/ in incognito en controleer dat primaire CTA naar /app/registreren gaat.
2. Log in met 2FA en controleer dat dezelfde CTA naar /app/aanmelden gaat.
3. Controleer op /app/programma, /app/bouwen, /app/antweight en /app/teams dezelfde omschakeling.
4. Controleer /sitemap.xml op aanwezigheid van /registreren en afwezigheid van /aanmelden.
