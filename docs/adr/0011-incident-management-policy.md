# ADR 0011: Incident management policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: incident, operations, reliability, communication

## Context
Het project heeft nu policies voor architectuur, security, testing, observability, data, access, dependency en change management. Een expliciete incident management policy ontbreekt nog, waardoor incidentafhandeling per situatie kan verschillen.

Belangrijkste risico's:
- Onduidelijke prioritering bij storingen
- Trage escalatie en rolverwarring tijdens incidenten
- Inconsistente communicatie naar stakeholders
- Gemiste structurele verbeteringen na incidenten

## Beslissing
We hanteren een pragmatische incident management policy met severity levels, escalation protocol, on-call afspraken en postmortem standaard.

Beleidsregels:
- Incidenten worden direct geclassificeerd op severity.
- Incident command structuur met duidelijke rollen wordt toegepast.
- Escalatiepaden zijn vooraf vastgesteld en tijdsgebonden.
- Communicatie tijdens incidenten volgt een vaste cadence.
- Elk significant incident krijgt een korte, actiegerichte postmortem.

## Severity model
- Sev1: Kritieke uitval of datarisico met directe gebruikersimpact.
- Sev2: Grote functionele verstoring met significante impact.
- Sev3: Beperkte verstoring of degradatie met workaround.
- Sev4: Kleine afwijking met lage impact.

Richtlijn:
- Severity kan tijdens incident worden herzien op basis van nieuwe feiten.

## Rollen tijdens incident
- Incident Commander: coördineert beslissingen en prioriteiten.
- Communications Lead: verzorgt interne/externe statusupdates.
- Technical Lead: stuurt diagnose en mitigatie aan.
- Scribe: houdt tijdlijn, acties en besluiten bij.

Kleine teams mogen rollen combineren, maar verantwoordelijkheden blijven expliciet.

## Escalation protocol
- Sev1: onmiddellijke escalatie naar kernverantwoordelijken.
- Sev2: snelle escalatie binnen afgesproken reactietijd.
- Sev3/Sev4: reguliere triage met geplande opvolging.

Escalatie gebeurt op basis van:
- Business impact
- Security/data risico
- Duur en trend van verstoring

## On-call afspraken
- Er is altijd een primaire verantwoordelijke voor operationele incidenten.
- Back-up contact is beschikbaar voor escalatie.
- On-call overdracht bevat open risico's, actieve mitigaties en aandachtspunten.

## Communicatiecadence
Voor actieve incidenten:
- Startmelding met eerste impactinschatting.
- Periodieke updates op vaste intervallen per severity.
- Eindmelding met status, impact en vervolgstappen.

Na incident:
- Korte samenvatting en acties voor stakeholders.

## Postmortem standaard
Significante incidenten (minimaal Sev1 en Sev2) krijgen postmortem met:
- Tijdlijn van gebeurtenissen
- Root cause en bijdragen factoren
- Wat goed ging / wat beter moet
- Concreet actieplan met eigenaar en deadline

Principes:
- Blameless aanpak
- Focus op systeemverbetering

## Overwogen alternatieven
### Optie A: Informele incidentafhandeling zonder standaard
- Voordelen:
  - minimale proceslast
- Nadelen:
  - inconsistentie en trage besluitvorming
  - lagere leeropbrengst na incidenten

### Optie B: Volledig enterprise incident framework direct invoeren
- Voordelen:
  - uitgebreid governancekader
- Nadelen:
  - te zwaar voor huidige teamscope
  - hoge operationele overhead

### Optie C: Pragmatic incident baseline policy (gekozen)
- Voordelen:
  - direct toepasbaar
  - duidelijke structuur bij verstoringen
  - schaalbaar naar meer volwassen model
- Nadelen:
  - vraagt discipline in documentatie en opvolging

## Gevolgen
### Positief
- Snellere en consistentere incidentrespons.
- Betere samenwerking door heldere rollen en escalatie.
- Hogere leerwaarde via postmortems en opvolgacties.

### Negatief
- Extra proceslast tijdens en na incidenten.
- Noodzaak om communicatie en logging actief bij te houden.

### Security impact
- Positief: security-incidenten worden sneller en eenduidiger opgeschaald.
- Let op: policy vereist actuele contact- en escalatiegegevens.

### Test impact
- Incidenten leiden tot gerichte regressietests op het faalpad.
- Postmortem acties kunnen extra testscenario's toevoegen.

### Operationele impact
- On-call en escalatieritme worden expliciet onderdeel van runbooks.
- Minder MTTR door vooraf gedefinieerde werkwijze.

## Implementatieplan
1. Leg policy vast in ADR-overzicht en architectuurdocument.
2. Definieer severity matrix en escalatiecontacten.
3. Voeg incident-communicatieformat toe aan operationele documentatie.
4. Introduceer compact postmortem template met actie-eigenaars.
5. Evalueer periodiek op MTTR, herhaalincidenten en actie-afronding.

## Rollback / migratie
- Policy is direct toepasbaar zonder technische migratie.
- Invoering kan gefaseerd starten bij Sev1/Sev2 incidenten.
- Aanscherpingen worden via opvolgende ADR's vastgelegd.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
