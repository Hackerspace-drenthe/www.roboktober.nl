# Documentatie

Dit is de startpagina voor alle projectdocumentatie. De root-README blijft het projectoverzicht; hier staat de verdiepende documentatie met de huidige projectbaseline.

## Huidige baseline

- Account-first authenticatie met verplichte TOTP 2FA voor bevestigde accounts.
- Teamregistratie en wijzigflows lopen via `/api/v1/registratie/mijn` en de bijbehorende account-endpoints.
- Admin- en contentflows zijn API-first en worden afgeschermd met rollen, policies en audit logging.
- Documentatie is opgesplitst in strategie, uitvoering, governance en release notes.

## Leesvolgorde

1. [Architectuur](ARCHITECTUUR.md)
2. [Projectplan](PLAN.md)
3. [Planwijzigingen](PLAN-CHANGES.md)
4. [Plan 2: audit en SOLID](PLAN2.md)
5. [SOLID/security test execution](PLAN-SOLID-SECURITY-TESTS-EXECUTION.md)
6. [Deploy checklist](DEPLOY-CHECKLIST.md)
7. [Testmatrix CTA-funnel](TESTMATRIX-CTA-FUNNEL.md)
8. [Docs improvement plan](DOCS-IMPROVEMENT-PLAN.md)

## Strategisch overzicht

- [Architectuur](ARCHITECTUUR.md) - huidige en gewenste platformarchitectuur.
- [Projectplan](PLAN.md) - bevroren bron van scope, doelen en eisen.
- [Planwijzigingen](PLAN-CHANGES.md) - delta-log op het bevroren plan.
- [Plan 2: audit en SOLID](PLAN2.md) - audit, huidige staat en resterende risico's.

## Uitvoering en operaties

- [Deploy checklist](DEPLOY-CHECKLIST.md) - actuele deploy- en verificatiestappen.
- [SOLID/security test execution](PLAN-SOLID-SECURITY-TESTS-EXECUTION.md) - implementatie- en hardeninglog.
- [Testmatrix CTA-funnel](TESTMATRIX-CTA-FUNNEL.md) - verificatie van de publieke CTA-flow.
- [Antweight Gemini image pack](antweight-gemini-image-pack.md) - content pack voor visuals.
- [Perspakket](pers/README.md) - actueel persbericht en recente v4-beelden voor herpublicatie.

## Governance en historiek

- [ADR's](adr/README.md) - architectuur- en beleidsbeslissingen.
- [Release notes](release-notes/README.md) - releases en functionele updates.
- [Docs improvement plan](DOCS-IMPROVEMENT-PLAN.md) - roadmap voor verdere documentatieverbetering.

## Verwijzingen

- Root projectinformatie: [../README.md](../README.md)