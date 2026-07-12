# ADR 0008: Change management policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: release, change-management, deployment, communication

## Context
Het project heeft duidelijke richtlijnen voor architecture, security, testing, observability en data governance. Een expliciete change management policy ontbreekt nog, waardoor releasebeslissingen en communicatie per situatie kunnen verschillen.

Belangrijkste risico's:
- Onvoldoende consistente release criteria
- Te late of onduidelijke rollback-beslissingen
- Onvoldoende communicatie naar betrokkenen bij impactvolle changes
- Variatie in pre-release en post-release checks

## Beslissing
We hanteren een pragmatische change management policy met vaste releasecriteria, rollback-beslismomenten en communicatie-afspraken.

Beleidsregels:
- Elke release volgt een vaste checklist met quality gates en operationele checks.
- High-risk changes vereisen expliciete release-go/no-go beslissing.
- Rollback-beslismomenten zijn vooraf gedefinieerd per release.
- Stakeholders ontvangen consistente communicatie bij geplande en ongeplande changes.
- Post-release evaluatie is verplicht bij incidenten of rollback.

## Release criteria
Minimale criteria voor productie-release:
- CI is groen op relevante pipelines.
- Vereiste tests en static analysis zijn geslaagd.
- Deploy dry-run en health checks zijn geslaagd.
- Relevante documentatie/ADR/checklist is bijgewerkt.
- Verantwoordelijke operator/reviewer heeft go bevestigd.

High-risk criteria (extra controle):
- Breaking API impact, auth/permissie wijzigingen, datamigraties of infra-kritieke wijzigingen.
- Vereist expliciet akkoord van minimaal twee verantwoordelijken.

## Rollback-beslismomenten
Voor elke release worden vooraf deze momenten bepaald:
1. Direct na deploy (technische health check).
2. Kort na eerste functionele smoke test.
3. Tijdens vroege monitoringwindow op errors/latency.

Rollback triggers (voorbeeld):
- Kritieke endpoint failure
- Significant verhoogde error-rate
- Datacorruptie of auth-regressie
- Niet te mitigeren incident binnen afgesproken tijd

## Communicatieprotocol
Voor geplande releases:
- Vooraf: scope, risico, verwachte impact, onderhoudsvenster.
- Tijdens release: statusupdates bij start, belangrijke stap, afronding.
- Na release: bevestiging van resultaat, bekende issues, follow-up acties.

Voor incidenten/rollback:
- Snelle melding met impactindicatie.
- Regelmatige updates tot stabilisatie.
- Korte postmortem met actiepunten.

## Overwogen alternatieven
### Optie A: Informeel change management zonder vaste policy
- Voordelen:
  - minimale overhead
  - snelle uitvoering bij kleine wijzigingen
- Nadelen:
  - inconsistentie en hogere foutkans bij complexe releases
  - beperkte traceerbaarheid

### Optie B: Strikt enterprise CAB-proces voor elke wijziging
- Voordelen:
  - sterk governance-kader
  - hoge auditability
- Nadelen:
  - te zwaar en traag voor huidige teamscope
  - onnodige proceslast voor low-risk changes

### Optie C: Pragmatic staged policy met risicoafhankelijke controls (gekozen)
- Voordelen:
  - goede balans tussen snelheid en beheersing
  - duidelijke afspraken voor release en rollback
  - direct toepasbaar
- Nadelen:
  - vereist discipline in communicatie en evaluatie

## Gevolgen
### Positief
- Voorspelbaarder releaseproces met duidelijke go/no-go criteria.
- Snellere en duidelijkere besluitvorming bij incidenten.
- Betere samenwerking door vaste communicatieafspraken.

### Negatief
- Extra processtappen bij high-risk changes.
- Team moet actief release-documentatie onderhouden.

### Security impact
- Positief: minder kans dat risicovolle security-impact zonder expliciete review live gaat.
- Let op: change policy is aanvullend op security policy, geen vervanging.

### Test impact
- Testresultaten worden formeel onderdeel van releasebeslissing.
- Incidenten en rollbacks leiden tot gerichte regressietests als follow-up.

### Operationele impact
- Operators werken met een uniform ritme voor release, monitoring en rollback.
- Post-release evaluaties verbeteren continue proceskwaliteit.

## Implementatieplan
1. Leg policy vast in ADR-overzicht en architectuurdocument.
2. Veranker releasecriteria en rollbackmomenten in deployment checklist.
3. Definieer compact communicatieformat voor release-updates.
4. Label high-risk changes expliciet in PR/release notes.
5. Evalueer kwartaalmatig op incidenten en releasekwaliteit.

## Rollback / migratie
- Policy is direct toepasbaar zonder technische migratie.
- Invoering kan incrementeel: eerst high-risk releases, daarna volledige adoptie.
- Aanpassingen worden via opvolgende ADR's vastgelegd.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
