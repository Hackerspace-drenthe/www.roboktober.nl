# ADR 0009: Access governance policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: access, identity, security, operations

## Context
Het project heeft inmiddels policies voor security, deployment, testing, observability en data governance. Een expliciete access governance policy ontbreekt nog, terwijl beheer van accounts, rechten en sleutels essentieel is voor security en continuiteit.

Belangrijkste risico's:
- Accounts zonder eigenaar of zonder periodieke review
- Rechten die in de tijd te ruim worden (privilege creep)
- Sleutels/tokens zonder rotatiebeleid
- Trage deprovisioning bij rolwijziging of offboarding

## Beslissing
We hanteren een pragmatische access governance policy voor account lifecycle, privilege reviews en key rotation.

Beleidsregels:
- Elk account en elke credential heeft een expliciete eigenaar.
- Least privilege is standaard en rechten zijn tijdig herzien.
- JIT/JEA-principes worden toegepast waar haalbaar voor verhoogde rechten.
- Keys/tokens worden periodiek geroteerd volgens afgesproken ritme.
- Offboarding en rolwijzigingen triggeren directe access-update.

## Account lifecycle
### Provisioning
- Nieuwe toegang alleen op basis van aantoonbare noodzaak.
- Minimaal benodigde rechten bij start (geen standaard admin).
- Toegang wordt geregistreerd met eigenaar, doel en scope.

### Changes
- Rechtenwijzigingen verlopen via traceerbare change met reviewer.
- Tijdelijke elevated access krijgt vervaldatum.
- Shared accounts worden vermeden; indien onvermijdelijk: extra logging en beheer.

### Deprovisioning
- Bij offboarding of rolwissel wordt toegang direct ingetrokken of aangepast.
- Credentials van vertrekkende gebruikers worden direct ongeldig gemaakt.
- Service-toegang wordt gevalideerd op noodzakelijkheid na teamwijzigingen.

## Privilege reviews
- Periodieke review van kritieke toegang (bijv. kwartaal).
- Focus op productie, deploy, database en beheerinterfaces.
- Uitkomsten worden vastgelegd met acties en deadlines.
- Niet-gerechtvaardigde rechten worden verwijderd.

## Key en token rotation
- Rotatiebeleid per type credential (API key, deploy key, token, wachtwoord).
- Kritieke credentials krijgen hogere rotatiefrequentie.
- Rotaties worden getest op functionaliteit en rollbackbaarheid.
- Oude credentials worden na succesvolle migratie direct ingetrokken.

## Overwogen alternatieven
### Optie A: Ad-hoc toegang zonder expliciet lifecyclebeleid
- Voordelen:
  - lage korte-termijnoverhead
  - snelle onboarding
- Nadelen:
  - hoog risico op privilege creep en vergeten accounts
  - lage auditability

### Optie B: Volledig enterprise IAM-programma direct invoeren
- Voordelen:
  - maximaal governancekader
  - uitgebreide compliance-ondersteuning
- Nadelen:
  - te zwaar voor huidige teamscope
  - hoge implementatie- en beheerkosten

### Optie C: Pragmatic access governance baseline (gekozen)
- Voordelen:
  - direct toepasbaar
  - substantiele risicoreductie zonder over-engineering
  - schaalbaar richting uitgebreidere IAM
- Nadelen:
  - vereist discipline in periodieke reviews
  - minder automatisering dan enterprise-oplossingen

## Gevolgen
### Positief
- Betere controle op wie toegang heeft tot kritieke systemen.
- Snellere risicoreductie door periodieke reviews en rotatie.
- Duidelijk eigenaarschap per account en credential.

### Negatief
- Extra operationele last voor review- en rotatiecycli.
- Meer procesafstemming nodig bij rol- en teamwijzigingen.

### Security impact
- Positief: minder kans op misbruik van verouderde of overmatige toegang.
- Restrisico: beleid blijft afhankelijk van consequente uitvoering.

### Test impact
- Key-rotaties vereisen validatie van kritieke integraties.
- Access-wijzigingen op kritieke paden vragen gerichte regressietests.

### Operationele impact
- Runbooks/checklists moeten provisioning, review en deprovisioning borgen.
- Incidentrespons profiteert van duidelijk eigenaarschap en snelle intrekking.

## Implementatieplan
1. Leg policy vast in ADR-overzicht en architectuurdocument.
2. Stel een access register op voor kritieke accounts/credentials.
3. Plan periodieke privilege review met vaste eigenaars.
4. Definieer en plan key-rotation ritme per credentialtype.
5. Integreer lifecycle checks in change/deploy processen.

## Rollback / migratie
- Policy is direct toepasbaar zonder schemawijzigingen.
- Invoering gebeurt incrementeel, startend bij productie-kritieke toegang.
- Aanscherpingen volgen via opvolgende ADR's.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
