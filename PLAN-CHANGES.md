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
- Admin-gebruiker: `admin@hackerspace-drenthe.nl` (wachtwoord beheerd buiten versiecontrole)

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

### WZ-011 · 2026-07-02 · CHANGE · Sectie 8 / 8.1 — Security baseline concreet geïmplementeerd op publieke registratie-API

**Reden:** PLAN.md eist rate limiting, OWASP-maatregelen en security headers. De publieke endpoint `POST /api/v1/registratie` had nog geen expliciete throttling-policy en geen centrale API security headers.

**Oorspronkelijk (PLAN.md):**
> API rate limiting voor publieke endpoints.
> Security: CSP headers, input validation via Form Requests, OWASP Top 10 compliance.

**Gewijzigd naar:**
- Custom rate limiter `registratie` toegevoegd:
	- 5 requests/minuut per IP
	- 20 requests/uur per e-mailadres
- `POST /api/v1/registratie` gebruikt nu `throttle:registratie`.
- Nieuwe middleware `ApiSecurityHeaders` voegt standaard API-headers toe:
	- `X-Content-Type-Options: nosniff`
	- `X-Frame-Options: DENY`
	- `Referrer-Policy: no-referrer`
	- `Permissions-Policy` (restrictief)
	- `Content-Security-Policy` (restrictief)
- Feature tests uitgebreid met checks op:
	- 429-rate-limit gedrag
	- aanwezigheid van security headers op API-responses.

---

### WZ-012 · 2026-07-02 · ADD · Sectie 8.1 — Projectdocumentatie geactualiseerd (README's + root-overzicht)

**Reden:** De bestaande README-bestanden waren nog framework-templates en weerspiegelden niet de actuele architectuur, setup en securitystatus van Roboktober.

**Oorspronkelijk (PLAN.md):**
> README.md per module/package

**Gewijzigd naar:**
- `roboktober-api/README.md` herschreven met:
	- actuele projectstatus
	- installatiehandleiding (PHP/Composer/DB/npm)
	- endpoint-overzicht
	- security baseline en auditstatus
	- test/lint commands
- `roboktober-frontend/README.md` herschreven met:
	- actuele frontendstatus
	- lokale setup
	- backend-koppeling via Vite proxy (`/api` → `http://localhost:8000`)
	- build/deploy flow naar `roboktober-api/public/app`
- Nieuwe root `README.md` toegevoegd als monorepo-overzicht met projectdoelen uit PLAN.md en verwijzing naar backend/frontend handleidingen.

---

### WZ-013 · 2026-07-04 · CHANGE · Sectie 5.1 / 5.2 — Teamaanmelding nu gekoppeld aan een editie

**Reden:** Er is behoefte aan meerdere Roboktober-edities over meerdere jaren. Registraties moeten daarom expliciet aan een editie hangen in plaats van impliciet aan "de huidige editie".

**Oorspronkelijk (PLAN.md):**
> Teamregistratie is altijd open, zonder expliciete editie-selectie in de aanmeldflow.

**Gewijzigd naar:**
- Nieuwe entiteit `Edition` toegevoegd in backend.
- `teams` tabel uitgebreid met `edition_id` (foreign key).
- Registratie-endpoint (`POST /api/v1/registratie`) vereist nu `edition_id`.
- Alleen edities met `is_done = false` zijn geldig voor nieuwe aanmeldingen.
- Nieuw publiek endpoint: `GET /api/v1/edities` voor open edities.
- Frontend aanmeldformulier toont editie-keuze en blokkeert submit als er geen open editie beschikbaar is.

---

### WZ-014 · 2026-07-04 · ADD · Sectie 7 / 5.2 — Editiebeheer in admin (incl. done-status)

**Reden:** Organisatie wil zelf edities kunnen beheren: toevoegen, aanpassen en afsluiten zodra een editie is afgerond.

**Oorspronkelijk (PLAN.md):**
> CMS/admin voor content, teams en robots; editiebeheer nog niet expliciet uitgewerkt.

**Gewijzigd naar:**
- Nieuwe Filament resource `Edities` toegevoegd met beheer van:
	- naam editie
	- omschrijving
	- locatie
	- startdatum/tijd
	- einddatum/tijd
	- afbeelding
	- `is_done` toggle (afgesloten)
- Teambeheer in Filament toont nu ook bij welke editie een team hoort.
- Seeders uitgebreid met standaard open editie (`Roboktober 2026`) voor lokale ontwikkeling.

### WZ-015 · 2026-07-06 · CHANGE · Sectie 8 / Uploads — Teamfoto uploadlimiet verhoogd naar 50 MB

**Reden:** Praktische use-case: teamfoto's en mobiele beelden zijn regelmatig groter dan 5 MB. De oude limiet veroorzaakte onnodige 422/413 fouten voor geldige gebruikersuploads.

**Oorspronkelijk (PLAN.md):**
> Uploadlimieten niet expliciet op 50 MB vastgelegd.

**Gewijzigd naar:**
- Backend validatie verhoogd naar `max:51200` (50 MB) voor teamfoto uploads bij registratie én bewerken.
- Runtime uploadlimieten verhoogd:
	- `roboktober-api/public/.user.ini`: `upload_max_filesize=50M`, `post_max_size=52M`
	- root `package.json` dev API command: `-d upload_max_filesize=50M -d post_max_size=52M`
- Frontend client-side guard verhoogd naar 50 MB, inclusief foutmeldingen in zowel `/aanmelden` als `/aanmelding/bewerken/:token`.

---

### WZ-016 · 2026-07-06 · CHANGE · Sectie 2.2 / 5.2 — API-only auth + role-based admin via frontend

**Reden:** De architectuurdoelstelling is een backend die volledig API-first blijft, inclusief admin-acties. Filament is daarmee niet langer de primaire beheervorm voor operationele workflows.

**Oorspronkelijk (PLAN.md):**
> Admin: Laravel Filament 3.x als admin-paneel (draait binnen Laravel).

**Gewijzigd naar:**
- API-auth endpoints toegevoegd (`/api/v1/auth/register`, `/api/v1/auth/login`, `/api/v1/auth/me`, `/api/v1/auth/logout`, `/api/v1/auth/claim-team`) op Sanctum bearer tokens.
- Rollenmodel geïntroduceerd met vier rollen: `visitor`, `teamcaptain`, `moderator`, `admin`.
- Team ownership gekoppeld aan useraccounts via `teams.captain_user_id` en claim-flow op basis van bestaande registration edit tokens.
- Eerste admin API-only module geïmplementeerd: `/api/v1/admin/teams` (lijst/detail/status-moderatie), beveiligd met `auth:sanctum` + role middleware (`moderator|admin`).
- Frontend uitgebreid met:
	- auth composable/state
	- login/registratiepagina's
	- role-based route guards
	- admin teams view (`/admin/teams`) volledig via API.

**Impact:**
- Beheerflows kunnen stapsgewijs van Filament naar API+SPA worden gemigreerd zonder public site regressie.
- Dit vormt de fundering voor volledige backend API-only governance.

---

### WZ-017 · 2026-07-06 · CHANGE · Sectie 5.2 / 7 — Admin contentmoderatie uitgebreid naar API-only modules

**Reden:** Na de teams-module moest contentbeheer hetzelfde API-only pad volgen zodat publicatiebeheer niet meer afhankelijk is van server-side panelroutes.

**Oorspronkelijk (PLAN.md):**
> Write operations are primarily managed via Filament admin resources.

**Gewijzigd naar:**
- Nieuwe admin API modules toegevoegd voor:
	- posts (`/api/v1/admin/posts`)
	- pages (`/api/v1/admin/pages`)
	- team updates (`/api/v1/admin/team-updates`)
- Voor alle drie zijn list/detail/status endpoints aanwezig met `auth:sanctum` + `role:moderator,admin`.
- Uniforme publish-state request geïntroduceerd voor `is_published` en `published_at`.
- Frontend adminroutes en views toegevoegd:
	- `/app/admin/posts`
	- `/app/admin/pages`
	- `/app/admin/team-updates`
- Navigatie toont deze modules alleen voor moderator/admin gebruikers.

**Validatie:**
- Feature tests voor auth + teammoderatie + contentmoderatie zijn groen.
- Frontend `npm run build` slaagt met nieuwe admin modules.

---

### WZ-018 · 2026-07-06 · CHANGE · Sectie 5.2 / User management — Admin-only rolbeheer via API

**Reden:** Voor volledige API-only governance moest ook gebruikers- en rollenbeheer uit server-side panelworkflows gehaald worden.

**Oorspronkelijk (PLAN.md):**
> User management was onderdeel van backend admin tooling, zonder expliciete API-only rolbeheerflow.

**Gewijzigd naar:**
- Nieuwe admin-only user endpoints:
	- `GET /api/v1/admin/users`
	- `PATCH /api/v1/admin/users/{user}/role`
- Extra beveiliging:
	- endpointgroep alleen voor `role:admin`
	- self-role wijziging geblokkeerd (admin kan zichzelf niet per ongeluk degraderen)
- Frontend adminmodule toegevoegd:
	- route `/app/admin/users`
	- gebruikerslijst met directe rolwissel (visitor/teamcaptain/moderator/admin)
	- alleen zichtbaar/toegankelijk voor admin-rol.

**Validatie:**
- Nieuwe `AdminUserManagementApiTest` slaagt.
- Volledige auth/admin testset groen.
- Frontend build groen met admin user management view.

---

### WZ-019 · 2026-07-06 · CHANGE · Sectie 7 / Governance — Filament write-lock + audit logging

**Reden:** Om API-only beheer af te dwingen moest server-side admin write-capaciteit worden uitgezet voor gemigreerde modules. Daarnaast is traceability vereist voor moderatie- en rolwijzigingen.

**Oorspronkelijk (PLAN.md):**
> Filament resources verzorgen CRUD voor teams/posts/pages/updates.

**Gewijzigd naar:**
- Filament resources voor teams, posts, pagina's en team updates zijn read-only gemaakt:
	- create/edit/delete acties verwijderd
	- create/edit routes niet meer gepubliceerd
	- `canCreate/canEdit/canDelete` returns op `false`
- Write-acties lopen nu uitsluitend via API endpoints onder `/api/v1/admin/*`.
- Nieuwe auditlog-infrastructuur toegevoegd:
	- tabel `audit_logs`
	- logregels voor:
		- team status updates
		- post/page/team-update publish state updates
		- user role updates
	- actor + action + subject + before/after context opgeslagen.

**Validatie:**
- Auth/admin API testset groen (incl. audit assertions).
- Frontend build groen.

---

### WZ-020 · 2026-07-06 · ADD · Sectie 7 / Governance — Admin audit log viewer via API + SPA

**Reden:** Audit logging was aanwezig in de database, maar zonder beheerinterface was operationele controle beperkt. Admins moeten mutaties kunnen filteren en inspecteren in de API-first admin.

**Oorspronkelijk (PLAN.md):**
> Governance en logging niet uitgewerkt als dedicated admin module.

**Toegevoegd:**
- Nieuwe admin-only endpoint:
	- `GET /api/v1/admin/audit-logs`
	- filtermogelijkheden: `action`, `actor_user_id`, `subject_type`
- Nieuwe API resource voor audit log records met actor metadata en before/after payload.
- Frontend adminmodule toegevoegd:
	- route `/app/admin/audit-logs`
	- filterformulier + tabelweergave van actor/actie/subject/before/after
	- alleen toegankelijk voor admin users.

**Validatie:**
- Nieuwe `AdminAuditLogApiTest` slaagt.
- Volledige auth/admin suite groen.
- Frontend build groen met audit logs view.

---

### WZ-021 · 2026-07-06 · ADD · Sectie 7 / Admin UX — Dashboard summary voor moderatieprioriteiten

**Reden:** Na module-splitsing (teams/posts/pages/updates/users/audit) was er behoefte aan één startscherm met prioriteiten en recente activiteit.

**Toegevoegd:**
- Nieuwe endpoint:
	- `GET /api/v1/admin/dashboard-summary`
	- toegankelijk voor `moderator` en `admin`
- Response bevat:
	- KPI stats (`pending_teams`, `draft_posts`, `draft_pages`, `draft_team_updates`)
	- top 5 pending teams
	- recente auditactiviteiten
- Frontend route en view toegevoegd:
	- `/app/admin` → dashboard overzicht
	- navigatielink "Dashboard" zichtbaar voor moderator/admin.

**Validatie:**
- Nieuwe `AdminDashboardSummaryApiTest` slaagt.
- Volledige auth/admin suite groen.
- Frontend build groen met dashboard view.

---

### WZ-022 · 2026-07-06 · CHANGE · Sectie 5.2 / Security — Registratie-bewerkflow read-only publiek, writes alleen ingelogd

**Reden:** De bewerklink mocht niet langer anonieme wijzigingen toestaan. Bezoekers moeten kunnen lezen, maar mutaties (registratie opslaan en team-updates plaatsen) alleen na inloggen.

**Oorspronkelijk (PLAN.md):**
> Teamregistratie heeft een edit-token flow voor bewerken.

**Gewijzigd naar:**
- Publiek blijft read-only:
	- `GET /api/v1/registratie/{token}`
	- `GET /api/v1/registratie/{token}/updates`
- Write endpoints vereisen nu `auth:sanctum`:
	- `PUT /api/v1/registratie/{token}`
	- `POST /api/v1/registratie/{token}/updates`
- Extra autorisatie op controller-niveau:
	- alleen gekoppelde `teamcaptain` van het team mag muteren
	- `moderator`/`admin` behouden override-rechten
- Frontend bewerkpagina toont read-only melding met login-CTA en blokkeert submitknoppen zolang gebruiker niet ingelogd is.

**Validatie:**
- `RegistratieBewerkenTest`, `TeamUpdatesTest`, `AuthApiTest` groen (14 tests, 51 assertions).
- Frontend build groen.

---

### WZ-023 · 2026-07-06 · ADD · Sectie 5.2 / 6.x — Rich-media library voor content (afbeelding, video, STL, bijlagen)

**Reden:** Contentteams moeten media-assets (afbeeldingen, video, STL/3D, documenten) kunnen uploaden, hergebruiken en koppelen aan pages/blogs/team-content via API-first workflows.

**Toegevoegd:**
- Nieuwe authenticated rich-media API endpoints:
	- `GET /api/v1/media` (library lijst)
	- `POST /api/v1/media/upload` (upload + optionele directe koppeling)
	- `POST /api/v1/media/{media}/attach` (bestaand bestand koppelen)
- Rollenbeleid:
	- upload/attach toegestaan voor `teamcaptain`, `moderator`, `admin`
	- `moderator/admin` mogen koppelen aan `post`, `page`, `team`, `team_update`
	- `teamcaptain` alleen aan eigen `team` en eigen `team_update`
- Uploadvalidatie en bestandssoorten:
	- afbeeldingen, video, STL/OBJ/3MF, PDF/ZIP/TXT/MD
	- max 100MB per bestand
- Response bevat direct gebruikbare snippets:
	- `html_snippet`
	- `markdown_snippet`
- Nieuwe frontend admin module:
	- route `/app/admin/media`
	- uploadformulier met target-koppeling (type/id/collectie)
	- snippet-copy voor direct gebruik in content.

**Validatie:**
- Nieuwe `RichMediaUploadApiTest` groen.
- Regressies `AdminContentModerationApiTest` en `AuthApiTest` groen.
- Frontend build groen met `AdminMediaLibraryView`.

---

### WZ-024 · 2026-07-08 · CHANGE · Sectie 5.2 / Security — Nieuwe teamaanmelding alleen met ingelogd account

**Reden:** De gewenste flow is dat een gebruiker eerst een account heeft en daarna pas een team kan aanmaken. Dit voorkomt anonieme teamcreatie en maakt ownership direct expliciet.

**Oorspronkelijk (PLAN.md):**
> Teamregistratie is publiek toegankelijk en altijd open.

**Gewijzigd naar:**
- `POST /api/v1/registratie` vereist nu `auth:sanctum` naast throttling.
- Nieuwe teams worden direct gekoppeld aan de ingelogde gebruiker via `teams.captain_user_id`.
- Gebruikers met rol `visitor` worden bij registratie gepromoveerd naar `teamcaptain`.
- Frontend route `/app/aanmelden` vereist login (`requiresAuth`).
- Registratiecopy aangepast naar account-gebaseerde flow (geen claim meer dat een bewerklink altijd per e-mail wordt uitgegeven).

**Validatie:**
- `RegistratieTest` aangepast met unauthenticated blokkering + authenticated creatiepad.
- Frontend build groen.

---

### WZ-025 · 2026-07-08 · CHANGE · Sectie 6.x / UX — Account-gebaseerde "Mijn aanmelding" redirectflow

**Reden:** Gebruikers verwachtten dat `/app/aanmelding/wijzigen` direct naar hun eigen bewerkomgeving gaat in plaats van een uitlegpagina met handmatige stap.

**Oorspronkelijk (PLAN.md):**
> Wijzigflow primair via tokenlink-gedrag.

**Gewijzigd naar:**
- Route `/app/aanmelding/wijzigen` vereist login.
- Pagina `AanmeldingWijzigenView` doet bij openen automatisch `issueTeamEditLink()` en redirect direct naar de persoonlijke bewerk-URL.
- Bij ontbrekende teamkoppeling/fout blijft een fallback met retry + CTA naar nieuwe aanmelding.

**Validatie:**
- Frontend diagnostics foutvrij op router/view.
- Frontend build groen.

---

### WZ-026 · 2026-07-08 · ADD · Sectie 6.x / Editor UX — Gebruiksvriendelijke content-opmaak + resource insert

**Reden:** Editpagina's moesten gebruiksvriendelijker worden, met directe invoeging van media (video, STL, afbeeldingen) en minder handmatig schrijven van markup.

**Oorspronkelijk (PLAN.md):**
> Content-editing zonder expliciete WYSIWYG-achtige opmaaktoolbar.

**Toegevoegd/Gewijzigd:**
- Nieuwe herbruikbare formatting-toolbar component voor editors.
- `useContentInsertion` uitgebreid met selectie-gebaseerde format-acties (bold/italic/headings/lijsten/link/quote/code/divider).
- Toolbar gekoppeld in admin editors (posts/pages/team updates) en team-update sectie van de captain-bewerkpagina.
- Teamfoto UX verbeterd met live preview in `/app/aanmelden` en "huidige vs nieuwe" preview in `/app/aanmelding/bewerken/:token`.

**Validatie:**
- Frontend diagnostics foutvrij op aangepaste editor/views.
- Frontend build groen.

---

### WZ-027 · 2026-07-08 · DECISION · Sectie 8.1 / Process — Clarify-first AI werkwijze verplicht bij complexe taken

**Reden:** Om misinterpretatie te voorkomen bij grotere wijzigingen is expliciet vastgelegd dat onduidelijke en middel/hoge complexiteitstaken eerst worden bevestigd met gerichte vragen.

**Oorspronkelijk (PLAN.md):**
> Geen expliciete workflowregel voor verplichte verduidelijkingsvragen in AI-assisted development.

**Gewijzigd naar:**
- Workspace-instructie `.github/copilot-instructions.md` toegevoegd/aangescherpt met clarify-first regels.
- User-instructie `clarify-first.instructions.md` aangescherpt met mandatory confirmation bij middel/hoge complexiteit.
- Uitzondering gedefinieerd voor lage complexiteit en expliciete "direct uitvoeren" opdracht.

---

### WZ-028 · 2026-07-08 · CHANGE · Sectie 5.2 / Auth — Account lifecycle en password reset toegevoegd

**Reden:** De gewenste gebruikersflow vereist volledig accountbeheer zonder afhankelijkheid van token-gebaseerde bewerklinks.

**Gewijzigd naar:**
- Nieuwe auth/account endpoints toegevoegd:
	- `POST /api/v1/auth/forgot-password`
	- `POST /api/v1/auth/reset-password`
	- `PATCH /api/v1/auth/account`
	- `PATCH /api/v1/auth/password`
- Frontend routes/views toegevoegd voor:
	- `/app/account`
	- `/app/wachtwoord-vergeten`
	- `/app/wachtwoord-reset`
- Reset-mail URL wordt API-first gegenereerd naar frontend resetroute.

**Validatie:**
- `AuthApiTest` uitgebreid en groen.
- Frontend build groen.

---

### WZ-029 · 2026-07-08 · CHANGE · Sectie 5.2 / Registration — Tokenflow verwijderd, eigenaarschap via account

**Reden:** Teambeheer moet altijd aan een geauthenticeerd account gekoppeld zijn en niet langer afhankelijk zijn van losse edit tokens.

**Oorspronkelijk (PLAN.md):**
> Bewerkflow via token-link en claim endpoint.

**Gewijzigd naar:**
- Token-gerelateerde middleware/request/mail verwijderd (`EnsureValidRegistrationEditToken`, `ClaimTeamRequest`, `TeamBewerkLink`).
- Eigen registratiebeheer verloopt via:
	- `GET /api/v1/registratie/mijn`
	- `PUT /api/v1/registratie/mijn`
	- `GET /api/v1/registratie/mijn/updates`
	- `POST /api/v1/registratie/mijn/updates`
- Teamregistratie gebruikt accountownership (`teams.captain_user_id`) als bron van autorisatie.

**Validatie:**
- `RegistratieBewerkenTest`, `TeamUpdatesTest`, `UserFormsApiOnlyTest` aangepast en groen.

---

### WZ-030 · 2026-07-08 · CHANGE · Sectie 5.2 / Data-integriteit — Team e-mailadres wordt afgeleid van captain account

**Reden:** Teamcontact moet consistent zijn met de ingelogde registrator/captain en niet afwijken door client-input.

**Gewijzigd naar:**
- Backend forceert bij registratie `teams.email = authenticated_user.email`.
- Frontend prefillt het registratie-e-mailadres vanuit ingelogde user en toont dit read-only.

**Validatie:**
- Nieuwe assertie in `RegistratieTest` voor captain-email mapping.

---

### WZ-031 · 2026-07-08 · ADD · Sectie 5.2 / Roles — Teammembership domein en captain reviewflow

**Reden:** Gebruikers moeten zich kunnen aanmelden als teamlid bij bestaande teams, met beoordeling door de captain.

**Toegevoegd:**
- Nieuwe database-entiteit + migratie:
	- `team_memberships`
	- model `TeamMembership`
	- enum `TeamMembershipStatus`
- Nieuwe endpoints:
	- `POST /api/v1/teams/{team}/membership-requests`
	- `GET /api/v1/teams/mijn/lidmaatschappen`
	- `GET /api/v1/teams/mijn/membership-requests`
	- `PATCH /api/v1/teams/mijn/membership-requests/{teamMembership}`
- Frontend-integratie:
	- teamdetail: lidmaatschapsaanvraag versturen
	- captain bewerkpagina: pending aanvragen goedkeuren/afwijzen

**Validatie:**
- Nieuwe `TeamMembershipApiTest` toegevoegd en groen.

---

### WZ-032 · 2026-07-08 · CHANGE · Sectie 8 / Throttling — Registratie-limiter gesplitst per route-context

**Reden:** Account-gebonden bewerk/endpoints vielen onterecht onder e-mailgebaseerde throttle-keys zonder e-mailpayload, waardoor legitieme reads/writes te snel 429 konden geven.

**Gewijzigd naar:**
- `registratie.store` behoudt strikte limitering per IP + e-mail.
- `registratie.mijn*` gebruikt user-based limitering voor ingelogde accountflows.
- Fallback voor niet-auth context blijft IP-gebaseerd.

**Validatie:**
- Regressietest toegevoegd voor herhaalde `GET /api/v1/registratie/mijn` zonder snelle throttle-hit.

---

### WZ-033 · 2026-07-08 · CHANGE · Sectie 5.2 / Teammembership — Aanvragen toegestaan voor bestaande niet-afgewezen teams

**Reden:** In praktijk gaf lidmaatschapsaanvraag onnodige fouten bij bestaande teams zonder captain-account of met pending status.

**Gewijzigd naar:**
- Aanvragen zijn toegestaan voor `pending` en `approved` teams.
- Alleen `rejected` teams blokkeren nieuwe aanvragen.
- Frontend toont backend-foutmelding direct bij mislukte aanvraag.

**Validatie:**
- `TeamMembershipApiTest` uitgebreid met pending/no-captain/rejected scenario's en groen.

---

### WZ-028 · 2026-07-08 · CHANGE · Sectie 5.1 / 5.2 / 6.x — Edit-token verwijderd, account-only beheer + account lifecycle

**Reden:**
De edit-token flow zorgde voor extra complexiteit en inconsistente ownership. Gewenst model: account-first, team gekoppeld aan account, en volledige account lifecycle (registreren, wijzigen, wachtwoord resetten).

**Oorspronkelijk (PLAN.md):**
> Teamwijzigingen via token-gebaseerde bewerkflow.

**Gewijzigd naar:**
- API tokenroutes verwijderd voor team bewerken en team updates (`/api/v1/registratie/{token}` varianten).
- Nieuwe account-only endpoints toegevoegd:
	- `GET/PUT /api/v1/registratie/mijn`
	- `GET/POST /api/v1/registratie/mijn/updates`
- Legacy auth token-acties verwijderd:
	- `/api/v1/auth/claim-team`
	- `/api/v1/auth/team-edit-link`
- Auth/account lifecycle uitgebreid met:
	- `PATCH /api/v1/auth/account`
	- `PATCH /api/v1/auth/password`
	- `POST /api/v1/auth/forgot-password`
	- `POST /api/v1/auth/reset-password`
- Frontend migratie naar account-only flow:
	- `AanmeldingBewerkenView` gebruikt nu `/registratie/mijn` contract.
	- Route `/app/aanmelding/bewerken` vereist login (geen tokenparameter).
	- Nieuwe views: account beheren, wachtwoord vergeten, wachtwoord reset.
	- Navigatie uitgebreid met "Mijn account".
- Overbodige token-componenten verwijderd uit source (middleware/request/mailclass).

**Validatie:**
- Backend focused suite groen: 29 tests, 85 assertions.
- Frontend type-check + build groen.

---

### WZ-034 · 2026-07-09 · CHANGE · Sectie 5 / 6.x — Teams en Competitie samengevoegd tot tabbed pagina

**Reden:**
Publieke navigatie en content overlapten sterk tussen `Teams` en `Competitie`. Gewenst was een eenduidige gebruikersflow met twee subpagina's/tabs binnen dezelfde context.

**Oorspronkelijk (PLAN.md):**
> `Teams en Robots` en `Competitie` als losse publiekspagina's.

**Gewijzigd naar:**
- Nieuwe gecombineerde pagina met tabs in de frontend:
	- `GET /app/teams` -> tab `Teams`
	- `GET /app/teams/competitie` -> tab `Competitie`
- Oude route `GET /app/competitie` redirect nu naar `GET /app/teams/competitie`.
- Nieuwe viewcomponent toegevoegd: `TeamsCompetitionView`.
- Hoofdmenu opgeschoond: losse `Competitie`-entry verwijderd omdat deze nu onder `Teams` beschikbaar is.

**Validatie:**
- Frontend `npm run type-check` groen.
- Frontend build groen.
- Live deploy op server bevestigd op commit `88c915a`.

---

### WZ-035 · 2026-07-09 · DECISION · Sectie 2.2 / Deploy — Server runtime voor frontend builds vastgezet op Node 24 + npm 12

**Reden:**
Server-side frontend builds faalden op oudere Node-versie (`v18`) omdat Vite 8 en diverse dependencies minimaal Node 20+ vereisen.

**Oorspronkelijk (PLAN.md):**
> Frontend build/deploy flow beschreven zonder expliciete server Node-runtimeversie.

**Gewijzigd naar:**
- Server runtime geüpdatet naar:
	- Node `v24.18.0`
	- npm `12.0.0`
- Build/deploy command gebruikt expliciet nvm-context (`nvm use 24`) voordat `npm run build` wordt uitgevoerd.
- Dit is nu de operationele baseline voor production deploys van `roboktober-frontend`.

**Validatie:**
- Productiebuild draait succesvol op server met Node 24.
- Deployflow (`git pull` -> `npm run build` -> `php artisan optimize:clear`) succesvol uitgevoerd.

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
