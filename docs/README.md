# Documentatie

Dit is de startpagina voor alle projectdocumentatie. De root-README blijft het projectoverzicht; hier staat de verdiepende documentatie met de huidige projectbaseline.

## Huidige baseline

- **Editie 2026 afgerond**, bevestigd door Rein op 5 november 2026. De website is al enkele maanden operationeel en stabiel; elektronica v4 werkt en is akkoord.
- Account-first authenticatie met verplichte TOTP 2FA voor bevestigde accounts.
- Teamregistratie en wijzigflows lopen via `/api/v1/registratie/mijn` en de bijbehorende account-endpoints.
- Admin- en contentflows zijn API-first en worden afgeschermd met rollen, policies en audit logging.
- Actuele gebruiks- en beheerinstructies blijven toepasbaar. Historische plannen, audits, persstukken en release notes bewaren hun oorspronkelijke context; open historische punten zijn geen resterende opleverblokkades voor 2026 en geen bewijs van alsnog uitgevoerde tests.

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

- [Architectuur](ARCHITECTUUR.md) - operationele platformarchitectuur en historische ontwerpcontext.
- [Projectplan](PLAN.md) - bevroren bron van scope, doelen en eisen.
- [Planwijzigingen](PLAN-CHANGES.md) - delta-log op het bevroren plan.
- [Plan 2: audit en SOLID](PLAN2.md) - historische audit en toen vastgelegde bevindingen.

## Uitvoering en operaties

- [Website en server](SERVER.md) - Laravel/Vue-stack, lokale ontwikkeling, Docker en Apache/deploy.
- [Deploy checklist](DEPLOY-CHECKLIST.md) - actuele deploy- en verificatiestappen.
- [SOLID/security test execution](PLAN-SOLID-SECURITY-TESTS-EXECUTION.md) - implementatie- en hardeninglog.
- [Testmatrix CTA-funnel](TESTMATRIX-CTA-FUNNEL.md) - verificatie van de publieke CTA-flow.
- [Antweight Gemini image pack](antweight-gemini-image-pack.md) - content pack voor visuals.
- [Perspakket](pers/README.md) - persmateriaal van editie 2026, met oorspronkelijke publicatiedata en gebruiksvoorwaarden.

## Governance en historiek

- [ADR's](adr/README.md) - architectuur- en beleidsbeslissingen.
- [Release notes](release-notes/README.md) - releases en functionele updates.
- [Docs improvement plan](DOCS-IMPROVEMENT-PLAN.md) - historische verbeterplanning; eventuele vervolgwerkzaamheden vallen buiten de afgeronde editie 2026.

## Verwijzingen

- Root projectinformatie: [../README.md](../README.md)