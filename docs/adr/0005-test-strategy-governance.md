# ADR 0005: Test strategy governance

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: testing, quality, ci, reliability

## Context
Het project heeft al meerdere testlagen (unit, feature, frontend unit/smoke) en een sterke CI-gate, maar zonder expliciet governance-kader ontstaat risico op inconsistent testgedrag tussen wijzigingen.

Belangrijkste risico's:
- Wijzigingen zonder passende testaanvulling
- Te veel focus op totaalcoverage i.p.v. risicogedreven dekking
- Flaky tests die releasevertraging en ruis veroorzaken
- Onduidelijkheid over minimale testeisen per type change

## Beslissing
We hanteren een expliciete test strategy governance policy met minimale eisen per wijzigingstype.

Beleidsregels:
- Testpiramide als standaard: unit eerst, daarna integratie/feature, dan e2e/smoke voor kritieke flows.
- Elke functionele wijziging krijgt minimaal 1 passende regressietest op het juiste niveau.
- Security- en auth-wijzigingen vereisen expliciete positieve en negatieve testcases.
- CI blijft blocking voor lint/type/static/test gates.
- Flaky tests krijgen prioritaire afhandeling met duidelijke workflow.

## Minimumeisen per change
### Backend
- Domeinlogica: unit tests waar mogelijk.
- API-contract of validatie/auth verandering: feature test verplicht.
- Bugfix: test die het oorspronkelijke falen reproduceert + fix valideert.

### Frontend
- UI-logica/composables: unit tests (Vitest) waar zinvol.
- Kritieke user journey wijziging: smoke/e2e dekking of update.
- Contractwijziging met API: type en gedragstest aanpassen.

### Cross-cutting
- Bij breaking of high-risk changes: extra regressietests op grensgevallen.
- Niet alleen coverage verhogen, maar risico's expliciet afdekken.

## Flaky test policy
- Definitie flaky: test die zonder codewijziging intermitterend faalt.
- Bij constatering:
  1. Label als flaky in issue/CI-notitie.
  2. Herstel met prioriteit binnen korte termijn.
  3. Tijdelijk quarantine alleen met expliciete eigenaar en deadline.
- Permanente negering van flaky tests is niet toegestaan.

## Overwogen alternatieven
### Optie A: Alleen globale coverage-doelstelling
- Voordelen:
  - simpel KPI-model
  - makkelijk te communiceren
- Nadelen:
  - kan leiden tot lage waarde tests
  - dekt risico's niet automatisch goed af

### Optie B: Volledige e2e-first strategie
- Voordelen:
  - hoge eind-tot-eind zekerheid op kernflows
- Nadelen:
  - trager, duurder en minder stabiel als primaire strategie
  - lastiger foutlokalisatie

### Optie C: Governance op basis van testpiramide en risicoklassen (gekozen)
- Voordelen:
  - goede balans tussen snelheid en betrouwbaarheid
  - betere onderhoudbaarheid en snellere feedback
- Nadelen:
  - vraagt discipline in code review en testontwerp

## Gevolgen
### Positief
- Consistentere testkwaliteit per wijziging.
- Snellere regressiedetectie op het juiste niveau.
- Minder ruis door expliciete flaky-aanpak.

### Negatief
- Extra reviewtijd om test-adequaatheid te beoordelen.
- Initiële inspanning om oude testgaten gericht op te vullen.

### Security impact
- Positief: auth/validatie/securitypaden krijgen structureel betere dekking.
- Restrisico: tests vervangen geen periodieke security-audits.

### Test impact
- Duidelijke definitie van minimaal verwachte testoutput per PR.
- Verbeterde focus op risicogedreven regressietests boven oppervlakkige coverage.

### Operationele impact
- Minder production incidents door betere pre-merge zekerheid.
- CI-signalen worden betrouwbaarder door flaky lifecyclebeleid.

## Implementatieplan
1. Leg policy vast in ADR-overzicht en architectuurdocument.
2. Voeg review-check toe: "Zijn passende tests toegevoegd/gewijzigd?".
3. Definieer minimaal issue-template voor flaky test tracking.
4. Evalueer maandelijks test failures, flaky trends en gaten.
5. Stuur teststrategie bij via opvolgende ADR's indien nodig.

## Rollback / migratie
- Governance is direct toepasbaar zonder code-migratie.
- Bij teamfrictie kan invoering gefaseerd, maar minimumeisen voor regressietests blijven actief.
- Wijzigingen op beleid worden versioneerbaar vastgelegd via volgende ADR.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde map: docs/adr
