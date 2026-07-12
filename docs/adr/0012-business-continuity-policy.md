# ADR 0012: Business continuity policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: continuity, resilience, disaster-recovery, operations

## Context
Het project heeft policies voor architectuur, security, testing, observability, data, access, dependency, change en incident management. Een expliciete business continuity policy ontbreekt nog, terwijl beschikbaarheid en herstelbaarheid bij grotere verstoringen cruciaal zijn.

Belangrijkste risico's:
- Onduidelijke hersteldoelen bij uitval
- Geen expliciete failover-principes voor kritieke onderdelen
- Backups zonder structurele disaster recovery oefeningen
- Trage, inconsistente besluitvorming bij grootschalige verstoringen

## Beslissing
We hanteren een pragmatische business continuity policy met RTO/RPO-richtwaarden, failover-principes en periodieke disaster recovery (DR) oefeningen.

Beleidsregels:
- Kritieke services krijgen gedefinieerde hersteldoelen (RTO/RPO).
- Continuiteitsmaatregelen zijn risicogestuurd en proportioneel.
- Failover- en fallback-principes zijn vooraf beschreven.
- DR-oefeningen worden periodiek uitgevoerd en geëvalueerd.
- Continuiteitsdocumentatie blijft actueel en testbaar.

## RTO en RPO
Richtlijnen per kritieke component:
- RTO (Recovery Time Objective): maximale hersteltijd na verstoring.
- RPO (Recovery Point Objective): maximaal acceptabel dataverlies in tijd.

Praktische aanpak:
- Classificeer componenten op impact (kritiek, hoog, medium, laag).
- Definieer per klasse streefwaarden voor RTO/RPO.
- Herzie waarden periodiek op basis van incidenten en businessbehoefte.

## Failover-principes
- Start met mitigatie die impact snel verlaagt (degraded mode waar mogelijk).
- Schakel naar vooraf gedefinieerde fallback/failover-routes bij langdurige storing.
- Prioriteer kernfunctionaliteit boven volledige functionaliteit tijdens herstel.
- Documenteer handmatige noodprocedures wanneer automatisering ontbreekt.

## Disaster recovery oefeningen
- Minimaal periodieke DR-oefening op kritieke herstelpaden.
- Oefeningen valideren minimaal:
  - herstel van backups
  - beschikbaarheid van kritieke endpoints na herstel
  - operationele rolverdeling en communicatie
- Resultaten leiden tot concrete verbeteracties met eigenaar en deadline.

## Overwogen alternatieven
### Optie A: Alleen reactief handelen bij uitval
- Voordelen:
  - lage korte-termijnoverhead
- Nadelen:
  - trage en onvoorspelbare hersteltijd
  - hogere kans op langdurige impact

### Optie B: Volledig enterprise BCP/DR framework direct invoeren
- Voordelen:
  - uitgebreide governance en compliance
- Nadelen:
  - te zwaar voor huidige teamscope
  - hoge implementatie- en onderhoudslast

### Optie C: Pragmatic continuity baseline met oefeningen (gekozen)
- Voordelen:
  - direct toepasbaar
  - duidelijke verbeterlus op basis van echte oefeningen
  - schaalbaar naar hogere volwassenheid
- Nadelen:
  - vereist discipline in planning en opvolging

## Gevolgen
### Positief
- Betere voorspelbaarheid van herstel bij grote verstoringen.
- Snellere besluitvorming door vooraf gedefinieerde doelen en rollen.
- Hogere operationele weerbaarheid door regelmatige oefeningen.

### Negatief
- Extra operationele planning en oefenlast.
- Continuiteitsdocumentatie vraagt actief onderhoud.

### Security impact
- Positief: incidenten met security-impact krijgen duidelijker herstelkader.
- Let op: continuiteit mag security-eisen niet verlagen tijdens noodprocedures.

### Test impact
- DR-oefeningen fungeren als periodieke validatie van herstelhypotheses.
- Kritieke herstelpaden krijgen gerichte regressie- en smoke-checks.

### Operationele impact
- Runbooks/checklists worden uitgebreid met continuiteitsstappen.
- Team ritme bevat geplande DR-validaties en lessons learned.

## Implementatieplan
1. Leg policy vast in ADR-overzicht en architectuurdocument.
2. Definieer kritieke componenten met voorlopige RTO/RPO-doelen.
3. Beschrijf failover/fallback-werkwijzen in operationele documentatie.
4. Plan eerste DR-oefening en leg meetresultaten vast.
5. Evalueer kwartaalmatig en stuur doelen/procedures bij.

## Rollback / migratie
- Policy is direct toepasbaar zonder technische migratieverplichting.
- Invoering gebeurt incrementeel, startend met meest kritieke componenten.
- Aanscherpingen worden via opvolgende ADR's vastgelegd.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
