# ADR 0001: Service-layer voor complexe controllerflows

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: backend, laravel, solid, testability

## Context
In de huidige backend wordt domeinlogica deels in controllers afgehandeld. Voor eenvoudige endpoints is dat werkbaar, maar bij complexere flows leidt dit tot:
- te brede controller-methodes
- verminderde testbaarheid van businesslogica
- hogere kans op duplicatie tussen endpoints
- moeilijkere scheiding tussen HTTP-zorgen en domeinzorgen

Daarnaast is de kwaliteitslat verhoogd (PHPStan, tests, security checks). Een duidelijkere architectuurscheiding helpt om die lat duurzaam te houden.

## Beslissing
We introduceren een expliciete service-layer voor complexe use-cases in de backend.

Architectuurafspraak:
- Controllers orchestreren alleen request -> service -> response.
- Businesslogica voor niet-triviale flows verhuist naar services/actions.
- FormRequests blijven verantwoordelijk voor inputvalidatie.
- API Resources blijven verantwoordelijk voor outputpresentatie.

Conventie:
- Nieuwe services komen onder `app/Services` (of submappen per domein).
- Service methodes krijgen duidelijke input/output contracten.
- Services blijven framework-light waar praktisch mogelijk.

## Overwogen alternatieven
### Optie A: Logica in controllers houden
- Voordelen:
  - snelste korte-termijn implementatie
  - minder extra bestanden op korte termijn
- Nadelen:
  - slechtere onderhoudbaarheid bij groei
  - lagere herbruikbaarheid/testbaarheid
  - hogere kans op regressies

### Optie B: Alleen model-methodes uitbreiden (fat models)
- Voordelen:
  - centrale plek dicht bij data
  - minder controller-code
- Nadelen:
  - risico op overvolle modellen
  - mix van persistence- en use-case logica
  - minder expliciete use-case grenzen

### Optie C: Service-layer per use-case (gekozen)
- Voordelen:
  - duidelijke verantwoordelijkheden (SRP)
  - betere unit-testbaarheid van businesslogica
  - eenvoudiger hergebruik tussen endpoints/jobs/commands
- Nadelen:
  - meer structuur en extra klassen
  - initiële migratiekost

## Gevolgen
### Positief
- Betere scheiding van concerns in backend-laag.
- Verbeterde leesbaarheid van controllers.
- Grotere betrouwbaarheid door gerichtere tests.

### Negatief
- Meer bestanden en naamgevingsdiscipline nodig.
- Tijdsinvestering bij refactor van bestaande complexe controllers.

### Security impact
- Positief: authorisatiechecks en validatieregels worden consistenter toepasbaar doordat flowstappen explicieter zijn.
- Let op: services mogen nooit impliciet autorisatie overslaan; policies/gates blijven expliciet in de flow.

### Test impact
- Unit tests kunnen direct op services draaien zonder HTTP-setup.
- Feature tests blijven nodig voor contract/auth/validatie op endpoint-niveau.

### Operationele impact
- Geen directe deploy-impact.
- Wel impact op ontwikkelworkflow: code reviews moeten service-grenzen expliciet beoordelen.

## Implementatieplan
1. Identificeer top 3 complexe controllerflows (grootte, branching, side effects).
2. Introduceer per flow een service/action class met heldere contracten.
3. Verplaats businesslogica stap voor stap uit controller naar service.
4. Voeg unit tests toe voor services en behoud/uitbreid feature tests.
5. Borg conventie in review-checklist en documentatie.

## Rollback / migratie
- Migratie gebeurt incrementeel per endpoint.
- Als een refactor regressie geeft, kan de betreffende endpoint tijdelijk teruggezet worden naar vorige controller-implementatie zonder globale rollback.
- Geen database-migratie vereist voor deze architectuurbeslissing.

## Referenties
- Gerelateerd document: `ARCHITECTUUR.md`
- Gerelateerde map: `docs/adr`
