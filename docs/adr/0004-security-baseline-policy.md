# ADR 0004: Security baseline policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: security, operations, backend, frontend

## Context
De codekwaliteit en deploymentflow zijn aangescherpt, maar een expliciete security-baseline ontbrak nog als overkoepelende afspraak. Daardoor kunnen teams security-adoptie verschillend interpreteren.

Belangrijkste risico's zonder baseline:
- Inconsistente secret handling
- Te ruime rechten in applicatie en infrastructuur
- Uitgestelde dependency-updates en kwetsbaarheden
- Onvoldoende duidelijke incidentrespons bij security events

## Beslissing
We hanteren een projectbrede security baseline policy met verplichte minimummaatregelen.

Beleidsregels:
- Secrets staan nooit in repository; alleen via omgevingsvariabelen of veilige secret stores.
- Least privilege is standaard voor accounts, tokens, services en deployment users.
- Dependencies worden periodiek geaudit en geüpdatet op basis van risico.
- Security-relevante wijzigingen krijgen expliciete review en testimpactcheck.
- Incident response light runbook is verplicht beschikbaar en actueel.

## Baseline controls
### Secret management
- Geen credentials/tokens in code, docs of voorbeeldbestanden met echte waarden.
- `.env`-achtige bestanden met secrets blijven genegeerd door git.
- Productie secrets worden alleen op target omgeving beheerd.

### Toegangsbeheer
- Gebruik alleen noodzakelijke rechten voor runtime en deploy.
- Admin-acties en gevoelige endpoints vereisen expliciete autorisatie.
- Service-accounts en keys worden periodiek herzien en geroteerd.

### Dependency hygiene
- Maandelijkse dependency review (backend + frontend).
- Kritieke security updates krijgen voorrang buiten reguliere releasecadans.
- CI/audit outputs worden onderdeel van releasebeslissing.

### Applicatie-hardening
- Inputvalidatie via FormRequests/typed contracts blijft verplicht.
- Output minimalisatie via API Resources (geen onnodige data exposure).
- Logging bevat geen secrets of gevoelige payloads.

### Incident response light
- Security issue triage binnen afgesproken responstijd.
- Tijdelijke mitigatie boven perfecte langetermijnfix bij acute risico's.
- Post-incident korte evaluatie en documentatie-update.

## Overwogen alternatieven
### Optie A: Geen expliciete baseline, alleen best effort
- Voordelen:
  - weinig procesoverhead
  - maximale ontwikkelsnelheid op korte termijn
- Nadelen:
  - inconsistent securityniveau
  - hogere kans op regressies en incidenten

### Optie B: Volledige enterprise security governance
- Voordelen:
  - zeer uitgebreid controlekader
  - sterke auditability
- Nadelen:
  - te zware overhead voor huidige teamscope
  - lagere uitvoerbaarheid op korte termijn

### Optie C: Pragmatische baseline policy (gekozen)
- Voordelen:
  - direct toepasbaar
  - duidelijke minimale lat zonder over-engineering
  - goede balans tussen veiligheid en snelheid
- Nadelen:
  - vereist discipline in periodieke opvolging
  - minder diepgaand dan enterprise frameworks

## Gevolgen
### Positief
- Consistente securityverwachting over backend, frontend en operations.
- Minder kans op menselijke fouten rond secrets en permissies.
- Snellere besluitvorming bij security events door vooraf gedefinieerd kader.

### Negatief
- Extra processtappen bij reviews en releases.
- Structurele onderhoudslast voor audits en runbooks.

### Security impact
- Positief: baseline reduceert kans op veelvoorkomende kwetsbaarheden.
- Restrisico: baseline vervangt geen periodieke diepte-audits of pentests.

### Test impact
- Security-impact moet expliciet meegenomen worden in teststrategie per change.
- Kritieke auth/permissie- en validatiepaden blijven prioritaire testdoelen.

### Operationele impact
- Deploy/releasechecklists moeten security gates blijven bevatten.
- Team moet eigenaarschap beleggen voor periodieke dependency- en access reviews.

## Implementatieplan
1. Leg baseline vast in ADR-overzicht en architectuurdocument.
2. Voeg security-checkpunten toe aan review- en releaseflow.
3. Plan periodieke dependency review en access review.
4. Voeg compact incident-response document toe en onderhoud dit.
5. Evalueer baseline elk kwartaal op incidenten en lessons learned.

## Rollback / migratie
- Deze policy vereist geen technische migratie; invoering is direct.
- Bij frictie kunnen controls gefaseerd ingevoerd worden, maar minimale secret- en least-privilege-eisen blijven hard.
- Aanpassingen worden via opvolgende ADR's vastgelegd.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
