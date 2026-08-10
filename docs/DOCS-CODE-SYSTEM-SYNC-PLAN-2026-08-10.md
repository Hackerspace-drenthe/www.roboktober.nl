# Docs Sync Plan - 2026-08-10

Doel: alle documentatie in lijn brengen met de huidige code en de actuele productie-operatie.

## Scope

Deze planupdate dekt:
- monorepo overzicht en quickstart
- backend/frontend readmes
- deploy en productie runbooks
- architectuur en operationele realiteit
- teststrategie (lokaal, CI, productie)
- release notes en ADR delta

Niet in scope:
- inhoudelijke feature-ontwikkeling
- nieuwe infra-opzet

## Huidige feiten (geverifieerd)

Code en gedrag:
- Frontend heeft geen site-brede wachtwoordgate meer in root app shell.
- Programma kaart gebruikt Google Maps embed/route zonder API key flow.
- Media library client normaliseert raw Laravel paginator payloads in frontend API client.

Operatie:
- Productie mail werkt via SMTP met TransIP na auth-fix en correcte scheme-instelling.
- Productie DB testdata-opruiming is uitgevoerd voor teams/competitie-tabellen.
- Productie backup is gemaakt in storage/backups met timestamped sql.gz bestand.

Teststatus:
- Lokale backend tests slagen (`php artisan test`, `composer test`).
- Lokale frontend unit + e2e slagen.
- Productiehost heeft geen volledige dev test-tooling (verwacht gedrag).

## Inventaris en status

| Bestand | Status | Belangrijkste afwijking |
|---|---|---|
| README.md | deels actueel | vermeldt nog standaard localhost poorten als default pad, geen recente operationele notities |
| docs/README.md | deels actueel | baseline benoemt 2FA; moet geverifieerd worden tegen actuele auth-flow en docs |
| docs/ARCHITECTUUR.md | deels actueel | mist recente runtime-operatiepunten (mail, backup, cleanup runbook verwijzingen) |
| docs/DEPLOY-CHECKLIST.md | deels actueel | mist productie-realiteit voor php binary selectie en mail verificatie stap |
| deploy/README.md | grotendeels actueel | mist korte post-deploy verificatie voor SMTP en data safety |
| roboktober-api/README.md | deels actueel | mist operationele runbooks (mail test, backup/restore, cleanup safety) |
| roboktober-frontend/README.md | deels actueel | mist expliciete kaartprovider-keuze en media compat notitie |
| docs/TESTMATRIX-CTA-FUNNEL.md | onbekend/te valideren | mogelijk niet in lijn met recente lokale testresultaten |
| docs/release-notes/2026-07-22.md | verouderd | stelt dat backend test niet uitvoerbaar was; is nu lokaal wel uitvoerbaar |
| docs/release-notes/README.md | te valideren | indexeren van nieuwe release note nodig |
| docs/adr/README.md | te valideren | mogelijk ADR nodig voor mapprovider switch en operationele mail policy |

## Bestand-voor-bestand wijzigingsplan

### P0 (direct)

1. README.md
- Voeg sectie "Actuele operationele status" toe met links naar deploy, backup, mail runbook.
- Verduidelijk dat productievalidatie via smoke checks + mailcheck gebeurt.

2. docs/DEPLOY-CHECKLIST.md
- Voeg expliciete stap toe: juiste PHP binary bepalen/forceren.
- Voeg post-deploy SMTP check toe (verwachte output en foutpatronen).
- Voeg verplichte pre-delete backup stap toe voor datamutaties.

3. roboktober-api/README.md
- Voeg "Operations quick runbook" toe:
  - SMTP verificatie
  - DB backup
  - Veilige data cleanup (dry-run -> confirm -> execute)
- Verduidelijk verschil tussen lokale testuitvoering en productiehost.

4. docs/release-notes/2026-08-10.md (nieuw)
- Leg vast:
  - SMTP fix en validatie
  - productiedata cleanup
  - backup uitvoering
  - lokale volledige teststatus groen

### P1 (kort daarna)

5. roboktober-frontend/README.md
- Documenteer Google Maps gebruik in programma pagina.
- Documenteer media pagination compatibiliteit in API client.

6. docs/ARCHITECTUUR.md
- Werk operationele sectie bij met:
  - mail delivery mechanism
  - backup policy verwijzing
  - cleanup governance verwijzing
- Voeg "runtime reality" subsectie toe.

7. deploy/README.md
- Voeg compacte "post deploy verify" sectie toe:
  - API endpoint check
  - frontend route check
  - SMTP test command
  - log-check command

### P2 (governance)

8. docs/README.md
- Werk baseline bullets bij en link naar nieuwe runbooks/release note.

9. docs/release-notes/README.md
- Voeg indexregel toe voor 2026-08-10 release note.

10. docs/adr/README.md + evt nieuw ADR
- Beslis of mapprovider switch en mail operational policy ADR-waardig zijn.
- Indien ja: voeg ADR toe met context, besluit, gevolgen.

11. docs/TESTMATRIX-CTA-FUNNEL.md
- Actualiseer testmatrix op basis van huidige suites en resultaten.

## Uitvoering in 3 fasen

Fase 1 - Feitvalidatie (0.5 dag)
- Command-validatie van alle runbook commands.
- Controle op tegenspraken tussen README, docs en deploy.

Fase 2 - Schrijven en syncen (1 dag)
- P0 en P1 bestanden bijwerken.
- Nieuwe release note toevoegen.

Fase 3 - Review en borging (0.5 dag)
- Technische review door backend + frontend eigenaar.
- Final consistency check en merge.

## Definition of Done

- Alle P0-bestanden geupdate en gereviewd.
- Geen tegenspraken meer tussen docs en actuele code/operatie.
- Elk kritisch runbook bevat:
  - preconditions
  - exact command
  - expected output
  - failure hints
- Release note 2026-08-10 staat in index.

## Werkvolgorde (concrete checklist)

1. Update docs/DEPLOY-CHECKLIST.md
2. Update roboktober-api/README.md
3. Add docs/release-notes/2026-08-10.md
4. Update README.md
5. Update roboktober-frontend/README.md
6. Update docs/ARCHITECTUUR.md
7. Update deploy/README.md
8. Update docs/README.md
9. Update docs/release-notes/README.md
10. Update docs/TESTMATRIX-CTA-FUNNEL.md
11. ADR decision and optional ADR file

## Risico's en mitigatie

Risico:
- Docs lopen opnieuw achter door operationele hotfixes buiten PR flow.
Mitigatie:
- Voeg "docs impact" checkbox toe aan deploy/change workflow.

Risico:
- Productie-specifieke commando's raken verouderd bij serverwijziging.
Mitigatie:
- Maandelijkse runbook-validatie met timestamp en eigenaar.

Risico:
- Team voert tests op productie uit terwijl tooling ontbreekt.
Mitigatie:
- In elk relevant document expliciet: volledige suites lokaal/CI, niet op productie.
