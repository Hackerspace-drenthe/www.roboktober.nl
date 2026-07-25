# PLAN2 - Audit en SOLID herstructurering

Datum: 2026-07-22
Scope: volledige functionele audit van auth, registratie, teambeheer, contentbeheer, moderatie en rollen/rechten.

## 0. Huidige stand

De belangrijkste keuzes uit deze audit zijn inmiddels in code geborgd. De resterende waarde van dit document zit vooral in het vastleggen van de huidige risico's, de gewenste eindrichting en de open governance-vragen.

Voor de actuele technische baseline en de concrete hardeningstappen zie [PLAN-SOLID-SECURITY-TESTS-EXECUTION.md](PLAN-SOLID-SECURITY-TESTS-EXECUTION.md).

## 1. Audit samenvatting

### 1.1 Gecontroleerde flows
- Auth flow: registreren, inloggen, /auth/me, uitloggen.
- Team ownership flow: aanmelding aanmaken en wijzigen via het account-first pad.
- Team updates flow: plaatsen en uitlezen van voortgangsupdates.
- Rich media flow: uploaden en koppelen aan content targets.
- Admin flow: teams, posts, pages, team-updates, users, audit-logs, dashboard.
- Frontend route guards en rolgestuurde navigatie.

### 1.2 Teststatus (uitgevoerde regressierun)
- 41 tests geslaagd, 155 assertions.
- Gedekt: AuthApiTest, RegistratieTest, RegistratieBewerkenTest, TeamUpdatesTest, RichMediaUploadApiTest, Admin* API tests.

### 1.3 Huidige account- en authflow
De huidige account- en 2FA-flow is beschreven in [PLAN-SOLID-SECURITY-TESTS-EXECUTION.md](PLAN-SOLID-SECURITY-TESTS-EXECUTION.md) en hoort niet dubbel in dit auditdocument te staan.

## 2. Belangrijkste bevindingen

### 2.1 Positief (wat al goed staat)
- Rolgebaseerde toegang is functioneel aanwezig op API en frontend guards.
- Ownership-checks voor teamcaptain vs moderator/admin werken op kritieke mutaties.
- Audit logging is aanwezig op moderatie- en rolwijzigingen.
- Media upload/attach is aanwezig met bruikbare snippets.
- Admin modules zijn API-first en niet meer afhankelijk van write in Filament.

### 2.2 Gaten en risico's

#### Kritiek
1. Historisch opgelost: dubbele beheerparadigma's (account + edit token) zijn in de huidige flow teruggebracht tot account ownership.
- Huidig: mutaties lopen via account ownership en auth/policy checks.
- Effect van de oude situatie: grotere attack surface en meer uitzonderingen in mutatiechecks.

2. Historisch opgelost: de primaire UX stuurt niet meer op een losse bewerklink-mail.
- Huidig: registratie, wijziging en accountbeheer lopen via de account-first flow.
- Effect van de oude situatie: conceptuele inconsistentie en support-verwarring.

#### Hoog
3. Autorisatie is inmiddels grotendeels policy-first en ondersteund door middleware en expliciete controllerchecks.
- Huidig: de rol- en policylaag is in code aanwezig en breed toegepast.
- Restpunt: nieuwe endpoints moeten consequent dezelfde patronen volgen.

4. Sommige controllers dragen nog te veel verantwoordelijkheden (validatie-input, businessregels, mutaties, logging).
- Effect: beperkt SOLID, moeilijk onderhoud en beperkte herbruikbaarheid.

5. Moderatie heeft nog niet overal een expliciete state machine/transitieregels-laag.
- Effect: statuswijzigingen zijn technisch mogelijk, maar governance-regels zijn niet centraal afgedwongen.

#### Middel
6. Role middleware parseert rollen met enum-cast zonder veilige fallback op configuratiefouten.
- Status: dit is grotendeels afgedekt door de huidige role/capability laag en tests.

7. Het capability-model is inmiddels expliciet genoeg voor de huidige admin- en media-acties.
- Huidig: rollen, policies en route-middleware coderen de belangrijkste permissies.
- Restpunt: bij nieuwe modules moet dezelfde explicitering worden aangehouden.

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

### 6.1 Keuzebesluit
Model A is de actuele implementatie: account-only ownership, geen token als primaire mutatieroute.

### 6.2 Huidige implementatie
De implementatiedetails van account ownership, policies en 2FA-bescherming staan in [PLAN-SOLID-SECURITY-TESTS-EXECUTION.md](PLAN-SOLID-SECURITY-TESTS-EXECUTION.md).

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

## 8. Vervolgsporen

Deze punten zijn de uitkomst van de audit en horen inhoudelijk bij de roadmap, niet bij de uitvoeringslog:
- Centraliseer autorisatie verder in services en policies.
- Maak content- en moderatiestatussen explicieter in model of policy.
- Verklaar nog niet-uniforme media- en targetregels op één plek.
- Verlaag controller-bloat waar dezelfde beslissingen nog op meerdere plekken terugkomen.
- Houd observability en regressietests in lijn met de huidige account-first baseline.

## 9. Acceptatiecriteria
- Geen ad-hoc role checks meer in controllers (policy/service only).
- Alle muterende endpoints hebben eenduidige owner/moderator/admin regels.
- Content lifecycle is expliciet en afdwingbaar.
- Moderatiequeue met rationale is operationeel.
- Testset dekt role matrix, transitions en audit events.
- Frontend UX/tekst consistent met daadwerkelijke autorisatieflow.

## 9.1 Wat al gehaald is
De huidige baseline is gerealiseerd; de samenvatting daarvan staat in [PLAN-SOLID-SECURITY-TESTS-EXECUTION.md](PLAN-SOLID-SECURITY-TESTS-EXECUTION.md).

## 10. Directe next actions (kort)
1. Houd policy- en route-matrix synchroon met nieuwe admin modules.
2. Breng content lifecycle en moderatiestatus explicieter in model of policy.
3. Houd documentatie en release notes in sync met de huidige account-first baseline.
4. Gebruik dit document als checklist voor resterende auditpunten, niet als uitvoeringsplan.
