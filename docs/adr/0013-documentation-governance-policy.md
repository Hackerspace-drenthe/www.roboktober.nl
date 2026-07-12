# ADR 0013: Documentation governance policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: documentation, governance, quality, process

## Context
Het project heeft nu een uitgebreide set aan architectuur- en governance ADR's. Zonder expliciet documentatiebeleid ontstaat risico op verouderde, inconsistente of moeilijk vindbare documentatie.

Belangrijkste risico's:
- Onduidelijk eigenaarschap van kern-documenten
- Documentatie die niet meebeweegt met code- en proceswijzigingen
- Wisselende kwaliteit en detailniveau tussen documenten
- PR's die belangrijke wijzigingen doorvoeren zonder doc-updates

## Beslissing
We hanteren een documentation governance policy met owner model, update cadence, kwaliteitscriteria en review gates.

Beleidsregels:
- Kritieke documenten hebben een expliciete eigenaar.
- Documentatie-updates zijn verplicht bij relevante code- of proceswijzigingen.
- Documenten volgen minimale kwaliteitscriteria voor structuur en actualiteit.
- PR-review bevat expliciete documentatie-check voor impactvolle changes.
- Periodieke documentatie-review borgt actualiteit en consistentie.

## Owner model
Per documenttype wordt eigenaarschap vastgelegd:
- Architectuur en ADR's: core maintainers.
- Deploy/runbooks/checklists: operations verantwoordelijken.
- API contract en integratiedocumentatie: backend/frontend maintainers.

Eigenaarschap betekent:
- inhoudelijke kwaliteit bewaken
- updates coördineren bij relevante wijzigingen
- periodieke reviewmomenten faciliteren

## Update cadence
- Change-driven: direct bijwerken in dezelfde PR als de wijziging.
- Periodiek: minimaal kwartaalreview op kern-documenten.
- Incident-driven: documentatie-aanpassing na postmortemacties.

## Kwaliteitscriteria
Minimale eisen voor governance-documenten:
- Duidelijke scope, context, beslissingen en gevolgen.
- Concrete, uitvoerbare afspraken (niet alleen intenties).
- Verwijzingen naar relevante gerelateerde documenten.
- Laatste updatecontext en status zijn eenduidig.
- Taal en structuur zijn consistent binnen de documentset.

## Review gates
Voor impactvolle wijzigingen geldt:
- PR-template/checklist bevat documentatie-impactvraag.
- Reviewer valideert of docs correct en volledig zijn bijgewerkt.
- Merge zonder noodzakelijke documentatie-update is niet toegestaan.

Voorbeelden van impactvolle wijzigingen:
- security/auth/permissie-aanpassingen
- deploy- of incidentproceswijzigingen
- breaking API wijzigingen
- architectuur- en infrastructuurbeslissingen

## Overwogen alternatieven
### Optie A: Geen expliciete document governance
- Voordelen:
  - lage korte-termijn overhead
- Nadelen:
  - snelle veroudering en inconsistentie
  - kennisverlies en hogere onboardingkosten

### Optie B: Strikt zwaar documentatieproces voor elke kleine wijziging
- Voordelen:
  - hoge formele consistentie
- Nadelen:
  - onnodige vertraging bij kleine changes
  - te veel proceslast voor huidige teamscope

### Optie C: Pragmatic governance met duidelijke minimale regels (gekozen)
- Voordelen:
  - goede balans tussen snelheid en kwaliteit
  - direct toepasbaar in bestaande workflow
  - schaalbaar naar meer volwassen model
- Nadelen:
  - vereist discipline in reviews en eigenaarschap

## Gevolgen
### Positief
- Betere actualiteit en betrouwbaarheid van documentatie.
- Snellere onboarding en minder contextverlies.
- Duidelijke verantwoordelijkheid voor onderhoud.

### Negatief
- Extra review- en onderhoudswerk.
- Team moet actief eigenaarschap en ritme bewaken.

### Security impact
- Positief: security-relevante afspraken blijven beter actueel en vindbaar.
- Let op: documentatiekwaliteit vervangt geen technische controles.

### Test impact
- Teststrategie-documentatie blijft synchroon met CI en testafspraken.
- Wijzigingen in testgovernance vereisen parallelle doc-updates.

### Operationele impact
- Runbooks/checklists worden consistenter onderhouden.
- Incident- en release-uitvoering profiteert van actuele procedures.

## Implementatieplan
1. Leg policy vast in ADR-overzicht en architectuurdocument.
2. Wijs eigenaren toe aan kern-documenten.
3. Voeg documentatie-impactcheck toe aan reviewproces.
4. Plan kwartaalreview voor architectuur- en operationele docs.
5. Evalueer periodiek op documentatiekwaliteit en open gaps.

## Rollback / migratie
- Policy is direct toepasbaar zonder technische migratie.
- Invoering kan gefaseerd starten bij kern-documenten.
- Aanscherpingen volgen via opvolgende ADR's.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
