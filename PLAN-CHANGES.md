# Roboktober – Plan wijzigingen

> **PLAN.md is bevroren** (commit `140f157`). Dit bestand bevat alle wijzigingen ten opzichte van dat origineel.
> Voeg wijzigingen toe als genummerde secties met datum, reden en het gewijzigde onderdeel.

---

## Hoe dit bestand werken

| Kolom | Betekenis |
|---|---|
| **Sectie** | Verwijzing naar sectienummer in PLAN.md |
| **Type** | `ADD` / `CHANGE` / `REMOVE` / `DECISION` |
| **Reden** | Waarom de wijziging nodig was |
| **Inhoud** | Wat er veranderd is (of verwijzing naar sectie hieronder) |

---

## Wijzigingen

### WZ-001 · 2026-07-02 · DECISION · Sectie 5.2 — Laravel 13.x in plaats van 11.x

**Reden:** `composer create-project laravel/laravel` installeert automatisch de laatste stabiele versie. Laravel 13.18.0 is beschikbaar en backward-compatible met alle PLAN.md vereisten.

**Oorspronkelijk (PLAN.md):**
> Laravel 11.x: API-only backend

**Gewijzigd naar:** Laravel 13.18.0 (supersette van 11.x, zelfde API, extra features zoals `php artisan install:api`, native `routes/api.php` generatie).

---

### WZ-002 · 2026-07-02 · DECISION · Sectie 8.1 — Larastan tijdelijk uitgeschakeld (PHP 8.5 incompatibiliteit)

**Reden:** Larastan 3.x crasht op PHP 8.5.4 zonder output (silent exit code 1). De oorzaak is een onopgeloste incompatibiliteit tussen Larastan's intern gebruik van `phpstan/phpstan-src` en PHP 8.5. PHPStan zelf werkt correct op level 9 zonder de Larastan-extension.

**Oorspronkelijk (PLAN.md):**
> larastan/larastan voor Laravel-specifieke PHPStan-regels

**Gewijzigd naar:** PHPStan level 9 actief zonder Larastan-extension. De `phpstan.neon` bevat de extension als uitgecommentarieerde regel zodat re-activatie triviaal is. Zodra Larastan PHP 8.5 ondersteunt, wordt WZ-002 gesloten.

---

### WZ-003 · 2026-07-02 · DECISION · Sectie 5.3 — Vue 3 build output naar roboktober-api/public/app/

**Reden:** PLAN.md §5.3 spreekt van "deploys to Laravel public/ folder" zonder exact pad. Gekozen voor subdirectory `/app/` zodat Filament assets (`/css/filament/`, `/js/filament/`) niet worden overschreven bij `npm run build`.

**Oorspronkelijk (PLAN.md):**
> Vue 3 frontend deploys to Laravel public/ folder

**Gewijzigd naar:** Build output: `roboktober-api/public/app/`. Laravel serveert de SPA via een catch-all route (`routes/web.php`). Filament blijft bereikbaar via `/admin`.

---

### WZ-004 · 2026-07-02 · DECISION · Sectie 5.1 — Alleen antweight klasse voor editie 2026

**Reden:** Eerste editie, lage drempel. Antweights (≤ 150 gram) zijn goedkoper, veiliger en makkelijker te transporteren. Zwaardere klassen worden overwogen voor toekomstige edities.

**Oorspronkelijk (PLAN.md):**
> Type robot: combat robots (antweight/beetleweight gewichtsklassen)

**Gewijzigd naar:** Alleen antweight (≤ 150 gram) voor Roboktober 2026. Beetleweight en featherweight worden niet aangeboden. Database-enum en frontend bevatten nog alle klassen voor toekomstbestendigheid.

---

### WZ-005 · 2026-07-02 · ADD · Sectie 6.8 — Pagina "Uw Gastheer" (Walter / Wallieonline)

**Reden:** PLAN.md §6.8 beschrijft een "Credits en Wallie Touch" sectie. Dit is uitgewerkt als een volwaardige pagina op `/walter` met krediet aan Walter (alias Wallieonline), team captain van Team Upstart.

**Toegevoegd:**
- Route `/walter` → `WalterView.vue`
- Pagina bevat: biografie, citaat, robots (Cookiemonster, TLC, Kecil), rol bij Roboktober, externe links (wallieonline.nl, YouTube, Facebook, Dutch Robot Games-profiel)
- Navigatielink "Uw Gastheer" toegevoegd in desktop- én mobiel menu

---

### WZ-006 · 2026-07-02 · ADD · Seeders — Initiële content voor ontwikkeling

**Reden:** PLAN.md beschrijft een website die content toont. Voor ontwikkeling en demo zijn seeders aangemaakt.

**Toegevoegd:**
- `TeamSeeder`: Hackerspace Drenthe (status: approved) + HackerBot (antweight, in ontwikkeling)
- `LinkSeeder`: 18 links verdeeld over alle categorieën (wallie, community, competitie, tools, onderdelen, documentatie)
- `PostSeeder`: blog-artikel "Roboktober: combat robots komen naar Hackerspace Drenthe" — gepubliceerd, antweight-focus
- Admin-gebruiker: `admin@hackerspacedrenthe.nl` (wachtwoord beheerd buiten versiecontrole)

---

### WZ-007 · 2026-07-02 · ADD · Sectie 6.x — Pagina "Bouwen" met BOM + korte bouwgids

**Reden:** Deelnemers vroegen om een praktische instappagina met onderdelenlijst, prijsranges, besturingsopties en eerste bouwstappen.

**Oorspronkelijk (PLAN.md):**
> Buildinformatie en links zijn verspreid over meerdere pagina's

**Gewijzigd naar:** Nieuwe route `/bouwen` met:
- Bill of Materials (incl. prijsranges en leverancierslinks)
- Opties voor afstandsbediening (hardwarezender of Android/Web GUI)
- Korte stap-voor-stap bouwbeschrijving voor antweight
- Interne links vanaf navigatie en `/walter`

---

### WZ-008 · 2026-07-02 · CHANGE · Sectie 6.x — Unieke infographic hero-header per pagina

**Reden:** Betere visuele storytelling en directe context per pagina; elk scherm opent met een beeld dat de inhoud samenvat.

**Oorspronkelijk (PLAN.md):**
> Generieke hero-stijl zonder page-specifieke header illustratie

**Gewijzigd naar:** Alle primaire routes gebruiken een eigen headerafbeelding uit `roboktober-frontend/src/assets/headers/` met donkere overlay voor leesbaarheid. Dit geldt o.a. voor home, programma, teams, teamdetail, nieuws, nieuwsartikel, build hub, bouwen, aanmelden, walter, CMS-pagina en 404.

---

### WZ-009 · 2026-07-02 · ADD · Sectie 6.1 — Voorpagina-story met 3 infographics

**Reden:** De homepage moest het volledige verhaal direct duidelijk maken: samen bouwen (vrienden/familie), educatie (elektronica + software), en de robotwars arena-ervaring in Drenthe.

**Oorspronkelijk (PLAN.md):**
> Home bevat hero, CTA en algemene introductie

**Gewijzigd naar:** Nieuwe sectie op de homepagina met drie visuele infographic-kaarten en volledig Nederlandse copy:
- Samen bouwen in Hackerspace Drenthe (laagdrempelig/beginner-vriendelijk)
- Leren met elektronica en software (incl. Wallieonline-context)
- Van werkplaats naar antweight robotwars arena

Elke kaart bevat een concrete CTA naar relevante pagina's (`/aanmelden`, `/build-hub`, `/programma`).

---

### WZ-010 · 2026-07-02 · ADD · Sectie 6.x — Video-sectie op Bouwen-pagina

**Reden:** Vraag vanuit gebruikers om concrete antweight-actiebeelden en bouwvideo's op de site, met Wallieonline als primaire bron.

**Oorspronkelijk (PLAN.md):**
> Bouwen-pagina bevat BOM en tekstuele bouwstappen

**Gewijzigd naar:** Nieuwe sectie op `/bouwen` met ingesloten YouTube-video's:
- Bouwvideo's (WORC robot controller + ESP-NOW remote controller)
- Arena-actievideo's ter inspiratie

Toelichting toegevoegd dat sommige arena-video's zwaardere robotklassen tonen, maar dezelfde basisprincipes relevant zijn voor antweight (besturing, betrouwbaarheid, strategie en veiligheid).

---

<!-- TEMPLATE voor een nieuwe wijziging:

### WZ-001 · 2026-MM-DD · [Type] · Sectie X.X — Korte omschrijving

**Reden:**
...

**Oorspronkelijk (PLAN.md):**
> citeer de relevante tekst uit PLAN.md

**Gewijzigd naar:**
...

-->
