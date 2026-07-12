# ADR 0006: Observability policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: observability, logging, monitoring, operations

## Context
Het platform heeft een volwassen quality en deploy basis, maar observability-afspraken zijn nog niet expliciet vastgelegd. Zonder duidelijke standaard ontstaan blinde vlekken bij incidenten en regressies.

Belangrijkste risico's:
- Onvoldoende context in logs voor snelle diagnose
- Te late detectie van fouten en performanceproblemen
- Inconsistente health checks tussen componenten
- Onvoldoende zicht op API- en deploygedrag

## Beslissing
We hanteren een pragmatische observability policy met minimale verplichte standaarden.

Beleidsregels:
- Gestructureerde logging als standaard voor backend-events en fouten.
- Kritieke errors worden centraal zichtbaar gemaakt (log aggregation of error tracking).
- Health checks zijn verplicht voor applicatiebeschikbaarheid en basisafhankelijkheden.
- Kernmetrics zijn minimaal aanwezig voor API-gezondheid en deploy-impact.
- Observability-data bevat geen secrets of gevoelige payloads.

## Minimale standaarden
### Logging
- Log entries bevatten minimaal: timestamp, level, component, request/context id, boodschap.
- Error logs bevatten voldoende technische context voor reproduceerbaarheid.
- PII/secrets mogen niet gelogd worden.

### Error tracking
- Onverwachte exceptions worden centraal verzameld en geprioriteerd.
- Kritieke errors krijgen triage met eigenaar en opvolgactie.
- Herhalende errors worden gekoppeld aan issue of ADR wanneer structureel.

### Health checks
- Endpoint of mechanisme voor liveness en readiness is beschikbaar.
- Post-deploy check gebruikt health status als release-gate.
- Afhankelijkheden (bijv. DB/storage) worden minimaal gevalideerd op basisniveau.

### Metrics
- Minimaal monitoren:
  - API error rate
  - API latency (p95 waar mogelijk)
  - request volume
  - deploy success/failure events
- Trends worden periodiek geëvalueerd en gekoppeld aan verbeteracties.

## Overwogen alternatieven
### Optie A: Alleen basis server logs
- Voordelen:
  - minimale setup
  - lage operationele overhead
- Nadelen:
  - beperkte diagnosekwaliteit
  - trage incidentrespons

### Optie B: Volledig enterprise observability platform vanaf dag 1
- Voordelen:
  - maximale zichtbaarheid
  - uitgebreide analyseopties
- Nadelen:
  - hoge implementatie- en onderhoudslast
  - overkill voor huidige teamscope

### Optie C: Pragmatic minimum observability baseline (gekozen)
- Voordelen:
  - direct toepasbaar
  - sterke verbetering zonder over-engineering
  - schaalbaar naar uitgebreidere tooling later
- Nadelen:
  - minder diepgaande analyses dan enterprise setup
  - vereist discipline in metadata en logkwaliteit

## Gevolgen
### Positief
- Snellere detectie en diagnose van productieproblemen.
- Betere release-evaluatie door meetbare signalen.
- Meer consistente operationele werkwijze.

### Negatief
- Extra werk voor instrumentatie en onderhoud van dashboards/alerts.
- Team moet actief bewaken dat logging schoon en bruikbaar blijft.

### Security impact
- Positief: snellere detectie van afwijkend gedrag.
- Let op: observability mag geen gevoelige data exposen.

### Test impact
- Kritieke flows moeten controleerbare logging/error-signalen opleveren.
- Health checks en error paden krijgen gerichte testdekking waar haalbaar.

### Operationele impact
- Incidentrespons wordt sneller en consistenter.
- Deploy-checklists blijven health-check validatie bevatten.

## Implementatieplan
1. Leg policy vast in ADR-overzicht en architectuurdocument.
2. Definieer standaard logvelden en pas logging op kernflows toe.
3. Borg health checks in release en post-deploy verificatie.
4. Meet en review minimaal error rate, latency en deploy outcomes.
5. Evalueer elk kwartaal en scherpt policy aan via opvolgende ADR's.

## Rollback / migratie
- Policy is direct toepasbaar zonder schema- of code-migratievereiste.
- Implementatie van extra instrumentatie kan incrementeel per component.
- Bij operationele ruis worden signalen gefaseerd getuned, niet verwijderd.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
