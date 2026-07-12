# ADR Richtlijnen

In deze map leggen we belangrijke architectuurbeslissingen vast als Architecture Decision Records (ADR's).

Doel:
- Beslissingen traceerbaar maken
- Alternatieven en trade-offs expliciet documenteren
- Nieuwe teamleden sneller context geven

## Bestandsnaam conventie
Gebruik oplopende nummers met een korte titel:
- `0001-korte-titel.md`
- `0002-volgende-beslissing.md`

## Statuswaarden
- Proposed
- Accepted
- Superseded
- Rejected

## Werkwijze
1. Kopieer `0000-template.md` naar het volgende nummer.
2. Vul context, opties, beslissing en gevolgen in.
3. Laat de ADR reviewen in dezelfde PR als de codewijziging.
4. Markeer oude ADR's als `Superseded` wanneer ze vervangen worden.

## Minimale kwaliteitslat
- Beschrijf minstens 2 alternatieven.
- Benoem expliciet security impact.
- Benoem expliciet test impact.
- Benoem operationele impact (deploy/monitoring).

## ADR Overzicht
- 0001: Service-layer voor complexe controllerflows (`0001-service-layer-voor-complexe-controllerflows.md`)
- 0002: API-versiebeleid en breaking changes (`0002-api-versiebeleid-en-breaking-changes.md`)
- 0003: Deployment safety policy (`0003-deployment-safety-policy.md`)
- 0004: Security baseline policy (`0004-security-baseline-policy.md`)
- 0005: Test strategy governance (`0005-test-strategy-governance.md`)
- 0006: Observability policy (`0006-observability-policy.md`)
- 0007: Data governance policy (`0007-data-governance-policy.md`)
- 0008: Change management policy (`0008-change-management-policy.md`)
