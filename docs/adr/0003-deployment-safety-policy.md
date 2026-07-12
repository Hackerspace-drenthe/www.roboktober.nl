# ADR 0003: Deployment safety policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: deploy, operations, security, reliability

## Context
Deployment is een risicovol moment voor beschikbaarheid en dataintegriteit. Het project heeft al deployment scripts en een productieconfirmatie, maar zonder expliciet beleidsdocument blijven afspraken impliciet.

Belangrijke risico's:
- Directe productie-uitrol zonder staging-validatie
- Uitrol zonder dry-run of preflight checks
- Onbedoelde productie-acties door menselijke fouten
- Inconsistente post-deploy verificatie

## Beslissing
We hanteren een expliciete deployment safety policy met verplichte stappen.

Beleidsregels:
- Staging-first: wijzigingen gaan eerst via staging voordat productie volgt.
- Verplichte dry-run vóór echte productie-run.
- Productie vereist expliciete bevestiging via PRODUCTION_CONFIRM=deploy-production.
- Deployment gebeurt via gestandaardiseerde wrapper scripts.
- Post-deploy verificatie is verplicht met checklist en health checks.

Standaard flow:
1. Valideer CI status en release readiness.
2. Draai staging deployment en functionele smoke checks.
3. Draai productie dry-run en controleer remote command.
4. Start productie deployment met expliciete confirmatie.
5. Voer post-deploy checks uit en documenteer resultaat.

## Overwogen alternatieven
### Optie A: Handmatige ad-hoc deploy zonder policy
- Voordelen:
  - snel voor kleine wijzigingen
  - minimale procesoverhead
- Nadelen:
  - hoog operationeel risico
  - lage reproduceerbaarheid en auditability

### Optie B: Volledig geautomatiseerde one-click productie deploy
- Voordelen:
  - snelle uitrol
  - minder handmatige stappen
- Nadelen:
  - hoger risico zonder sterke gates en observability
  - foutimpact kan sneller escaleren

### Optie C: Gecontroleerde staged policy met guards (gekozen)
- Voordelen:
  - betere veiligheid en voorspelbaarheid
  - duidelijk runbook voor operators
  - lager risico op menselijke fouten
- Nadelen:
  - iets langere release door extra checks
  - meer discipline nodig in operationele uitvoering

## Gevolgen
### Positief
- Lager risico op incidenten tijdens productie-uitrol.
- Hogere reproduceerbaarheid van deploys.
- Duidelijker overdraagbare operationele werkwijze.

### Negatief
- Extra stappen kunnen doorlooptijd van releases verhogen.
- Team moet actief checklist en runbook onderhouden.

### Security impact
- Positief: expliciete confirmatie en gecontroleerde flow beperken kans op onbedoelde productie-wijzigingen.
- Let op: secrets blijven buiten repository en via omgeving beheerd.

### Test impact
- Deployment policy leunt op groene CI als gate.
- Smoke checks na staging en productie worden onderdeel van definitie van gereed voor release.

### Operationele impact
- Operators volgen vaste stappen in deploy documentatie.
- Incidentanalyse verbetert door consistent proces en traceerbaarheid.

## Implementatieplan
1. Borg policy in ADR en architectuurdocument.
2. Houd wrapper scripts leidend voor staging en productie.
3. Houd production confirm guard actief als harde eis.
4. Veranker dry-run en post-deploy checks in checklist.
5. Evalueer policy periodiek op basis van incidenten en deploy-metrics.

## Rollback / migratie
- Bij afwijking of fouten na productie-uitrol: rollback volgens bestaande deploystrategie en git-revisiebeheer.
- Policy zelf is direct toepasbaar zonder code-migratie.
- Aanpassingen aan scripts/checklists worden incrementeel uitgerold.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
