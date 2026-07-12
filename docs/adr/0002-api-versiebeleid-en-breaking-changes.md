# ADR 0002: API-versiebeleid en breaking changes

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: api, versioning, compatibility, frontend

## Context
De applicatie gebruikt versieerde API-routes onder `/api/v1`. Zonder expliciet versiebeleid ontstaat risico op:
- onbedoelde breaking changes voor frontend of externe consumers
- regressies bij release van backend-wijzigingen
- onduidelijkheid over de levensduur van oudere contracten

Omdat frontend en backend gescheiden deploybaar zijn, is contractstabiliteit noodzakelijk.

## Beslissing
We hanteren een expliciet API-versiebeleid met onderstaande regels.

Beleidsregels:
- Breaking changes gaan nooit in dezelfde major API-versie.
- Breaking changes vereisen een nieuwe API-versie (bijv. `/api/v2`).
- Niet-breaking uitbreidingen binnen bestaande versie zijn toegestaan.
- Nieuwe versie draait tijdelijk parallel met vorige versie tijdens migratieperiode.
- Deprecaties worden vooraf aangekondigd en gedocumenteerd.

Definitie breaking change:
- Endpoint verwijderen of URL-structuur incompatibel wijzigen
- Verplichte requestvelden toevoegen zonder backward-compatible default
- Responsevelden verwijderen of type/semantiek incompatibel wijzigen
- Auth/permissiegedrag wijzigen waardoor bestaande clients breken

## Overwogen alternatieven
### Optie A: Geen expliciete versieing, alleen changelog
- Voordelen:
  - minimale overhead
  - snel leveren op korte termijn
- Nadelen:
  - hoge kans op onverwachte breuken
  - weinig handvatten voor clients

### Optie B: Datum-gebaseerde versieing
- Voordelen:
  - duidelijke release-cadans
  - makkelijk chronologisch te volgen
- Nadelen:
  - minder intuïtief voor compatibiliteitsverwachting
  - hogere complexiteit in clientcommunicatie

### Optie C: Path-gebaseerde major versieing (gekozen)
- Voordelen:
  - helder en gangbaar patroon
  - expliciete compatibiliteitsgrenzen
  - eenvoudige routering en migratiepad
- Nadelen:
  - parallel onderhoud van meerdere versies mogelijk nodig
  - extra documentatie/verificatiewerk bij transities

## Gevolgen
### Positief
- Betere voorspelbaarheid voor frontend en andere consumers.
- Lagere kans op productie-incidenten door contractbreuk.
- Duidelijk kader voor code review en releasebeslissingen.

### Negatief
- Tijdelijk dubbel onderhoud bij overgang naar nieuwe versie.
- Extra discipline nodig in changelog en documentatie.

### Security impact
- Positief: auth/permissieaanpassingen kunnen gecontroleerd per versie uitgerold worden.
- Let op: oude versies mogen geen bekende securitylekken blijven bevatten; patchbeleid blijft actief zolang versie ondersteund is.

### Test impact
- Feature tests moeten per actieve API-versie kritieke contracten dekken.
- Contracttests voor response-shape op kernendpoints worden belangrijker bij parallelle versies.

### Operationele impact
- Monitoring en logs moeten API-versie meenemen voor inzicht in gebruik en migratievoortgang.
- Sunset van oude versies vereist planning, communicatie en release-checklist updates.

## Implementatieplan
1. Leg beleid vast in architectuurdocument en ADR-overzicht.
2. Voeg review-check toe: "Is dit een breaking API change?".
3. Bij eerste breaking wijziging: introduceer `/api/v2` parallel naast `/api/v1`.
4. Breid tests uit om beide versies van kritieke endpoints te valideren.
5. Definieer en communiceer deprecatie- en sunsetdatum voor oude versie.

## Rollback / migratie
- Bij problemen met nieuwe versie blijft vorige versie tijdelijk beschikbaar.
- Rollback gebeurt door verkeer/functionele afhankelijkheid naar oude versie te houden.
- Geen database rollback impliciet vereist, maar schemawijzigingen moeten backward-compatible gemigreerd worden gedurende transitie.

## Referenties
- Gerelateerd document: `ARCHITECTUUR.md`
- Gerelateerde map: `docs/adr`
