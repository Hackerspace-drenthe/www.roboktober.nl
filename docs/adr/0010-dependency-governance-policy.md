# ADR 0010: Dependency governance policy

- Status: Accepted
- Datum: 2026-07-12
- Beslissers: Core maintainers
- Tags: dependencies, security, maintenance, release

## Context
Het project heeft meerdere policies voor architectuur, security, testing, observability, data en access governance. Een expliciete dependency governance policy ontbrak nog, terwijl third-party packages een directe impact hebben op security, stabiliteit en onderhoudbaarheid.

Belangrijkste risico's:
- Te late updates waardoor bekende kwetsbaarheden blijven bestaan
- Onvoorspelbare regressies bij grote sprong-updates
- Onduidelijke prioriteit bij vulnerability triage
- Geen vaste flow voor emergency patches

## Beslissing
We hanteren een pragmatische dependency governance policy met update cadence, vulnerability triage en emergency patch flow.

Beleidsregels:
- Dependencies worden periodiek beoordeeld en bijgewerkt volgens een vaste cadence.
- Vulnerabilities worden geclassificeerd op ernst en exploitability.
- Kritieke security issues krijgen een versnelde patchflow buiten reguliere releasecadans.
- Grote major updates worden gecontroleerd ingepland met extra validatie.
- Verouderde of ongebruikte dependencies worden periodiek opgeschoond.

## Update cadence
- Maandelijks: reguliere updatecyclus voor backend en frontend dependencies.
- Wekelijks/light-touch: review op nieuwe security advisories.
- Kwartaal: evaluatie van achterstanden, major upgrades en technische schuld.

## Vulnerability triage
Minimale triageflow:
1. Detectie via audit tools/CI/advisories.
2. Classificatie op severity en contextimpact.
3. Besluit: direct patchen, gepland patchen, of gemotiveerd accepteren met deadline.
4. Validatie via tests/static checks.
5. Documentatie van beslissing en follow-up.

Richtlijn:
- Kritiek/hoog met relevante exposure: versnelde afhandeling.
- Medium/laag: gepland in eerstvolgende onderhoudsrelease, tenzij contextrisico hoger is.

## Emergency patch flow
Trigger:
- Kritieke of actief misbruikte kwetsbaarheid met relevante impact.

Stappen:
1. Start incident/light change procedure met eigenaar.
2. Minimal patchset voorbereiden.
3. Versnelde validatie (tests + health checks + regressie op kritieke paden).
4. Uitrol via gecontroleerde deployflow met monitoring.
5. Post-release evaluatie en backlog voor structurele follow-up.

## Overwogen alternatieven
### Optie A: Alleen ad-hoc updates wanneer iemand tijd heeft
- Voordelen:
  - lage directe proceslast
- Nadelen:
  - hoge kans op security-achterstand
  - accumulerende upgrade-risico's

### Optie B: Altijd direct alle nieuwste versies toepassen
- Voordelen:
  - minimale version lag
- Nadelen:
  - verhoogde regressiekans en release-onrust
  - weinig risicodifferentiatie

### Optie C: Cadence + triage + emergency flow (gekozen)
- Voordelen:
  - balans tussen veiligheid en stabiliteit
  - voorspelbaar onderhoudsritme
  - duidelijke escalatieroute voor kritieke issues
- Nadelen:
  - vereist discipline en continue opvolging

## Gevolgen
### Positief
- Betere beheersing van security-risico's uit afhankelijkheden.
- Minder technische schuld door periodieke updates.
- Snellere responscapaciteit op kritieke advisories.

### Negatief
- Structurele tijdsinvestering voor review en onderhoud.
- Mogelijke extra testlast bij frequente updates.

### Security impact
- Positief: kortere blootstelling aan bekende kwetsbaarheden.
- Let op: supply chain risico blijft bestaan en vraagt blijvende aandacht.

### Test impact
- Dependency updates vereisen consistente regressievalidatie.
- Major updates krijgen extra testdiepte op kritieke functionaliteit.

### Operationele impact
- Releaseplanning bevat standaard dependency onderhoudsmomenten.
- Emergency patches vragen snelle communicatie en strakke uitvoering.

## Implementatieplan
1. Leg policy vast in ADR-overzicht en architectuurdocument.
2. Plan maandelijkse dependency update sessie.
3. Borg vulnerability triage in bestaande CI/review flow.
4. Definieer owner en communicatiepad voor emergency patches.
5. Evalueer kwartaalmatig op update achterstand en incidenten.

## Rollback / migratie
- Policy is direct toepasbaar zonder schemawijziging.
- Updates worden incrementeel uitgevoerd om risico te beperken.
- Bij regressie wordt teruggedraaid naar laatste stabiele dependencyset.

## Referenties
- Gerelateerd document: ARCHITECTUUR.md
- Gerelateerde documenten: DEPLOY-CHECKLIST.md, deploy/README.md
- Gerelateerde map: docs/adr
