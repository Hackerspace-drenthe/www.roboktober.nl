# ADR 0007: Data governance policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: data, governance, backup, retention, privacy

## Context
Het platform heeft inmiddels duidelijke architectuur-, security-, test- en observability-afspraken. Een expliciet data governance kader ontbreekt nog, terwijl data lifecycle en herstelbaarheid cruciaal zijn voor continuiteit en compliance.

Belangrijkste risico's:
- Onheldere dataretentie en opschoning
- Backups zonder periodieke herstelvalidatie
- Onduidelijke classificatie van gegevensgevoeligheid
- Inconsistente procedures bij dataverlies of corruptie

## Beslissing
We hanteren een pragmatische data governance policy met minimale verplichte standaarden.

Beleidsregels:
- Data wordt geclassificeerd op gevoeligheid en kritischheid.
- Retentie en verwijdering volgen vastgelegde regels per datacategorie.
- Backups zijn verplicht en hersteltesten worden periodiek uitgevoerd.
- Productiedata wordt niet ongecontroleerd naar ontwikkelomgevingen gekopieerd.
- Dataveranderingen met risico-impact vereisen expliciete review en rollbackplan.

## Dataclassificatie
Minimale klassen:
- Openbaar: data die publiek gedeeld mag worden.
- Intern: operationele data zonder directe gevoeligheid.
- Gevoelig: persoonsgegevens, account- of contactgegevens.
- Kritiek: data die essentieel is voor bedrijfscontinuiteit.

Per klasse bepalen we minimaal:
- Toegangsniveau
- Logging- en maskeringsvereisten
- Retentieperiode
- Backup-prioriteit

## Retentie en verwijdering
- Retentie wordt per datacategorie vastgesteld en gedocumenteerd.
- Data zonder actuele noodzaak wordt periodiek opgeschoond.
- Verwijderacties zijn traceerbaar en waar nodig herstelbaar binnen bewaartermijn.
- Wettelijke bewaarplichten gaan voor op functionele voorkeuren.

## Backup en herstel
- Regelmatige geautomatiseerde backups van kritieke data.
- Backups worden veilig opgeslagen met toegangsbeperking.
- Periodieke restore-test is verplicht om herstelbaarheid te verifiëren.
- Recoverydoelen (RPO/RTO) worden pragmatisch vastgesteld en periodiek herzien.

## Overwogen alternatieven
### Optie A: Ad-hoc data beheer zonder expliciet beleid
- Voordelen:
  - lage korte-termijnoverhead
  - snelle operationele uitvoering
- Nadelen:
  - hoge kans op inconsistentie
  - groter risico op dataverlies en compliance-problemen

### Optie B: Volledig enterprise data governance framework direct invoeren
- Voordelen:
  - zeer uitgebreid controlekader
  - sterke auditability
- Nadelen:
  - hoge implementatielast
  - te zwaar voor huidige teamscope

### Optie C: Pragmatic baseline data governance (gekozen)
- Voordelen:
  - direct toepasbaar
  - duidelijke minimale veiligheids- en continuiteitslat
  - schaalbaar naar uitgebreidere governance
- Nadelen:
  - vereist periodieke discipline in uitvoering
  - minder diepgaand dan volledige enterprise frameworks

## Gevolgen
### Positief
- Betere voorspelbaarheid van data lifecycle en herstelprocedures.
- Lagere kans op langdurige impact bij data-incidenten.
- Duidelijkere verantwoordelijkheden rond datagebruik en opslag.

### Negatief
- Extra operationele taken voor retentiebeheer en restore-tests.
- Regelmatige reviewmomenten vragen blijvende capaciteit.

### Security impact
- Positief: betere controle op gevoelige en kritieke data.
- Let op: classificatie is alleen effectief als toegangsbeheer en logging mee evolueren.

### Test impact
- Restore-procedures en datamigraties vereisen periodieke validatie.
- Kritieke dataflows verdienen extra regressietests bij schema- of lifecyclewijzigingen.

### Operationele impact
- Backups en hersteltests worden expliciete onderdelen van runbooks/checklists.
- Incidentrespons op data-issues wordt sneller door vooraf gedefinieerde procedures.

## Implementatieplan
1. Leg policy vast in ADR-overzicht en architectuurdocument.
2. Definieer datacategorieen met eigenaar per domein.
3. Documenteer retentie- en verwijderregels per categorie.
4. Borg backupschema en periodieke restore-test in operationele checklist.
5. Evalueer elk kwartaal op incidenten, restore-resultaten en verbeterpunten.

## Rollback / migratie
- Policy is direct toepasbaar en vraagt geen directe schemawijziging.
- Invoering gebeurt incrementeel per datadomein.
- Aanpassingen of aanscherpingen worden via opvolgende ADR's vastgelegd.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
