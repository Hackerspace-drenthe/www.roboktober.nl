# Docs Improvement Plan

Datum: 2026-07-22
Doel: de documentatie onder `docs/` vereenvoudigen, beter vindbaar maken en minder dubbelzinnig maken voor maintainers en nieuwe bijdragers.

## 1. Huidige observaties

### Sterk
- Er is nu een duidelijke scheiding tussen projectinfo in de root en diepere documentatie in `docs/`.
- De belangrijkste plan-, architectuur- en operationele documenten zijn verplaatst.
- ADR's bestaan en geven governance-beslissingen context.

### Gaten
- De docs-index is nog te smal: niet alle relevante documenten zijn vanaf één plek zichtbaar.
- Er is overlap tussen `PLAN.md`, `PLAN2.md` en `PLAN-SOLID-SECURITY-TESTS-EXECUTION.md` in scope en intentie.
- Sommige documenten beschrijven status, andere beleid en weer andere uitvoering; dat onderscheid is nog niet overal expliciet.
- Referenties in docs zijn niet overal uniform genoeg voor snelle navigatie.
- De submappen `adr/` en `release-notes/` hebben nog geen duidelijke leesvolgorde voor nieuwe lezers.

## 2. Gewenste docs-structuur

### 2.1 Informatie-architectuur
1. `README.md` in root blijft alleen projectinformatie.
2. `docs/README.md` wordt de centrale startpagina voor alle documentatie.
3. `docs/` krijgt drie duidelijke lagen:
   - Strategie en visie: `PLAN.md`, `PLAN-CHANGES.md`, `ARCHITECTUUR.md`
   - Uitvoering en operaties: `DEPLOY-CHECKLIST.md`, `PLAN-SOLID-SECURITY-TESTS-EXECUTION.md`, `TESTMATRIX-CTA-FUNNEL.md`
   - Governance en wijzigingsgeschiedenis: `adr/`, `release-notes/`

### 2.2 Documentrollen
- `PLAN.md`: bron van waarheid voor product- en scopekeuzes.
- `PLAN-CHANGES.md`: delta-log voor afwijkingen op het plan.
- `ARCHITECTUUR.md`: huidige gewenste architectuur en principes.
- `PLAN2.md`: audit en analyse van open risico's en SOLID-refactor.
- `PLAN-SOLID-SECURITY-TESTS-EXECUTION.md`: concrete uitvoeringsroadmap.
- `DEPLOY-CHECKLIST.md`: operationele runbook/checklist.
- `TESTMATRIX-CTA-FUNNEL.md`: verificatie van een specifieke funnel.
- `antweight-gemini-image-pack.md`: content production pack voor visuals.

## 3. Prioriteiten

### P0 - Vindbaarheid en navigatie
1. Voeg alle relevante docs toe aan `docs/README.md`.
2. Maak per document een korte one-line beschrijving.
3. Voeg een vaste leesvolgorde toe voor nieuwe lezers.
4. Koppel ADR's en release notes duidelijk aan de hoofdindex.

### P1 - Consolidatie
1. Controleer overlap tussen `PLAN2.md` en `PLAN-SOLID-SECURITY-TESTS-EXECUTION.md`.
2. Bepaal of één van deze documenten samengevoegd, hernoemd of als subsectie gepubliceerd moet worden.
3. Splits status, beleid en uitvoering expliciet als dat in een document door elkaar loopt.

### P2 - Consistentie
1. Gebruik overal dezelfde verwijzingsstijl voor interne doc-links.
2. Voeg bovenaan documenten een korte contextregel toe: doel, status, doelgroep.
3. Maak de taal van operationele docs uniform: instructiegericht en kort.

### P3 - Onderhoudbaarheid
1. Voeg een changelog of release-note regel toe wanneer een document inhoudelijk wijzigt.
2. Herzie verouderde verwijzingen in comments en code waar docs zijn verhuisd.
3. Controleer periodiek of root- en docs-links nog gelijk lopen.

## 4. Concrete werkstroom

1. Inventariseer alle markdown-bestanden en markeer hun rol.
2. Voeg ontbrekende entries toe aan `docs/README.md`.
3. Leg de document-eigenaar en updatefrequentie per bestand vast.
4. Reduceer overlap tussen plan- en uitvoeringsdocs.
5. Voeg een vaste reviewcheck toe voor nieuwe of gewijzigde docs.

## 5. Succescriteria

- Een nieuwe medewerker vindt binnen 1 minuut het juiste startdocument.
- De rol van elk hoofddocument is in één zin duidelijk.
- Geen dubbele of strijdige uitleg tussen plan- en uitvoeringsdocs.
- Alle belangrijke docs zijn vanaf `docs/README.md` bereikbaar.
- De ADR- en release-note secties zijn logisch te scannen.

## 6. Volgende stap

Start met P0 en werk daarna P1 af; pas daarna pas fijnslijpen op stijl en onderhoud.