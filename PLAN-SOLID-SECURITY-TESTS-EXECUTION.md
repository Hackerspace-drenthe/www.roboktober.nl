# Roboktober Uitvoeringsplan: SOLID, Security en Testdekking

Dit document is het uitvoerplan na de audit en bevat zowel roadmap als directe uitvoering.

## 1. Doelen

1. Architectuur verbeteren richting duidelijke verantwoordelijkheden (SOLID).
2. Security-risico's reduceren met focus op content rendering, auth en abuse protection.
3. Testdekking vergroten op kritieke backend- en frontend-stromen.

## 2. Scope en uitgangspunten

1. Werk in kleine, verifieerbare stappen met tests per fase.
2. Geen brede risicovolle refactor in een enkele wijziging.
3. Eerst de grootste risico's met directe impact op productie.

## 3. Gefaseerde aanpak

## Fase 1 - Direct hardenen (nu)

### Doel
Kritieke security en regressies direct oplossen zonder grote structurele refactor.

### Taken
1. HTML-output sanitizen voor publiek gerenderde content uit API resources.
2. Rate limiter regressie herstellen voor registratie endpoint.
3. Gerichte feature tests toevoegen/aanpassen voor bovenstaande gedragingen.

### Acceptatiecriteria
1. Schadelijke script/event-handler content komt niet meer als uitvoerbare HTML terug.
2. Registratie-throttle test geeft stabiel 429 op limietoverschrijding.
3. Doelgerichte tests slagen lokaal.

### Status
- Deels uitgevoerd
- Uitgevoerd: output sanitization voor page/post/team update/programma resources.
- Uitgevoerd: registratie limiter route match fix.
- Uitgevoerd: regressietests voor sanitization en throttle-pad.

## Fase 2 - Autorisatie en boundaries

### Doel
Autorisatie consistenter maken met policy-driven checks i.p.v. alleen route-role middleware.

### Taken
1. Policy matrix opstellen per admin resource (pages, posts, programma, editions, competition, links, users).
2. Controllers per resource voorzien van expliciete authorize calls.
3. Negatieve tests (403/401) per kritieke mutatie-endpoint uitbreiden.

### Acceptatiecriteria
1. Elke admin mutatie heeft expliciete policy of gedocumenteerde uitzondering.
2. Authz tests dekken moderator/admin/visitor paden.

### Status
- In uitvoering
- Uitgevoerd: policy-classes toegevoegd voor admin-resources (content, programma, editie, competitie, robots, links, users, audit, analytics).
- Uitgevoerd: expliciete authorize-checks toegevoegd in admin-controllers bovenop route-middleware.
- Gevalideerd: bestaande admin feature tests slagen met policy-checks actief.

## Fase 3 - SOLID refactor

### Doel
Controller-bloat reduceren en duplicatie verwijderen.

### Taken
1. Routebestand opdelen per domein (auth, public-content, team, admin).
2. Services introduceren voor analytics ingest/aggregatie en page visit incrementlogica.
3. Shared domain acties (bijv. media attach/replace) centraliseren waar dubbel aanwezig.

### Acceptatiecriteria
1. Grote controllers opgesplitst met kleinere methoden/services.
2. Dubbele logica nog maar op 1 plek aanwezig.
3. Bestaande API contracten blijven ongewijzigd.

### Status
- In uitvoering
- Uitgevoerd: routebestand opgesplitst in domeinbestanden (public/authenticated/admin) met behoud van bestaande route-namen.
- Uitgevoerd: dubbele analytics/page-visit logica geëxtraheerd naar gedeelde services (`PathNormalizer`, `PageVisitAggregateService`).
- Gevalideerd: route footprint behouden en regressietests op analytics/admin/programma slagen.

## Fase 4 - Teststrategie uitbreiden

### Doel
Regressierisico verlagen op backend en frontend.

### Taken
1. Backend: request validation edge-cases, policies, throttling, sanitization, uploads.
2. Frontend: Vitest unit tests voor composables en kritieke views.
3. E2E smoke flow (auth, programma, admin CRUD basis).

### Acceptatiecriteria
1. CI bevat backend + frontend teststappen.
2. Kritieke stromen hebben ten minste 1 positieve en 1 negatieve test.

### Status
- In uitvoering
- Uitgevoerd: Vitest + jsdom + Vue Test Utils testinfrastructuur toegevoegd.
- Uitgevoerd: Playwright smoke e2e infrastructuur toegevoegd met lokale webserverconfig.
- Uitgevoerd: eerste unit tests voor routercontracten en auth-composable.
- Uitgevoerd: publieke smoke e2e tests voor home en programma routes.
- Uitgevoerd: auth-guard smoke e2e tests voor `/aanmelden` en `/admin/users` redirect naar login.
- Uitgevoerd: extra e2e smoke dekking voor `/nieuws` bereikbaarheid en auth redirects voor `/account` en `/admin`.
- Uitgevoerd: CI workflow toegevoegd voor backend tests en frontend unit+e2e tests.
- Uitgevoerd: backend style-schuld weggewerkt met volledige Pint-fix; backend style-check is nu blocking in CI.
- Uitgevoerd: backend static analysis (PHPStan) staat voorlopig advisory met expliciete technical debt-track (huidige legacy-foutlast).
- Uitgevoerd: eerste reductieronde op static-analysis schuld in controllers/models; PHPStan raw-output verlaagd van 433 naar 166 regels.
- Uitgevoerd: tweede reductieronde op top-offenders (`CompetitionController`, `RichMediaController`, `TeamRegistrationUpdateController`); targeted fouten opgelost en totale raw-output verder verlaagd naar 128 regels.
- Uitgevoerd: extra foutenronde op admin management controllers (`EditionManagementController`, `ProgrammaItemManagementController`, `CompetitionManagementController`); totale raw-output verder verlaagd naar 109 regels.
- Uitgevoerd: extra foutenronde op upload/resource typing (`TeamPhotoUploadService`, `FilesystemMediaStorage`, `CompetitionBattleScoreResource` + call sites); totale PHPStan raw-output verder verlaagd naar 87 regels.
- Uitgevoerd: extra foutenronde op model typing (`HasFactory` generics, relatie-returntypes en media variant URL typing); totale PHPStan raw-output verder verlaagd naar 68 regels.
- Uitgevoerd: backend testjob opgesplitst in aparte Unit/Feature suites voor snellere feedback.
- Gevalideerd: unit tests en e2e smoke tests slagen lokaal.

## 4. Risico's en mitigaties

1. Risico: Sanitization breekt bestaande opmaak.
Mitigatie: conservatieve sanitizer + regressietests op toegestane HTML.

2. Risico: Grote refactor introduceert regressies.
Mitigatie: fasegewijze aanpak met contract-behoud en tests per stap.

3. Risico: Frontend test-infra kost initieel tijd.
Mitigatie: starten met beperkte smoke + composable unit tests.

## 5. Werklog

1. Commit baseline gemaakt voor huidige status.
2. Fase 1 gestart in deze wijzigingsronde.
