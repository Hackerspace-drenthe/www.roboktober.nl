# PLAN2 - Audit en SOLID herstructurering

Datum: 2026-07-08
Scope: volledige functionele audit van auth, registratie, teambeheer, contentbeheer, moderatie en rollen/rechten.

## 1. Audit samenvatting

### 1.1 Gecontroleerde flows
- Auth flow: registreren, inloggen, /auth/me, uitloggen.
- Team ownership flow: claim-team, team-edit-link, aanmelding aanmaken en wijzigen.
- Team updates flow: plaatsen en uitlezen van voortgangsupdates.
- Rich media flow: uploaden en koppelen aan content targets.
- Admin flow: teams, posts, pages, team-updates, users, audit-logs, dashboard.
- Frontend route guards en rolgestuurde navigatie.

### 1.2 Teststatus (uitgevoerde regressierun)
- 41 tests geslaagd, 155 assertions.
- Gedekt: AuthApiTest, RegistratieTest, RegistratieBewerkenTest, TeamUpdatesTest, RichMediaUploadApiTest, Admin* API tests.

## 2. Belangrijkste bevindingen

### 2.1 Positief (wat al goed staat)
- Rolgebaseerde toegang is functioneel aanwezig op API en frontend guards.
- Ownership-checks voor teamcaptain vs moderator/admin werken op kritieke mutaties.
- Audit logging is aanwezig op moderatie- en rolwijzigingen.
- Media upload/attach is aanwezig met bruikbare snippets.
- Admin modules zijn API-first en niet meer afhankelijk van write in Filament.

### 2.2 Gaten en risico's

#### Kritiek
1. Dubbele beheerparadigma's (account + edit token) maken autorisatiecomplexiteit hoger dan nodig.
- Huidig: account ownership en token-based edit bestaan naast elkaar.
- Effect: extra attack surface en meer uitzonderingen in mutatiechecks.

2. Registratieflow stuurt nog bewerklink-mail terwijl de primaire UX account-gebaseerd is.
- Effect: conceptuele inconsistentie, support-verwarring en onduidelijke security-grens.

#### Hoog
3. Autorisatie is verspreid over middleware, policies en handmatige controllerchecks.
- Effect: lastig te testen, kans op regressie bij nieuwe endpoints.

4. Controllers dragen te veel verantwoordelijkheden (validatie-input, businessregels, mutaties, logging).
- Effect: beperkt SOLID, moeilijk onderhoud en beperkte herbruikbaarheid.

5. Moderatie heeft geen expliciete state machine/transitieregels.
- Effect: statuswijzigingen zijn technisch mogelijk, maar governance-regels zijn niet centraal afgedwongen.

#### Middel
6. Role middleware parseert rollen met enum-cast zonder veilige fallback op configuratiefouten.
- Effect: foutieve routeconfig kan 500 geven in plaats van gecontroleerde 4xx.

7. Token abilities bestaan, maar autorisatie vertrouwt hoofdzakelijk op rollen/middleware.
- Effect: capability-model is niet eenduidig benut.

8. Content/media regels (welke collecties/targets per rol) zitten deels impliciet in code.
- Effect: policy drift mogelijk.

## 3. Target architectuur (SOLID)

### 3.1 Principes
- Single Responsibility: controllers alleen transportlaag.
- Open/Closed: policies en state transitions uitbreidbaar zonder controllerwijzigingen.
- Liskov/Interface Segregation: duidelijke service-contracten per domeinactie.
- Dependency Inversion: controllers afhankelijk van application services + interfaces.

### 3.2 Gelaagde opzet
- Presentation: controllers + API resources.
- Application: use-case services (commands/handlers) per flow.
- Domain: entities, policies, status transition rules.
- Infrastructure: storage, mail, queue, audit persistence.

### 3.3 Kernservices (nieuw)
- RegisterTeamService
- ClaimTeamService
- IssueTeamEditLinkService
- UpdateTeamRegistrationService
- CreateTeamUpdateService
- ModerateTeamStatusService
- ModerateContentStateService
- UpdateContentBodyService
- AttachMediaToTargetService
- AuthorizationGateService (centrale actor+target checks)

## 4. Rollen- en rechtenmodel (doel)

### 4.0 Harde instapregel (non-negotiable)
- Een gebruiker moet eerst een account hebben en ingelogd zijn voordat een team kan worden aangemaakt.
- Anonieme teamregistratie is niet toegestaan.
- Deze regel geldt voor API, frontend-flow en tests.

### 4.1 Rollen
- visitor: alleen publieke read + eigen accountbeheer.
- teamcaptain: eigen team + eigen teamupdates + eigen media-koppelingen.
- moderator: moderatie op teams/posts/pages/teamupdates + mediabeheer voor content.
- admin: alles van moderator + user role management + audit governance.

### 4.2 Rechtenmatrix (doel)
- Team registratie aanmaken: authenticated user (visitor/teamcaptain), resultaat koppelt captain_user_id.
- Team muteren: owner teamcaptain of moderator/admin override.
- Team update maken: owner teamcaptain of moderator/admin override.
- Post/Page/TeamUpdate modereren: moderator/admin.
- User rollen wijzigen: admin only, self-role change blocked.
- Audit logs lezen: admin only.
- Media upload: teamcaptain/moderator/admin.
- Media attach:
  - teamcaptain: alleen eigen team/eigen updates.
  - moderator/admin: alle ondersteunde targets.

### 4.3 Policy-first regel
Alle domeinbeslissingen via policies/services; geen ad-hoc role checks in controllers.

## 5. Duidelijke content management flow (doel)

### 5.1 Auteurflow
1. Auteur maakt/wijzigt content in editor.
2. Content gaat naar draft.
3. Preview beschikbaar met vaste sanitizer/render-regels.
4. Submit for review zet status naar in_review.

### 5.2 Moderatieflow
1. Moderator ziet review-queue met filters (type, prioriteit, ouderdom).
2. Acties: approve, request_changes, reject.
3. Elke actie met verplichte rationale.
4. Audit log schrijft actor, actie, oud/nieuw status, motivatie.

### 5.3 Publicatieflow
- Alleen approved kan published worden.
- Publish/unpublish met expliciete transition-regels.
- Optioneel scheduled publish via queue.

## 6. Teamregistratie en bewerken (doel flow)

### 6.1 Keuzebesluit (vereist)
Kies een van deze twee modellen en verwijder het andere om complexiteit te verlagen:
- Model A (aanbevolen): account-only ownership (geen bewerklink voor mutaties).
- Model B: token + account hybrid met expliciete TTL/refresh/one-time constraints.

### 6.2 Aanbevolen implementatie
- Houd token alleen als recovery-mechanisme met expliciete claim-flow.
- Mutaties uitsluitend op basis van account ownership + policy checks.
- Verwijder "standaard bewerklink-mail" uit primaire flow.

## 7. Moderatie- en statusmodel (doel)

### 7.1 Team status
- pending -> approved | rejected
- approved -> rejected (met reden)
- rejected -> pending (herbeoordeling)

### 7.2 Content status
- draft -> in_review -> approved -> published
- approved -> draft (na grote wijziging)
- published -> archived

### 7.3 Technische borging
- Centrale TransitionPolicy per domeinobject.
- Transition validator + domain events + audit logging.

## 8. Implementatieplan in fases

### Fase 1 - Governance baseline
- Introduceer centrale AuthorizationGateService.
- Verplaats controller-level role/owner checks naar policies/services.
- Harden role middleware bij ongeldige role strings.

### Fase 2 - Registratieflow normaliseren
- Besluit en implementeer Model A of B.
- Maak communicatie en UX consistent (mail, teksten, redirects, fouten).
- Verwijder legacy-pad dat niet meer bij gekozen model hoort.

### Fase 3 - Content lifecycle
- Voeg statusmodel draft/in_review/approved/published toe voor content.
- Voeg review queue endpoint + frontend views toe.
- Maak rationale verplicht op moderatieacties.

### Fase 4 - Service extraction (SOLID)
- Knip grote controllers op in use-case services.
- Introduceer command DTO's per use-case.
- Hou controllers dun: request -> service -> resource.

### Fase 5 - Media governance
- Centraliseer target/collectie permissieregels in policy/service.
- Voeg MIME/collectie constraints per target toe.
- Voeg scan/validation hooks toe voor uploads.

### Fase 6 - Observability en hardening
- Audit log uitbreiden met correlation id + reason fields.
- Voeg monitoring op 401/403/429 trends toe.
- Voeg regressietests toe voor transition rules en policy matrix.

## 9. Acceptatiecriteria
- Geen ad-hoc role checks meer in controllers (policy/service only).
- Alle muterende endpoints hebben eenduidige owner/moderator/admin regels.
- Content lifecycle is expliciet en afdwingbaar.
- Moderatiequeue met rationale is operationeel.
- Testset dekt role matrix, transitions en audit events.
- Frontend UX/tekst consistent met daadwerkelijke autorisatieflow.

## 10. Directe next actions (kort)
1. Besluit registratie-model (A account-only aanbevolen).
2. Verwijder bewerklink-mail uit primaire aanmeldflow of markeer als recovery-only.
3. Maak centrale authorisatie-service + refactor TeamRegistration* controllers.
4. Ontwerp en implementeer content state machine + review queue.
5. Breid tests uit met policy/transitie matrix.
